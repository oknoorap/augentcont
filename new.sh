# Add new domain:
# wget https://raw.githubusercontent.com/oknoorap/augentcont/master/new.sh && chmod +x new.sh && ./new.sh password domain.com dbname

# Set Default password
kunci=${1-sukses999}

#domain folder
folder=${2-html}

#dbname
dbname=${3-agc}

mkdir /var/www/$folder
cd /var/www/$folder

#--------------------------
# add mysql database
#--------------------------
echo "MySQL root's password: "
read mysqlpwd
mysql -u root -p$mysqlpwd -e "create database ${dbname}; GRANT ALL PRIVILEGES ON ${dbname}.* TO root@localhost IDENTIFIED BY '${mysqlpwd}'"

#--------------------------
# Install mod-rewrite
#--------------------------
echo "Mod Rewrite"
sudo a2enmod rewrite

cat << EOFTEST1 > /etc/apache2/sites-available/${folder}.conf
<VirtualHost *:80>
    # server for ${folder}
    ServerName ${folder}
    DocumentRoot /var/www/${folder}
    
    <Directory /var/www/${folder}>
      Options Indexes FollowSymLinks MultiViews
      AllowOverride All
      Order allow,deny
      allow from all
    </Directory>
</VirtualHost>
EOFTEST1

#--------------------------
# nano .htaccess
#--------------------------
a2ensite $folder
sudo service apache2 restart

#--------------------------
# Clone website source
#--------------------------
git clone https://oknoorap@bitbucket.org/oknoorap/augencont.git
mv augencont/* ./
rm augencont -rf

#--------------------------
# nano .htaccess
#--------------------------
cat << EOFTEST1 > ./.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteRule ^sitemap([0-9]{0,3})?\.xml(\.gz)?\$ sitemap.php?offset=\$1&format=\$2 [QSA,L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)\$ index.php?\$1 [L,QSA]
</IfModule>
EOFTEST1

#--------------------------
# Fix Permission
#--------------------------
chown -R www-data:www-data /var/www/
find /var/www/ -type d -exec chmod 755 {} \;
find /var/www/ -type f -exec chmod 644 {} \;
usermod -aG ftpgroup www-data
chown -R ftpuser:ftpgroup /var/www/
chmod -R g+ws /var/www/
chmod +x backup.sh
chmod +x update.sh

sudo service apache2 restart
service pure-ftpd restart

#--------------------------
# Finishing Installation
#--------------------------
promptyn () {
    while true; do
        read -p "$1 " yn
        case $yn in
            [Yy]* ) return 0;;
            [Nn]* ) return 1;;
            * ) echo "Please answer yes or no.";;
        esac
    done
}

if promptyn "NEW installation [y/n]?"; then
    echo "Import MySQL database"
    mysql -u root -p$mysqlpwd $dbname < db.sql
    echo "Success. Please insert keyword."
else
    echo "Enter website's source (include http:// without /) : "
    read website
    wget $website/backup.tar.gz
    echo "Extract Zip"
    tar -zxvf backup.tar.gz
    echo "Import MySQL database"
    mysql -u root -p$mysqlpwd $dbname < backup/db.sql
    mv backup/config_backup.php config.php
    rm backup.tar.gz -rf
    rm backup -rf
fi

# change config.php password
sed -i "s/sukses999/${kunci}/g" config.php
sed -i "s/\"database.name\":\"agc\"/\"database.name\":\"${dbname}\"/g" config.php

rm db.sql -rf
rm monitor.sh -rf
rm install.sh -rf
rm new.sh -rf
rm lamp.sh -rf