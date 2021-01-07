<?php

use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\AuthorizationServerConfigurator;
use SimpleSAML\Module\oauth2\Controller\AccessTokenRequestHandler;
use SimpleSAML\Module\oauth2\InjectorFactory;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;


$injector = InjectorFactory::getInjector();
$injector->create(AuthorizationServerConfigurator::class);

$injector->create(App::class, [
    'middleware' => $injector->create(RequestExceptionMiddleware::class),
    'handler' => $injector->create(AccessTokenRequestHandler::class),
])->run();
