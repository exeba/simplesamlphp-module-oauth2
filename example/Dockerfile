FROM php:8.0-apache-bullseye

RUN apt-get update
RUN apt-get install wget git zip unzip sqlite3 -y

RUN apt-get install libicu-dev -y
RUN docker-php-ext-install intl

RUN apt-get install npm -y
RUN npm i npm@latest -g

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -i "$PHP_INI_DIR/php.ini" -e 's/^session.use_strict_mode.*/session.use_strict_mode = 1/'

RUN mkdir /opt/simplesamlphp
WORKDIR /opt/simplesamlphp

RUN wget https://getcomposer.org/download/2.1.12/composer.phar
RUN chmod +x composer.phar

RUN ./composer.phar require "simplesamlphp/simplesamlphp:~2.0.0-rc2"
WORKDIR /opt/simplesamlphp/vendor/simplesamlphp/simplesamlphp
COPY files/simplesamlphp/config/* config/
COPY files/simplesamlphp/metadata/* metadata/
COPY files/simplesamlphp/cert/* cert/

COPY files/theme-mod modules/theme-mod

RUN npm install
RUN npm run build

WORKDIR /opt/simplesamlphp
RUN ./composer.phar require "exeba/simplesamlphp-module-oauth2:dev-master"
RUN cp vendor/simplesamlphp/simplesamlphp/modules/oauth2/cli-config.php .

RUN vendor/bin/doctrine orm:schema-tool:create
RUN vendor/bin/doctrine orm:schema-tool:update --force
RUN vendor/bin/doctrine orm:generate-proxies

RUN chown www-data:www-data -R /opt/simplesamlphp

RUN mkdir /opt/dummyclient
COPY files/dummyclient /opt/dummyclient
RUN ln -s /opt/dummyclient/www /var/www/html/dummyclient
RUN sqlite3 /opt/simplesamlphp/vendor/simplesamlphp/simplesamlphp/simplesamlphp.sqlite < /opt/dummyclient/dummy_client.sql

RUN ln -s /opt/simplesamlphp/vendor/simplesamlphp/simplesamlphp/www /var/www/html/simplesamlphp

ARG PORT=8000
RUN sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
RUN sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-enabled/*
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
RUN echo "CGIPassAuth On" > /var/www/html/simplesamlphp/.htaccess
