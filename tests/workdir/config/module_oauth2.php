<?php

$config = [
    'useridattr' => 'user_id_attribute',
    'auth' => 'default_auth_source',

    'oauth2.dbal.url' => 'sqlite://./test.sqlite',
    'oauth2.dbal.prefix' => 'test_',

    // Tokens TTL
    'authCodeDuration' => 'PT10M', // 10 minutes
    'refreshTokenDuration' => 'P1M', // 1 month
    'accessTokenDuration' => 'PT1H', // 1 hour,

    'certificate' => 'oauth2_module_test.crt',
    'privateKey' => 'oauth2_module_test.key',

    'scopes' => [
        'basic' => [
            'icon' => 'user',
            'description' => [
                'en' => 'Your username.',
                'es' => 'Su nombre de usuario.',
            ],
            'attributes' => ['uid'],
        ],
        'extra' => [
            'icon' => 'user',
            'description' => [
                'en' => 'Your username.',
                'es' => 'Su nombre de usuario.',
            ],
            'attributes' => ['uid'],
        ],
    ],
];
