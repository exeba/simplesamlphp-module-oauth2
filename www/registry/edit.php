<?php

use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\Controller\EditClientHandler;
use SimpleSAML\Module\oauth2\InjectorFactory;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;


$injector = InjectorFactory::getInjector();
$injector->create(App::class, [
    'middleware' => $injector->create(RequestExceptionMiddleware::class),
    'handler' => $injector->create(EditClientHandler::class)
])->run();
