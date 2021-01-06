<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Laminas\Diactoros\ResponseFactory;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\App;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Controller\AuthorizeRequestHandler;
use SimpleSAML\Module\oauth2\Middleware\RequestExceptionMiddleware;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;

$responseFactory = new ResponseFactory();
$oauth2config = Configuration::getOptionalConfig('module_oauth2.php');
$authorizationServer = OAuth2AuthorizationServer::getInstance();
$authRequestSerializer = AuthRequestSerializer::getInstance();

$authenticationService = new AuthenticationService(
        new ClientRepository(),
        $oauth2config);

$handler = new AuthorizeRequestHandler(
        new UserRepository(),
        $authorizationServer,
        $authRequestSerializer,
        $authenticationService,
        $oauth2config);

$app = new App(new RequestExceptionMiddleware($responseFactory));

(new App())->run($handler);
