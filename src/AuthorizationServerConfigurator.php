<?php

namespace SimpleSAML\Module\oauth2;

use League\OAuth2\Server\AuthorizationServer;

class AuthorizationServerConfigurator
{
    private $authorizationServer;

    public function __construct(
        AuthorizationServer $authorizationServer,
        $grants,
        \DateInterval $accessTokenDuration
    ) {
        foreach ($grants as $grant) {
            $authorizationServer->enableGrantType($grant, $accessTokenDuration);
        }

        $this->authorizationServer = $authorizationServer;
    }

    public function getAuthorizationServer(): AuthorizationServer
    {
        return $this->authorizationServer;
    }
}
