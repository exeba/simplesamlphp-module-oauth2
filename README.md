SimpleSAMLphp OAuth2 module
====================================

## Installation

This package add support for the OAuth2 protocol through a SimpleSAMLphp module
installable through [Composer](https://getcomposer.org/). Installation can be as
easy as executing:

```
composer.phar require exeba/simplesamlphp-module-oauth2 ~3.0
```

## Configuration

### Configure the module

Copy the template file to the config directory:

```
cp modules/oauth2/config-template/module_oauth2.php config/
```

and edit it. The options are self explained.

### Create or the schema

The schema is maintaned using doctrine command line tool  ```vendor/bin/doctrine```.
Tou just need to copy the ```cli.config.php``` file into the main directory:

```
cp vendor/simplesamlphp/simplesamlphp/modules/oauth2/cli-config.php .
```

## Create oauth2 clients

To add and remove Oauth2 clients, you need to logon on simplesaml with an admin account. Open the _Federation_ tab
and you will see the _OAuth2 Client Registry_ option.

You can specify as many redirect address as you want.

## Using the module

This module is based on [Oauth2 Server from the PHP League](https://oauth2.thephpleague.com/)
and supports the following grants:

 - Authorization code grant
 - Client credentials grant
 - Refresh grant

### Create the oauth2 keys:

The oauth2 library used generates Json Web Tokens to create the Access Tokens, so you need to create a public and private cert keys:

To generate the private key run this command on the terminal:

```
openssl genrsa -out cert/oauth2_module.pem 1024
```

If you want to provide a passphrase for your private key run this command instead:

```
openssl genrsa -passout pass:_passphrase_ -out cert/oauth2_module.pem 1024
```

then extract the public key from the private key:

```
openssl rsa -in cert/oauth2_module.pem -pubout -out cert/oauth2_module.crt
```
or use your passphrase if provided on private key generation:

```
openssl rsa -in cert/oauth2_module.pem -passin pass:_passphrase_ -pubout -out cert/oauth2_module.crt
```

If you use a passphrase remember to configure it in the _module_oauth2.php_ config file.

### Endpoints

 - Authorization Endpoint:  {{baseurlpath}}/module.php/oauth2/authorize
 - Token Endpoint:  {{baseurlpath}}/module.php/oauth2/access_token
 - Token Introspection Endpoint: {{baseurlpath}}/module.php/oauth2/userinfo
