<?php


namespace SimpleSAML\Module\oauth2\Services;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\RequestInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Module\oauth2\Entity\UserEntity;

class AuthenticationService
{
    private $userIdAttribute;
    private $defaultAuthenticationSource;
    private $attributesProcessor;

    public function __construct(
        $userIdAttribute,
        $defaultAuthenticationSource,
        AttributesProcessor $attributesProcessor
    ) {
        $this->userIdAttribute = $userIdAttribute;
        $this->defaultAuthenticationSource = $defaultAuthenticationSource;
        $this->attributesProcessor = $attributesProcessor;
    }

    public function getUserForAuthnRequest(AuthorizationRequest $authnRequest)
    {
        $authSource = $this->getAuthSourceIdFromAuthnRequest($authnRequest);
        $auth = $this->requireAuthentication($authSource);

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    public function getUserForRequest(RequestInterface $request)
    {
        $authSource = $this->getAuthSourceIdFromRequest($request);
        $auth = $this->requireAuthentication($authSource);

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    private function requireAuthentication(string $authenticationSource)
    {
        $auth = new Simple($authenticationSource);
        $auth->requireAuth();

        return $auth;
    }

    private function getAuthSourceIdFromAuthnRequest(AuthorizationRequest $authRequest)
    {
        return $authRequest->getClient()->getAuthSource() ?? $this->defaultAuthenticationSource;
    }

    private function getAuthSourceIdFromRequest(RequestInterface $request)
    {
        return $request->getAttributes()['authSource'] ?? $this->defaultAuthenticationSource;
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
