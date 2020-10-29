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
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;

try {
    $request = ServerRequestFactory::fromGlobals();
    $parameters = $request->getQueryParams();
    $clientId = array_key_exists('client_id', $parameters) ? $parameters['client_id'] : null;

    // The AS could be configured by client
    $clientRepository = new ClientRepository();
    $client = $clientRepository->find($clientId);

    $oauth2config = \SimpleSAML\Configuration::getOptionalConfig('module_oauth2.php');

    if (!$client || !$client['auth_source']) {
        $as = $oauth2config->getString('auth');
    } else {
        $as = $client['auth_source'];
    }

    $auth = new \SimpleSAML\Auth\Simple($as);
    $auth->requireAuth();

    $attributes = $auth->getAttributes();
    $useridattr = $oauth2config->getString('useridattr');

    if (!isset($attributes[$useridattr])) {
        throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
    }
    $userid = $attributes[$useridattr][0];

    // Persists the user attributes on the database
    $userRepository = new UserRepository();
    $userRepository->insertOrCreate($userid, $attributes);

    $server = OAuth2AuthorizationServer::getInstance();
    $authRequest = $server->validateAuthorizationRequest($request);
    $authRequest->setUser(new UserEntity($userid));
    $authRequest->setAuthorizationApproved(true);

    $response = $server->completeAuthorizationRequest($authRequest, new Response());

    $emiter = new SapiEmitter();
    $emiter->emit($response);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getMessage());

    print_r($e);
}
