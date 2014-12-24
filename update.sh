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

#--------------------------
# Remove autogent dir
#--------------------------
mv augencont/* ./
rm augencont -rf