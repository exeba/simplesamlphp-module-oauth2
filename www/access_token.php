<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;

try {
    $server = OAuth2AuthorizationServer::getInstance();
    $request = ServerRequestFactory::fromGlobals();

    $response = $server->respondToAccessTokenRequest($request, new Response());

    $emiter = new SapiEmitter();
    $emiter->emit($response);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getMessage());

    print_r($e);
}
