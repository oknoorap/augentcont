# wget https://bitbucket.org/oknoorap/augencont/raw/master/lamp.sh && chmod +x lamp.sh && ./lamp.sh
sudo apt-get update && sudo apt-get install apache2 mysql-server libapache2-mod-auth-mysql php5-mysql -y && sudo mysql_install_db && sudo /usr/bin/mysql_secure_installation && sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt -y && sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt -y

sed -i "s/DirectoryIndex/DirectoryIndex index.php/g" /etc/apache2/mods-enabled/dir.conf