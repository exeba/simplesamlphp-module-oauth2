<?php

use Laminas\Diactoros\ResponseFactory;
use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\Controller\AccessTokenRequestHandler;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;


$responseFactory = new ResponseFactory();
$handler = new AccessTokenRequestHandler(
        OAuth2AuthorizationServer::getInstance(),
        $responseFactory);

$app = new App(new RequestExceptionMiddleware($responseFactory));

(new App())->run($handler);
