<?php

$config = [
    'production' => false,

    'basedir' => dirname(__DIR__, 3),

    'logging.handler' => 'file',

    'certdir' => dirname(__DIR__).'/certs',
    'tempdir' => dirname(__DIR__).'/tmp',
    'loggingdir' => dirname(__DIR__).'/log',

    'secretsalt' => 'testsecretsalt',
];
