#!/bin/bash

#################################
# AGC Script installation with nginx version
# wget https://bitbucket.org/oknoorap/augencont/raw/master/install-nginx.sh && chmod +x install-nginx.sh && ./install-nginx.sh domain password
# ##########

# For vultr
if [ $(hostname) != 'localhost' ]; then
hostname localhost
fi

# domain name
DOMAIN=${1-default}

# password
PASS=${2-dollar777}

# Update all
echo "Update all..."
sudo apt-get update -y > /dev/null

##############################
# install LEMP stack
#############################
# Install nginx
if [ $(dpkg-query -W -f='${Status}' nginx 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
  echo "Installing Nginx..."
  sudo apt-get install nginx -y > /dev/null
fi

###### Install MySQL Begin #####
if [ $(dpkg-query -W -f='${Status}' mysql-server 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
  echo "Installing MySQL Server..."
  DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-server > /dev/null
  sudo mysql_install_db
  sudo mysqladmin -u root password $PASS
  
  if [ $(dpkg-query -W -f='${Status}' expect 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
    sudo apt-get install expect -y > /dev/null
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

  echo "${SECURE_MYSQL}"
  sudo apt-get -y remove expect --purge
  mysql -u root -p$PASS -e "create database agc; GRANT ALL PRIVILEGES ON agc.* TO root@localhost IDENTIFIED BY '$PASS'"
fi
############## END ###############

# Install php
sudo apt-get install php5 php5-mysql php5-json php5-mcrypt php5-fpm -y > /dev/null

# Install phpMyAdmin
#sudo apt-get install phpmyadmin -y > /dev/null
#sudo ln -s /usr/share/phpmyadmin /usr/share/nginx/html
#sudo php5enmod mcrypt
#sudo service php5-fpm restart