#!/bin/bash

#################################
# AGC Script installation with nginx version
# New install:
# wget -q https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password
# 
# Add Domain:
# wget -q https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password dbname
# ##########

# For vultr
if [[ $(hostname) != 'localhost' ]]; then
  hostname localhost
fi

# Update all
echo "Update all..."
sudo apt-get update -y >/dev/null

# Domain name
DOMAIN=${1-default}
if [[ $DOMAIN == '' ]]; then
  echo "What is your domain"
  exit 1
fi

# DB password for new installation
# Engine password for new domain
PASS=${2-dollar777}
if [[ $PASS == '' ]]; then
  echo "Password is blank"
  exit 1
fi

# Database variable
DBNAME=${3-}
DBROOT=''
DBPASS=''

# Select installation option
echo -e "--------------------------"
echo -e "| Select installation:"
echo -e "| 1 - New Install, New server"
echo -e "| 2 - Add domain"
echo -e "--------------------------"
echo "Your Option: "
read OPTION

# Make directory
sudo mkdir -p /web/${DOMAIN}
cd /web/${DOMAIN}

# specify password, etc
if [[ $OPTION == '1' ]]; then
  ############# Install LEMP stack #################
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
    sudo apt-get install php5 php5-json php5-mcrypt -y >/dev/null
    sudo apt-get install php5-fpm -y >/dev/null
  fi

  # Install phpMyAdmin
  if [ $(dpkg-query -W -f='${Status}' phpmyadmin 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
    sudo apt-get install phpmyadmin -y
    sudo ln -s /usr/share/phpmyadmin /usr/share/nginx/html/phpmyadmin
    
    # Password for phpmyadmin
    PMAPASS=openssl passwd -crypt $PASS
    echo '${DOMAIN}:${PMAPASS}'>/etc/nginx/pma_pass
  
    # Add phpMyAdmin to default nginx conf
    PMAORIGIN="\#error_page 404 \/404.html;"
    PMAREPLACEMENT="\#error_page 404 \/404\.html;\n\tlocation \/phpmyadmin \{\n \t\troot \/usr\/share\/;\n \t\tauth_basic \"Admin Login\";\n \t\tauth_basic_user_file \/etc\/nginx\/pma_pass;\n \t\tindex index\.php index\.html index\.htm;\n \t\tlocation ~ ^\/phpmyadmin\/(.+\\.php)\$ \{\n \t\t\ttry_files \$uri =404;\n \t\t\troot \/usr\/share\/;\n \t\t\tfastcgi_pass unix:\/var\/run\/php5-fpm.sock;\n \t\t\tfastcgi_index index\.php;\n \t\t\tfastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n \t\t\tinclude fastcgi_params;\n \t\t}\n \t\tlocation ~* ^\/phpmyadmin\/(.+\\\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))\$ {\n \t\t\troot \/usr\/share\/;\n \t\t}\n \t\}\n \n \tlocation \/phpMyAdmin \{\n \t\trewrite ^\/* \/phpmyadmin last;\n \t\}\n\n"
    sudo sed -i "s/$PMAORIGIN/$PMAREPLACEMENT/g" /etc/nginx/sites-available/default

    # Restart 
    sudo php5enmod mcrypt >/dev/null
    sudo service php5-fpm restart >/dev/null
    sudo service nginx restart >/dev/null
  fi
  ############## END INSTALL LEMP ###############
else
  if [[ $DBNAME == '' ]]; then
    exit 1
  fi
  
  while read -r line
  do
    # Split '=' from config
    arr=(${line//=/ })
    
    # Database root user
    if [[ $line == *"dbroot"* ]]; then
      DBROOT=${arr[1]}
    fi
    
    # Database root user
    if [[ $line == *"dbpass"* ]]; then
      DBPASS=${arr[1]}
    fi
  done < "/etc/mmengine.conf"
fi