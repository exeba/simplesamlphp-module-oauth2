<?php

namespace SimpleSAML\Module\oauth2\Services;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use SimpleSAML\Module\oauth2\Entity\UserEntity;

class AuthenticationService
{
    private $userIdAttribute;
    private $simpleSamlFactory;
    private $authenticationSourceResolver;
    private $attributesProcessor;

    public function __construct(
        $userIdAttribute,
        SimpleSamlFactory $simpleSamlFactory,
        AuthenticationSourceResolver $authenticationSourceResolver,
        AttributesProcessor $attributesProcessor
    ) {
        $this->userIdAttribute = $userIdAttribute;
        $this->simpleSamlFactory = $simpleSamlFactory;
        $this->authenticationSourceResolver = $authenticationSourceResolver;
        $this->attributesProcessor = $attributesProcessor;
    }

    public function getUserForAuthnRequest(AuthorizationRequest $authnRequest)
    {
        $authSourceId = $this->authenticationSourceResolver->getAuthSourceIdFromClient($authnRequest->getClient());
        $auth = $this->requireAuthentication($authSourceId);

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    public function getUserForRequest(ServerRequestInterface $request)
    {
        $authSourceId = $this->authenticationSourceResolver->getAuthSourceIdFromRequest($request);
        $auth = $this->requireAuthentication($authSourceId);

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    private function requireAuthentication(string $authenticationSource)
    {
        $auth = $this->simpleSamlFactory->createSimple($authenticationSource);
        $auth->requireAuth();

        return $auth;
    }

    private function buildUserFromAttributes($attributes): UserEntity
    {
        if (!isset($attributes[$this->userIdAttribute])) {
            throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
        }
        $userid = $attributes[$this->userIdAttribute][0];

        $user = new UserEntity($userid);
        $user->setAttributes($this->attributesProcessor->processAttributes($attributes));

        return $user;
    }
}
