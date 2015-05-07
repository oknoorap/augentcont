#!/bin/bash

#==================================================
# AGC Script installation with nginx version
# wget -q https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password
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

# Domain alias www
ISSUBDOMAIN='n'
WWWDOMAIN="www.$DOMAIN"

# Parse Password
# - DB password for new installation
# - Engine password for new domain
PASS=${2-dollar777}
if [[ $PASS == '' ]]; then
	echo "Password is blank"
	exit 1
fi

# Database password
DBPASS=''

# Select installation option
echo -e "=========================================="
echo -e "| Setup option:"
echo -e "| 1 - New Install, New server"
echo -e "| 2 - Add domain"
echo -e "| 3 - Clone"
echo -e "=========================================="
read -p "Your option: " OPTION

# function to check yes / no
yesorno () {
	while true; do
		read -p "$1 " yn
		case $yn in
			[Yy]* ) return 0;;
			[Nn]* ) return 1;;
			* ) echo "Please answer yes or no.";;
		esac
	done
}

# Check dbname for new domain
if [[ $OPTION == '2' ]]; then
	read -p "Database Name (no special chars): " DBNAME
	if [[ $DBNAME == '' ]]; then
		echo "Database name not provided"
		exit 1
	fi

	# add prefix www to addon domain
	if yesorno "Is this subdomain [y/n]"; then
		ISSUBDOMAIN='y'
		WWWDOMAIN="$DOMAIN"
	fi
elif [[ $OPTION == '3' ]]; then
	# add prefix www to addon domain
	if yesorno "Is this subdomain [y/n]"; then
		ISSUBDOMAIN='y'
		WWWDOMAIN="$DOMAIN"
	fi
fi

# Make directory
if [[ $OPTION == '3' ]]; then
	echo -e "Please select directory to clone"
	echo $(ls /web)
	read -p "Choose Dir: " CLONEDIR
	sudo cp -R /web/${CLONEDIR} /web/${DOMAIN}
	cd /web/${DOMAIN}
else
	sudo mkdir -p /web/${DOMAIN}
	cd /web/${DOMAIN}
fi

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
	PMAPASS=$(openssl passwd -crypt $PASS)
	echo "agc:$PMAPASS">/etc/nginx/pma_pass

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

# Clone engine from repo
if [[ $OPTION != '3' ]]; then
	git clone https://oknoorap@bitbucket.org/oknoorap/augencont.git >/dev/null
	mv augencont/* ./
	rm augencont -rf
fi

# change config.php password
sed -i "s/\"password\":\"sukses999\"/\"password\":\"${PASS}\"/g" config.php

# New installation
if [[ $OPTION == '1' ]]; then
	# change db password
	sed -i "s/\"database.password\":\"sukses999\"/\"database.password\":\"${PASS}\"/g" config.php

	# Create config /etc/mmengine.conf
	echo "dbpass=$PASS">/etc/mmengine.conf

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
	sudo mysql -u root -p${DBPASS} -e "CREATE DATABASE \`$DBNAME\`; GRANT ALL PRIVILEGES ON \`$DBNAME\`.* TO root@localhost IDENTIFIED BY '$DBPASS';"

	sed -i "s/\"database.password\":\"sukses999\"/\"database.password\":\"${DBPASS}\"/g" config.php
	sed -i "s/\"database.name\":\"agc\"/\"database.name\":\"${DBNAME}\"/g" config.php

# Clone from directory
elif [[ $OPTION == '3' ]]; then
echo "Continue..."
fi


# Add redirect script for domain
if [[ $ISSUBDOMAIN == 'y' ]]; then
	NGINXCONFREDIRECT=''
else
	NGINXCONFREDIRECT="server {
	listen 80;
	server_name ${DOMAIN};
	return 301 http://${WWWDOMAIN}\$request_uri;
}"
fi

# Write nginx config
CACHEKEY=${DOMAIN/./}
cat << NGINXCONF > /etc/nginx/sites-available/${DOMAIN}
fastcgi_cache_path /etc/nginx/cache levels=1:2 keys_zone=${CACHEKEY}:100m inactive=10d;
fastcgi_cache_key "\$scheme\$request_method\$host\$request_uri";

${NGINXCONFREDIRECT}

server {
	listen 80;
	root /web/${DOMAIN};

	#logs
	access_log /var/log/nginx/${DOMAIN}.access.log;
	error_log /var/log/nginx/${DOMAIN}.error.log;

	index index.php index.html index.html;
	server_name ${WWWDOMAIN};

	# deny all htaccess
	location ~ /\\.ht {
		deny all;
	}

	# set expiration for assets
	location ~* \\.(js|css|png|jpg|jpeg|gif|ico|eot|woff|ttf|svg)\$ {
		expires max;
		access_log off;
		log_not_found off;
	}

	# disable favicon log
	location ~* favicon.ico\$ {
		log_not_found off;
		access_log off;
	}

	# enable robots.txt
	location = /robots.txt {
		allow all;
		log_not_found off;
		access_log off;
	}

	# enable sitemap xsl
	location = /sitemap.xsl {
		add_header Content-Type "text/xsl";
		allow all;
		log_not_found off;
		access_log off;
	}

	set \$no_cache 0;

	if (\$request_method = POST)
	{
		set \$no_cache 1;
	}

	if (\$request_uri ~* "/admin/|sitemap(_index)?")
	{
		set \$no_cache 1;
	}

	# homepage
	location / {
		rewrite "^/sitemap([0-9]{0,3})?\.xml(\.gz)?$" /sitemap.php?offset=\$1&format=\$2 last;
		rewrite ^(.*)\$ /index.php?\$1 last;
	}

	# admin
	location /admin/  {
		alias /web/${DOMAIN}/admin/;
		set \$no_cache 1;
	}

	location ~ \\.php$ {
		try_files \$uri =404;
		fastcgi_split_path_info ^(.+\\.php)(/.+)\$;
		fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
		include fastcgi_params;
		fastcgi_cache ${CACHEKEY};
		fastcgi_cache_valid 200 10d;
		fastcgi_cache_bypass \$no_cache;
		fastcgi_no_cache \$no_cache;
	}
}
NGINXCONF

# enable site
sudo ln -s /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/${DOMAIN}

# add host
cat << ADDHOST >> /etc/hosts
127.0.0.1 ${DOMAIN}
ADDHOST

#==================================================
# Fix Permission
#==================================================
sudo chown -R www-data:www-data $(pwd)
sudo find $(pwd) -type d -exec chmod 755 {} \;
sudo find $(pwd) -type f -exec chmod 644 {} \;
sudo usermod -aG ftpgroup www-data
sudo chown -R ftpuser:ftpgroup $(pwd)
sudo chmod -R g+ws $(pwd)

#==================================================
# Restart Al System
#==================================================
# Restart fast-cgi
sudo service php5-fpm restart >/dev/null

# Restart nginx
sudo service nginx restart >/dev/null

# Restart FTP
sudo /etc/init.d/pure-ftpd restart >/dev/null


#==================================================
# Finishing Installation
#==================================================
if [[ $OPTION != '3' ]]; then
	if yesorno "NEW installation [y/n]?"; then
		echo "Import MySQL database"
		if [[ $OPTION == '1' ]]; then
			sudo mysql -u root -p$PASS agc < db.sql
		elif [[ $OPTION == '2' ]]; then
			sudo mysql -u root -p$DBPASS $DBNAME < db.sql
		fi
		echo -e "========================================="
		echo -e "Success. Please insert keyword."
		echo -e "========================================="
		echo -e ""
		echo -e ""
	else
		echo "Enter website's source (include http:// without /) : "
		read WEBSITE
		wget -q $WEBSITE/backup.tar.gz
		echo "Extract Zip"
		tar -zxvf backup.tar.gz >/dev/null
		echo "Import MySQL database"
		if [[ $OPTION == '1' ]]; then
			mysql -u root -p$PASS agc < backup/db.sql
		elif [[ $OPTION == '2' ]]; then
			mysql -u root -p$DBPASS $DBNAME < backup/db.sql
		fi
		sudo mv backup/config_backup.php config.php
		sudo rm backup.tar.gz -rf
		sudo rm backup -rf
	fi
fi

if [[ $OPTION == '1' ]]; then
	echo -e "========================================="
	echo -e "# FTP user:agc, pass:$PASS"
	echo -e "# phpMyAdmin Auth user:agc pass:$PASS"
	echo -e "# phpMyAdmin SQL user:root pass:$PASS"
	echo -e "========================================="
elif [[ $OPTION == '2' ]]; then
	echo -e "========================================="
	echo -e "# FTP user:agc, pass:$DBPASS"
	echo -e "# phpMyAdmin Auth user:agc pass:$DBPASS"
	echo -e "# phpMyAdmin SQL user:root pass:$DBPASS"
	echo -e "========================================="
fi

#==================================================
# Remove unnecessary files
#==================================================
sudo rm db.sql -rf
sudo rm install.sh -rf
sudo rm install-nginx.sh -rf
sudo rm monitor.sh -rf
sudo rm new.sh -rf
sudo rm lamp.sh -rf
sudo rm update.sh -rf
sudo chmod +x backup.sh
sudo chmod +x update-nginx.sh
sudo chmod +x cc.sh