#!/bin/bash

#--------------------------
# Make backup dir
#--------------------------
rm backup -rf
mkdir backup
cd backup

#--------------------------
# Backup MySQL
#--------------------------
mysqldump -u root -psukses999 agc > db.sql

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