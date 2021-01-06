<?php

use Laminas\Diactoros\ResponseFactory;
use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Controller\AuthorizeChoiceHandler;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;


$responseFactory = new ResponseFactory();
$handler = new AuthorizeChoiceHandler(
    $responseFactory,
    OAuth2AuthorizationServer::getInstance(),
    AuthRequestSerializer::getInstance());

$app = new App(new RequestExceptionMiddleware($responseFactory));

(new App())->run($handler);
