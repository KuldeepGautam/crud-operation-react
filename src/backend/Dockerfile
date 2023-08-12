FROM ubuntu:20.04

# Installing packages
RUN apt update -y
RUN apt install tzdata -y
RUN apt install apache2 -y
RUN apt install php libapache2-mod-php php-mysql php-curl php-cgi -y
RUN apt install mysql-server -y

# Installing phpmyadmin and configure it with apache
# DEBIAN_FRONTEND=noninteractive is used to prevent package prompts
RUN DEBIAN_FRONTEND=noninteractive apt install phpmyadmin php-mbstring php-zip php-gd php-json -y
RUN echo "Include /etc/phpmyadmin/apache.conf" >> /etc/apache2/apache2.conf

# Optional
RUN apt install nano -y

RUN chmod -R 777 /var/www/html

# Overriding apache config
RUN a2enmod rewrite
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Replace the bind-address value with 0.0.0.0
RUN sed -i 's/bind-address.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf

# Update the config-db.php file
RUN sed -i "s/\$dbuser='.*';/\$dbuser='';/" /etc/phpmyadmin/config-db.php
RUN sed -i "s/\$dbpass='.*';/\$dbpass='';/" /etc/phpmyadmin/config-db.php
RUN sed -i "s/\$dbserver='.*';/\$dbserver='127.0.0.1';/" /etc/phpmyadmin/config-db.php

WORKDIR /app
COPY ./docker-entrypoint.sh ./

WORKDIR /var/www/html
COPY . . 

RUN ["chmod", "+x", "-R", "/app/docker-entrypoint.sh"]

# ENV MYSQL_PASS=root123
# ENV PHP_UPLOAD_MAX_FILESIZE=1024M
# ENV PHP_POST_MAX_SIZE=1024M

EXPOSE 3306 80

CMD ["tail", "-f", "/dev/null"]