FROM php:8.2-apache-bookworm

RUN apt-get update
RUN apt-get install unzip git sqlite3 -y

RUN apt-get install libicu-dev -y
RUN docker-php-ext-install intl

COPY files/composer/composer-2.6.5.phar /usr/local/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -i "$PHP_INI_DIR/php.ini" -e 's/^session.use_strict_mode.*/session.use_strict_mode = 1/'

RUN mkdir /opt/simplesamlphp
WORKDIR /opt/simplesamlphp

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY files/composer/composer-git.json composer.json
RUN composer require "simplesamlphp/simplesamlphp:~2.0.4"
WORKDIR /opt/simplesamlphp/vendor/simplesamlphp/simplesamlphp
COPY files/simplesamlphp/config/* config/
COPY files/simplesamlphp/metadata/* metadata/
COPY files/simplesamlphp/cert/* cert/

COPY files/theme-mod modules/theme-mod

WORKDIR /opt/simplesamlphp
RUN composer require "exeba/simplesamlphp-module-oauth2:dev-master"

RUN vendor/simplesamlphp/simplesamlphp/modules/oauth2/bin/doctrine orm:schema-tool:create
RUN vendor/simplesamlphp/simplesamlphp/modules/oauth2/bin/doctrine orm:schema-tool:update --force --complete
RUN vendor/simplesamlphp/simplesamlphp/modules/oauth2/bin/doctrine orm:generate-proxies

RUN chown www-data:www-data -R /opt/simplesamlphp
RUN chown www-data:www-data -R /tmp/simplesaml

RUN mkdir /opt/dummyclient
COPY files/dummyclient /opt/dummyclient
RUN sqlite3 /opt/simplesamlphp/vendor/simplesamlphp/simplesamlphp/simplesamlphp.sqlite < /opt/dummyclient/dummy_client.sql

ARG PORT=8000
RUN sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
RUN sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-enabled/*
COPY files/apache/simplesamlphp-apache.conf /etc/apache2/conf-enabled/simplesamlphp.conf
COPY files/apache/dummyclient-apache.conf /etc/apache2/conf-enabled/dummyclient.conf
