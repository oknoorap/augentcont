#!/bin/bash

#--------------------------
# Clone website source
#--------------------------
git clone https://oknoorap@bitbucket.org/oknoorap/augencont.git

#--------------------------
# Remove all dirs on root
#--------------------------
rm augencont/config.php -rf
rm augencont/db.sql -rf
rm augencont/install.sh -rf
rm augencont/monitor.sh -rf
rm augencont/new.sh -rf

#--------------------------
# Remove autogent dir
#--------------------------
rm admin -rf
rm includes -rf
rm content -rf
mv augencont/* ./
rm augencont -rf

#--------------------------
# Fix Permission
#--------------------------
find /var/www/ -type d -exec chmod 755 {} \;
find /var/www/ -type f -exec chmod 644 {} \;
usermod -aG ftpgroup www-data
chown -R ftpuser:ftpgroup /var/www/
chmod -R g+ws /var/www/
chmod +x backup.sh
chmod +x update.sh
service apache2 restart
service pure-ftpd restart