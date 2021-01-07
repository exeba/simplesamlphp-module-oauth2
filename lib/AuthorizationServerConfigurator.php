<?php


namespace SimpleSAML\Module\oauth2;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

class AuthorizationServerConfigurator
{

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
    }
}
