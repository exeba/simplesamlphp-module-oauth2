<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Test\Module\oauth2\Fixtures\Fixtures;

class RefreshTokenRepositoryTest extends AbstractTokenRepositoryTest
{
    private $accessTokenRepository;
    private $clientRepository;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(RefreshTokenRepository::class);
        $this->accessTokenRepository = $kernel->getContainer()->get(AccessTokenRepository::class);
        $this->clientRepository = $kernel->getContainer()->get(ClientRepository::class);
    }

    protected function getRepository(): RefreshTokenRepository
    {
        return $this->repository;
    }

    protected function newValidToken($tokenId)
    {
        $accessToken = Fixtures::newValidAccessToken($tokenId, $this->getAnyClient(), 'user-id');
        $this->accessTokenRepository->persistNewAccessToken($accessToken);
        $token = Fixtures::newValidRefreshToken($tokenId, $accessToken);
        $this->getRepository()->persistNewRefreshToken($token);

        return $token;
    }

    protected function newExpiredToken($tokenId)
    {
        $accessToken = Fixtures::newValidAccessToken($tokenId, $this->getAnyClient(), 'user-id');
        $this->accessTokenRepository->persistNewAccessToken($accessToken);

        $token = Fixtures::newExpiredRefreshToken($tokenId, $accessToken);
        $this->getRepository()->persistNewRefreshToken($token);

        return $token;
    }

    protected function newRevokedToken($tokenId)
    {
        $accessToken = Fixtures::newValidAccessToken($tokenId, $this->getAnyClient(), 'user-id');
        $this->accessTokenRepository->persistNewAccessToken($accessToken);

        $token = Fixtures::newRevokedRefreshToken($tokenId, $accessToken);
        $this->getRepository()->persistNewRefreshToken($token);

        return $token;
    }

    protected function isTokenRevoked($tokenId)
    {
        return $this->getRepository()->isRefreshTokenRevoked($tokenId);
    }

    protected function revokeToken($tokenId)
    {
        $this->getRepository()->revokeRefreshToken($tokenId);
    }

    private function getAnyClient()
    {
        return $this->clientRepository->findAll()[0];
    }
}
