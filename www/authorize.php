<?php

use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\AuthorizationServerConfigurator;
use SimpleSAML\Module\oauth2\Controller\AuthorizeRequestHandler;
use SimpleSAML\Module\oauth2\InjectorFactory;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;


$injector = InjectorFactory::getInjector();
$injector->create(AuthorizationServerConfigurator::class);

$middleware = $injector->create(RequestExceptionMiddleware::class);
$handler = $injector->create(AuthorizeRequestHandler::class);


$app = new App($middleware);

(new App())->run($handler);
