<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Test\Module\oauth2\Fixtures\Fixtures;

class AccessTokenRepositoryTest extends AbstractTokenRepositoryTest
{
    private $clientRepository;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(AccessTokenRepository::class);
        $this->clientRepository = $kernel->getContainer()->get(ClientRepository::class);
    }

    protected function getRepository(): AccessTokenRepository
    {
        return $this->repository;
    }

    protected function newValidToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newValidAccessToken(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAccessToken($token);

        return $token;
    }

    protected function newExpiredToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newExpiredAccessToken(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAccessToken($token);

        return $token;
    }

    protected function newRevokedToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newRevokedAccessToken(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAccessToken($token);

        return $token;
    }

    private function getAnyClient()
    {
        return $this->clientRepository->findAll()[0];
    }

    protected function isTokenRevoked($tokenId)
    {
        return $this->getRepository()->isAccessTokenRevoked($tokenId);
    }

    protected function revokeToken($tokenId)
    {
        $this->getRepository()->revokeAccessToken($tokenId);
    }
}
