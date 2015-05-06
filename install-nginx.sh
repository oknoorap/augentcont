#!/bin/bash

#==================================================
# AGC Script installation with nginx version

# New installation:
# wget -q https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password

# Add Domain:
# wget -q https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password dbname
#==================================================

# remove host.vultr (only for vultr.com or others)
# that use hostname other than localhost
if [[ $(hostname) != 'localhost' ]]; then
	hostname localhost
fi

# Update all
echo "Update aptitude..."
sudo apt-get update -y >/dev/null

# Parse Domain name
DOMAIN=${1-default}
if [[ $DOMAIN == '' ]]; then
	echo "What is your domain"
	exit 1
fi

# Parse Password
# - DB password for new installation
# - Engine password for new domain
PASS=${2-dollar777}
if [[ $PASS == '' ]]; then
	echo "Password is blank"
	exit 1
fi

# Database variables
DBNAME=${3-}
DBPASS=''

# Select installation option
echo -e "=========================================="
echo -e "| Setup option:"
echo -e "| 1 - New Install, New server"
echo -e "| 2 - Add domain"
echo -e "| 3 - Clone"
echo -e "=========================================="
read -p "Your option: " OPTION


# Make directory
sudo mkdir -p /web/${DOMAIN}
cd /web/${DOMAIN}

#==================================================
# Install LEMP stack
#==================================================
# Install nginx
if [ $(dpkg-query -W -f='${Status}' nginx 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	echo "Installing Nginx..."
	sudo apt-get install nginx -y >/dev/null
fi

# Install MySQL
if [ $(dpkg-query -W -f='${Status}' mysql-server 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	echo "Installing MySQL Server..."
	DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-server >/dev/null
	sudo mysql_install_db >/dev/null
	sudo mysqladmin -u root password $PASS
	
	if [ $(dpkg-query -W -f='${Status}' expect 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
		sudo apt-get install expect -y >/dev/null
	fi

	SECURE_MYSQL=$(expect -c "
set timeout 10
spawn mysql_secure_installation

expect \"Enter current password for root (enter for none):\"
send \"$PASS\r\"

expect \"Change the root password?\"
send \"n\r\"

expect \"Remove anonymous users?\"
send \"y\r\"

expect \"Disallow root login remotely?\"
send \"y\r\"

expect \"Remove test database and access to it?\"
send \"y\r\"

expect \"Reload privilege tables now?\"
send \"y\r\"

expect eof
")

	echo "${SECURE_MYSQL}" >/dev/null
	sudo apt-get -y remove expect --purge >/dev/null
	mysql -u root -p$PASS -e "create database agc; GRANT ALL PRIVILEGES ON agc.* TO root@localhost IDENTIFIED BY '$PASS'"
fi

# Install php
if [ $(dpkg-query -W -f='${Status}' php5 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	sudo apt-get install php5-mysql -y >/dev/null
	sudo apt-get install php5 php5-curl php5-json php5-mcrypt -y >/dev/null
	sudo apt-get install php5-fpm -y >/dev/null
fi

# Install phpMyAdmin
if [ $(dpkg-query -W -f='${Status}' phpmyadmin 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	sudo apt-get install phpmyadmin -y

	# Restart 
	sudo ln -s /usr/share/phpmyadmin /usr/share/nginx/html/phpmyadmin
	sudo php5enmod mcrypt >/dev/null
	sudo service php5-fpm restart >/dev/null

	# Password for phpmyadmin
	PMAPASS=$(openssl passwd -crypt $PASS>/dev/null)
	echo "$DOMAIN:$PMAPASS">/etc/nginx/pma_pass

	# Add phpMyAdmin to default nginx conf
	PMAORIGIN="\#error_page 404 \/404.html;"
	PMAREPLACEMENT="\#error_page 404 \/404\.html;\n\tlocation \/phpmyadmin \{\n \t\troot \/usr\/share\/;\n \t\tauth_basic \"Admin Login\";\n \t\tauth_basic_user_file \/etc\/nginx\/pma_pass;\n \t\tindex index\.php index\.html index\.htm;\n \t\tlocation ~ ^\/phpmyadmin\/(.+\\.php)\$ \{\n \t\t\ttry_files \$uri =404;\n \t\t\troot \/usr\/share\/;\n \t\t\tfastcgi_pass unix:\/var\/run\/php5-fpm.sock;\n \t\t\tfastcgi_index index\.php;\n \t\t\tfastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n \t\t\tinclude fastcgi_params;\n \t\t}\n \t\tlocation ~* ^\/phpmyadmin\/(.+\\\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))\$ {\n \t\t\troot \/usr\/share\/;\n \t\t}\n \t\}\n \n \tlocation \/phpMyAdmin \{\n \t\trewrite ^\/* \/phpmyadmin last;\n \t\}\n\n"
	sudo sed -i "s/$PMAORIGIN/$PMAREPLACEMENT/g" /etc/nginx/sites-available/default
fi

#==================================================
# End of LEMP Installation
#==================================================

# Install Tor
if [ $(dpkg-query -W -f='${Status}' tor 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	echo "Installing Tor..."
	sudo apt-get install tor -y >/dev/null
fi

# Install Git
if [ $(dpkg-query -W -f='${Status}' git 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	echo "Installing Git..."
	sudo apt-get install git-core -y >/dev/null
fi

# Install FTP
if [ $(dpkg-query -W -f='${Status}' pure-ftpd 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
	echo "Installing FTP..."
	sudo apt-get install pure-ftpd pureadmin -y >/dev/null
	sudo groupadd ftpgroup
	sudo useradd -g ftpgroup -d /dev/null -s /etc ftpuser
	sudo pure-pw useradd agc -u ftpuser -d /web
	sudo pure-pw mkdb
	sudo ln -s /etc/pure-ftpd/pureftpd.passwd /etc/pureftpd.passwd
	sudo ln -s /etc/pure-ftpd/pureftpd.pdb /etc/pureftpd.pdb
	sudo ln -s /etc/pure-ftpd/conf/PureDB /etc/pure-ftpd/auth/PureDB
	sudo chown -hR ftpuser:ftpgroup /web
fi

# Check dbname for new domain
if [[ $OPTION == '2' ]]; then
	if [[ $DBNAME == '' ]]; then
		echo "Database name not provided"
		exit 1
	fi
fi

# Clone engine from repo
git clone https://oknoorap@bitbucket.org/oknoorap/augencont.git >/dev/null
mv augencont/* ./
rm augencont -rf

# change config.php password
sed -i "s/\"password\":\"sukses999\"/\"password\":\"${PASS}\"/g" config.php

# New installation
if [[ $OPTION == '1' ]]; then
	# Create config /etc/mmengine.conf
	echo "dbpass=$PASS\n">/etc/mmengine.conf

# Install new domain/subdomain
elif [[ $OPTION == '2' ]]; then
	# Read config from last installation
	# config location at /etc/mmengine.conf
	while read -r line
	do
		# Split '=' from config
		arr=(${line//=/ })
		
		# Get Database password
		if [[ $line == *"dbpass"* ]]; then
			DBPASS=${arr[1]}
		fi
	done < "/etc/mmengine.conf"

	# Create and Change database
	mysql -u root -p$DBPASS -e "create database $DBNAME; GRANT ALL PRIVILEGES ON $DBNAME.* TO root@localhost IDENTIFIED BY '$DBPASS'"

	sed -i "s/\"database.password\":\"sukses999\"/\"database.password\":\"${DBPASS}\"/g" config.php
	sed -i "s/\"database.name\":\"agc\"/\"database.name\":\"${DBNAME}\"/g" config.php

# Clone from directory
elif [[ $OPTION == '3' ]]; then
echo "Option 3"
fi

#==================================================
# Fix Permission
#==================================================
sudo chown -R www-data:www-data /web/
sudo find /web/ -type d -exec chmod 755 {} \;
sudo find /web/ -type f -exec chmod 644 {} \;
sudo usermod -aG ftpgroup www-data
sudo chown -R ftpuser:ftpgroup /web/
sudo chmod -R g+ws /web/

#==================================================
# Restart Al System
#==================================================
# Restart nginx
sudo service nginx restart >/dev/null

# Restart FTP
sudo /etc/init.d/pure-ftpd restart >/dev/null