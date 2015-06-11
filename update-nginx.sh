#!/bin/bash

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
if yesorno "Delete /content [y/n]"; then
	sudo rm content -rf
else
	sudo rm augencont/content -rf
fi
sudo mv augencont/* ./ >/dev/null
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
DOMAIN=$(pwd | xargs basename)
sudo sed -i "s/DOMAIN/${DOMAIN}/g" cc.sh