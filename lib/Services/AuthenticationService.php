<?php


namespace SimpleSAML\Module\oauth2\Services;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\RequestInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;

class AuthenticationService
{
    private $clientRepository;
    private $userIdAttribute;
    private $defaultAuthenticationSource;

    public function __construct(
        ClientRepository $clientRepository,
        $userIdAttribute,
        $defaultAuthenticationSource
    )
    {
        $this->clientRepository = $clientRepository;
        $this->userIdAttribute = $userIdAttribute;
        $this->defaultAuthenticationSource = $defaultAuthenticationSource;
    }

    public function getUserForAuthnRequest(AuthorizationRequest $authnRequest)
    {
        $authSource = $this->getAuthSourceIdFromAuthnRequest($authnRequest);
        $auth = $this->requireAuthentication($authSource);

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    public function getUserForRequest(RequestInterface $request)
    {
        $authSource = $this->getAuthSourceIdFromAuthnRequest($request);
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
        $user->setAttributes($attributes);

        return $user;
    }
}
