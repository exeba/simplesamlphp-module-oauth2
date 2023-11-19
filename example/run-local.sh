#!/bin/sh

set -e

mkdir -p local
if [ ! -e local/composer.json ]; then
    cp files/composer/composer-local.json local/composer.json
fi

composer --working-dir=local/ require "simplesamlphp/simplesamlphp:~2.0.4"
composer --working-dir=local/ require "exeba/simplesamlphp-module-oauth2 @dev"

export SIMPLESAMLPHP_INSTALL_DIR="$(pwd)/local/vendor/simplesamlphp/simplesamlphp"
export SIMPLESAMLPHP_CONFIG_DIR="$(pwd)/files/simplesamlphp/config"
export DUMMYCLIENT_INSTALL_DIR="$(pwd)/files/dummyclient"

export SQLITE_DATABASE_PATH="$(pwd)/files/simplesamlphp/simplesamlphp.sqlite"
if [ ! -e "$SQLITE_DATABASE_PATH" ]; then
    "$SIMPLESAMLPHP_INSTALL_DIR/modules/oauth2/bin/doctrine" orm:schema-tool:create
fi
"$SIMPLESAMLPHP_INSTALL_DIR/modules/oauth2/bin/doctrine" orm:schema-tool:update --force --complete
"$SIMPLESAMLPHP_INSTALL_DIR/modules/oauth2/bin/doctrine" orm:generate-proxies
sqlite3 "$SQLITE_DATABASE_PATH" < "$DUMMYCLIENT_INSTALL_DIR/dummy_client.sql"

if [ ! -e local/simplesamlphp ]; then
    ln -s "$SIMPLESAMLPHP_INSTALL_DIR/public" local/simplesamlphp
fi
if [ ! -e local/dummyclient ]; then
    ln -s "$DUMMYCLIENT_INSTALL_DIR/www" local/dummyclient
fi

export PHP_CLI_SERVER_WORKERS=2
php -S 127.0.0.1:8000 -t local/
