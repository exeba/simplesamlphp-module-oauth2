<?php

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;
use SimpleSAML\Module\oauth2\OAuth2AuthorizationServer;

$request = ServerRequestFactory::fromGlobals();
$form = new AuthorizeForm('authorize');
if (!$form->isSubmitted() || !$form->isValid()) {
    throw new BadRequest('AuthorizationRequest not found');
}
$form->fireEvents();

$authorizationRequest = AuthRequestSerializer::getInstance()->deserialize($form->getValues()['authRequest']);
$authorizationRequest->setAuthorizationApproved($form->hasPressed('allow'));

$server = OAuth2AuthorizationServer::getInstance();
$response = $server->completeAuthorizationRequest($authorizationRequest, new Response());

$emitter = new SapiEmitter();
$emitter->emit($response);
