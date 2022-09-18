<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Laminas\Diactoros\Response\RedirectResponse;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;

class AuthorizeChoiceHandler implements RequestHandlerInterface
{
    private $responseFactory;
    private $authorizationServer;
    private $authRequestSerializer;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        AuthorizationServer $authorizationServer,
        AuthRequestSerializer $authRequestSerializer
    ) {
        $this->responseFactory = $responseFactory;
        $this->authorizationServer = $authorizationServer;
        $this->authRequestSerializer = $authRequestSerializer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new AuthorizeForm('authorize');
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new BadRequest('AuthorizationRequest not found');
        }
        $form->fireEvents();

        $authorizationRequest = $this->authRequestSerializer->deserialize($form->getValues()['authRequest']);
        $authorizationRequest->setAuthorizationApproved($form->hasPressed('allow'));

        try {
            return $this->authorizationServer->completeAuthorizationRequest($authorizationRequest, $this->newResponse());
        } catch (OAuthServerException $e) {
            return new RedirectResponse($this->buildErrorRedirectUri($e));
        }
    }

    private function buildErrorRedirectUri(OAuthServerException $error)
    {
        $originalUri = $error->getRedirectUri();
        $errorInfo = http_build_query($error->getPayload());
        if (false === strpos($originalUri, '?')) {
            return "{$originalUri}?{$errorInfo}";
        } else {
            return "{$originalUri}&{$errorInfo}";
        }
    }

    private function newResponse()
    {
        return $this->responseFactory->createResponse();
    }
}
