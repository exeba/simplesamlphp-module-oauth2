<?php

$config = [
    'production' => false,

    'basedir' => dirname(__DIR__, 3),

    'certdir' => dirname(__DIR__).'/certs',
    'tempdir' => dirname(__DIR__).'/tmp',
    'loggingdir' => dirname(__DIR__).'/log',

    'secretsalt' => 'testsecretsalt',
];
