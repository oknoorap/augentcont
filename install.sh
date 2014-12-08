#!/bin/bash

# wget  
#
#

#--------------------------
# Update apt-get repository
#--------------------------
sudo apt-get update

#--------------------------
# Install git
#--------------------------
sudo apt-get install git-core -y


#--------------------------
# Install phpMyAdmin
#--------------------------
sudo apt-get install phpmyadmin -y
sudo php5enmod mcrypt
sudo service apache2 restart

cat << EOFTEST1 > /etc/apache2/conf-available/phpmyadmin.conf
# phpMyAdmin default Apache configuration

Alias /phpmyadmin /usr/share/phpmyadmin

<Directory /usr/share/phpmyadmin>
        Options FollowSymLinks
        DirectoryIndex index.php
        AllowOverride All

        <IfModule mod_php5.c>
                AddType application/x-httpd-php .php

                php_flag magic_quotes_gpc Off
                php_flag track_vars On
                php_flag register_globals Off
                php_admin_flag allow_url_fopen Off
                php_value include_path .
                php_admin_value upload_tmp_dir /var/lib/phpmyadmin/tmp
                php_admin_value open_basedir /usr/share/phpmyadmin/:/etc/phpmyadmin/:/var/lib/phpmyadmin/:/usr/share/php/php-gettext/:/usr/share/javascript/
        </IfModule>

</Directory>

# Authorize for setup
<Directory /usr/share/phpmyadmin/setup>
    <IfModule mod_authn_file.c>
    AuthType Basic
    AuthName "phpMyAdmin Setup"
    AuthUserFile /etc/phpmyadmin/htpasswd.setup
    </IfModule>
    Require valid-user
</Directory>

# Disallow web access to directories that don't need it
<Directory /usr/share/phpmyadmin/libraries>
    Order Deny,Allow
    Deny from All
</Directory>
<Directory /usr/share/phpmyadmin/setup/lib>
    Order Deny,Allow
    Deny from All
</Directory>
EOFTEST1

sudo service apache2 restart

cat << EOFTEST1 > /usr/share/phpmyadmin/.htaccess
AuthType Basic
AuthName "Restricted Files"
AuthUserFile /etc/phpmyadmin/.htpasswd
Require valid-user
EOFTEST1

sudo apt-get install apache2-utils -y
sudo htpasswd -c /etc/phpmyadmin/.htpasswd agc


#--------------------------
# Install mod-rewrite
#--------------------------
sudo a2enmod rewrite
sudo service apache2 restart

cat << EOFTEST1 >> /etc/apache2/sites-available/000-default.conf
<Directory /var/www/html>
Options Indexes FollowSymLinks MultiViews
AllowOverride All
Order allow,deny
allow from all
</Directory>
EOFTEST1


#--------------------------
# Install Tor
#--------------------------
sudo apt-get install tor -y

#--------------------------
# Install FTP
#--------------------------


#--------------------------
# Add monitor.sh to Crontab
#--------------------------
mv monitor.sh /home/monitor.sh
crontab -l | { cat; echo "* * * * * sh -x /home/monitor.sh"; } | crontab -


#--------------------------
# Finishing Installation
#--------------------------
rm monitor.sh -rf
rm install.sh -rf