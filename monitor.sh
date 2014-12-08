#!/bin/bash

# APACHE SECTION
RESTART="/etc/init.d/apache2 restart"
PGREP="/usr/bin/pgrep"
HTTPD="apache"
$PGREP ${HTTPD}
if [ $? -ne 0 ]
then
$RESTART
fi

# MYSQL SECTION
RESTARTM="/etc/init.d/mysql restart"
MYSQLD="mysqld"
$PGREP ${MYSQLD}
if [ $? -ne 0 ]
then
$RESTARTM
fi