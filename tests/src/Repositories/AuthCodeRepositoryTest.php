<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Test\Module\oauth2\Fixtures\Fixtures;

class AuthCodeRepositoryTest extends AbstractTokenRepositoryTest
{
    private $clientRepository;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(AuthCodeRepository::class);
        $this->clientRepository = $kernel->getContainer()->get(ClientRepository::class);
    }

    protected function getRepository(): AuthCodeRepository
    {
        return $this->repository;
    }

    protected function newValidToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newValidAuthCode(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAuthCode($token);

        return $token;
    }

    protected function newExpiredToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newExpiredAuthCode(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAuthCode($token);

        return $token;
    }

    protected function newRevokedToken($tokenId)
    {
        $clientEntity = $this->getAnyClient();
        $token = Fixtures::newRevokedAuthCode(
            $tokenId,
            $clientEntity,
            'user-id'
        );
        $this->getRepository()->persistNewAuthCode($token);

        return $token;
    }

    private function getAnyClient()
    {
        return $this->clientRepository->findAll()[0];
    }

    protected function isTokenRevoked($tokenId)
    {
        return $this->getRepository()->isAuthCodeRevoked($tokenId);
    }

    protected function revokeToken($tokenId)
    {
        $this->getRepository()->revokeAuthCode($tokenId);
    }
}
