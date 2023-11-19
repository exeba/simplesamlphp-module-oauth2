<?php

$client = [
    'id' => '_a166287db4f4697a0f7faddaed857d92ef53060d13',
    'secret' => '_cc39c61257c4fdbba6721185520d9f3ccb9747fb90',
    'scope' => 'openid basic extras',
    'redirect_uri' => 'http://127.0.0.1:8000/dummyclient/oauth2/endpoint.php',
];

$server = [
    'authoritazionEndpoint' => 'http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/authorize',
    'accessTokenEndpoint' => 'http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/access_token',
    'userInfoEndpoint' => 'http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/userinfo',
];

$dummyVerifier = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
$dummyState = 'fakecsrf';
