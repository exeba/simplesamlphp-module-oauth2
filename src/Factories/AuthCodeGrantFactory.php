<?php

namespace SimpleSAML\Module\oauth2\Factories;

use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class AuthCodeGrantFactory
{
    private $authCodeRepository;
    private $refreshTokenRepository;
    private $authCodeDuration;
    private $refreshTokenDuration;

    public function __construct(
        AuthCodeRepositoryInterface $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        \DateInterval $authCodeDuration,
        \DateInterval $refreshTokenDuration
    ) {
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->authCodeDuration = $authCodeDuration;
        $this->refreshTokenDuration = $refreshTokenDuration;
    }

    public function buildAuthCodeGrant()
    {
        $authCodeGrant = new AuthCodeGrant(
            $this->authCodeRepository,
            $this->refreshTokenRepository,
            $this->authCodeDuration
        );
        $authCodeGrant->setRefreshTokenTTL($this->refreshTokenDuration);

        return $authCodeGrant;
    }
}
