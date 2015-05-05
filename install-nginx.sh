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
sudo cd / && sudo mkdir web && sudo mkdir /web/${DOMAIN}

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
    sudo apt-get install php5 php5-mysql php5-json php5-mcrypt php5-fpm -y >/dev/null
  fi

  # Install phpMyAdmin
  if [ $(dpkg-query -W -f='${Status}' php5 2>/dev/null | grep -c "ok installed") -eq 0 ]; then
    sudo apt-get install phpmyadmin -y >/dev/null
    sudo ln -s /usr/share/phpmyadmin /usr/share/nginx/html
    sudo php5enmod mcrypt
    sudo service php5-fpm restart
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