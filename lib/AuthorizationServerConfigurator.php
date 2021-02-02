<?php


namespace SimpleSAML\Module\oauth2;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

class AuthorizationServerConfigurator
{

    private $authorizationServer;

    public function __construct(
        AuthorizationServer $authorizationServer,
        AuthCodeGrant $authCodeGrant,
        RefreshTokenGrant $refreshTokenGrant,
        ClientCredentialsGrant $clientCredentialsGrant,
        \DateInterval $refreshTokenDuration,
        \DateInterval $accessTokenDuration
    ) {
        $authCodeGrant->setRefreshTokenTTL($refreshTokenDuration);
        $authorizationServer->enableGrantType($authCodeGrant, $accessTokenDuration);

        $refreshTokenGrant->setRefreshTokenTTL($accessTokenDuration);
        $authorizationServer->enableGrantType($refreshTokenGrant, $accessTokenDuration);

        $authorizationServer->enableGrantType($clientCredentialsGrant, $accessTokenDuration);

        $this->authorizationServer = $authorizationServer;
    }

    public function getConfiguredAuthorizationServer(): AuthorizationServer
    {
        return $this->authorizationServer;
    }
}
