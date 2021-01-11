<?php

use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\Controller\RegistryIndexHandler;
use SimpleSAML\Module\oauth2\InjectorFactory;
use SimpleSAML\Module\oauth2\Middleware\AuthenticatedAdminMiddleware;
use SimpleSAML\Module\oauth2\Middleware\MiddlewareStack;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;

$injector = InjectorFactory::getInjector();

$middleware = new MiddlewareStack();
$middleware->addMiddleware($injector->create(RequestExceptionMiddleware::class));
$middleware->addMiddleware($injector->create(AuthenticatedAdminMiddleware::class));

$injector->create(App::class, [
    'middleware' => $middleware,
    'handler' => $injector->create(RegistryIndexHandler::class)
])->run();

