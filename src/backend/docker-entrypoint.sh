#!/bin/bash

# Starting services
# ./phpmyadmin.sh
service apache2 start
service mysql start

# Creating an admin user
mysql -e "CREATE USER 'admin'@'%' IDENTIFIED BY 'admin123';"
mysql -e "CREATE DATABASE devices;"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"

tail -f /dev/null
