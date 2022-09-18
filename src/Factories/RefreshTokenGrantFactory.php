<?php

namespace SimpleSAML\Module\oauth2\Factories;

use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenGrantFactory
{
    private $refreshTokenRepository;
    private $refreshTokenDuration;

    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        \DateInterval $refreshTokenDuration
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->refreshTokenDuration = $refreshTokenDuration;
    }

    public function buildRefreshTokenGrant()
    {
        $refreshTokenGrant = new RefreshTokenGrant($this->refreshTokenRepository);
        $refreshTokenGrant->setRefreshTokenTTL($this->refreshTokenDuration);

        return $refreshTokenGrant;
    }
}
