<?php

use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\AuthorizationServerConfigurator;
use SimpleSAML\Module\oauth2\InjectorFactory;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;
use SimpleSAML\Module\oauth2\Middleware\ResourceRequestMiddleware;
use SimpleSAML\Module\oauth2\Controller\UserInfoRequestHandler;
use SimpleSAML\Module\oauth2\Middleware\MiddlewareStack;

$injector = InjectorFactory::getInjector();
$injector->create(AuthorizationServerConfigurator::class);

$middleware = new MiddlewareStack();
$middleware->addMiddleware($injector->create(RequestExceptionMiddleware::class));
$middleware->addMiddleware($injector->create(ResourceRequestMiddleware::class));

$injector->create(App::class, [
    'middleware' => $middleware,
    'handler' => $injector->create(UserInfoRequestHandler::class),
])->run();

