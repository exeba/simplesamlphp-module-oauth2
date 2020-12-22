<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Laminas\Diactoros\ServerRequestFactory;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\ScopeRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Utils\Config;
use SimpleSAML\XHTML\Template;

try {
    $request = ServerRequestFactory::fromGlobals();
    $parameters = $request->getQueryParams();
    $clientId = array_key_exists('client_id', $parameters) ? $parameters['client_id'] : null;

    // The AS could be configured by client
    $clientRepository = new ClientRepository();
    $client = $clientRepository->find($clientId);

    $oauth2config = Configuration::getOptionalConfig('module_oauth2.php');

    if (!$client || !$client['auth_source']) {
        $as = $oauth2config->getString('auth');
    } else {
        $as = $client['auth_source'];
    }

    $auth = new Simple($as);
    $auth->requireAuth();

    $attributes = $auth->getAttributes();
    $useridattr = $oauth2config->getString('useridattr');
    if (!isset($attributes[$useridattr])) {
        throw new Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
    }
    $userid = $attributes[$useridattr][0];

    // Persists the user attributes on the database
    $userRepository = new UserRepository();
    $userRepository->insertOrCreate($userid, $attributes);

    $server = OAuth2AuthorizationServer::getInstance();
    $authRequest = $server->validateAuthorizationRequest($request);
    $authRequest->setUser(new UserEntity($userid));

    $serializer = new AuthRequestSerializer($clientRepository, new ScopeRepository(), $userRepository, Config::getSecretSalt());
    $serializedRequest = $serializer->serialize($authRequest);

    $form = new AuthorizeForm('authorize');
    $form->setDefaults(['authRequest' => $serializedRequest]);
    $form->setAction(Module::getModuleURL('oauth2/user_choice.php'));

    $config = Configuration::getInstance();
    $template = new Template($config, 'oauth2:authorize');
    $template->data['client'] = $client;
    $template->data['authRequest'] = $authRequest;
    $template->data['form'] = $form;
    $template->send();

} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getMessage());

    print_r($e);
}
