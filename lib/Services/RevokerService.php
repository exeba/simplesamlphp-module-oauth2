<?php


namespace SimpleSAML\Module\oauth2\Services;


use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;

class RevokerService
{

    public $accessTokenRepository;
    public $authCodeRepository;
    public $refreshTokenRepository;

    public function __construct(
        AccessTokenRepository $accessTokenRepository,
        AuthCodeRepository $authCodeRepository,
        RefreshTokenRepository $refreshTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function revokeAuthCodeIfOwner($tokenIdentifier, $ownerId)
    {
        $token = $this->authCodeRepository->findByIdentifier($tokenIdentifier);
        $this->throwIfNotMatching($ownerId, $token->getUserIdentifier());
        $this->revokeAuthCode($tokenIdentifier);
    }

    public function revokeAuthCode($tokenIdentifier)
    {
        $this->authCodeRepository->revokeAuthCode($tokenIdentifier);
    }

    public function revokeRefreshTokenIfOwner($tokenIdentifier, $ownerId)
    {
        $token = $this->refreshTokenRepository->findByIdentifier($tokenIdentifier);
        $this->throwIfNotMatching($ownerId, $token->getAccessToken()->getUserIdentifier());
        $this->revokeRefreshToken($tokenIdentifier);
    }

    public function revokeRefreshToken($tokenIdentifier)
    {
        $this->refreshTokenRepository->revokeRefreshToken($tokenIdentifier);
    }

    public function revokeAccessTokenIfOwner($tokenIdentifier, $ownerId)
    {
        $token = $this->accessTokenRepository->findByIdentifier($tokenIdentifier);
        $this->throwIfNotMatching($ownerId, $token->getUserIdentifier());
        $this->revokeAccessToken($tokenIdentifier);
    }

    public function revokeAccessToken($tokenIdentifier)
    {
        $this->accessTokenRepository->revokeAccessToken($tokenIdentifier);
    }

    private function throwIfNotMatching($userIdA, $userIdB)
    {
        if ($userIdA !== $userIdB) {
            throw new \Exception("User not allowed");
        }
    }
}
