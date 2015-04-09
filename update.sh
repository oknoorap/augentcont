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
# nano .htaccess
#--------------------------
cat << EOFTEST1 > ./.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteRule ^sitemap([0-9]{0,3})?\.xml(\.gz)?$ sitemap.php?offset=$1&format=$2 [QSA,L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?\$1 [L,QSA]
</IfModule>
EOFTEST1
sudo service apache2 restart

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