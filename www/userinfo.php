<?php

use Laminas\Diactoros\ResponseFactory;
use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;
use SimpleSAML\Module\oauth2\Middleware\ResourceRequestMiddleware;
use SimpleSAML\Module\oauth2\Controller\UserInfoRequestHandler;
use SimpleSAML\Module\oauth2\Middleware\MiddlewareStack;
use SimpleSAML\Module\oauth2\OAuth2ResourceServer;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;


$middleware = new MiddlewareStack();
$middleware->addMiddleware(new ResourceRequestMiddleware(OAuth2ResourceServer::getInstance()));
$middleware->addMiddleware(new RequestExceptionMiddleware(new ResponseFactory()));
$userInfoHandler = new UserInfoRequestHandler(new UserRepository());
(new App($middleware))->run($userInfoHandler);
