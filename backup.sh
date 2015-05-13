#!/bin/bash

#--------------------------
# Make backup dir
#--------------------------
rm backup -rf
rm backup.tar.gz -rf
mkdir backup
cd backup

#--------------------------
# Backup MySQL
#--------------------------
read -p "Database Name: " DBNAME
read -p "Database Password: " PASS
mysqldump -u root -p$PASS $DBNAME > db.sql

#--------------------------
# Backup config.php
#--------------------------
cd ..
cp config.php backup/config_backup.php

#--------------------------
# Create Archive
#--------------------------
tar -czvf backup.tar.gz backup/
rm backup -rf