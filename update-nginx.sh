#!/bin/bash

#--------------------------
# Clone website source
#--------------------------
git clone https://oknoorap@bitbucket.org/oknoorap/augencont.git

#--------------------------
# Remove all dirs on root
#--------------------------
sudo rm augencont/config.php -rf
sudo rm augencont/db.sql -rf
sudo rm augencont/install.sh -rf
sudo rm augencont/install-nginx.sh -rf
sudo rm augencont/monitor.sh -rf
sudo rm augencont/new.sh -rf
sudo rm augencont/lamp.sh -rf
sudo rm augencont/update.sh -rf

#--------------------------
# Remove autogent dir
#--------------------------
sudo rm admin -rf
sudo rm includes -rf
sudo rm content -rf
sudo mv augencont/* ./
sudo rm augencont -rf

#--------------------------
# Fix Permission
#--------------------------
find $(pwd) -type d -exec chmod 755 {} \;
find $(pwd) -type f -exec chmod 644 {} \;
usermod -aG ftpgroup www-data
chown -R ftpuser:ftpgroup $(pwd)
sudo chmod -R g+ws $(pwd)
sudo chmod +x backup.sh
sudo chmod +x update-nginx.sh
sudo chmod +x cc.sh