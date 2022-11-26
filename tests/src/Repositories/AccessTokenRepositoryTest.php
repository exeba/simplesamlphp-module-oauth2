<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Test\Module\oauth2\Fixtures\Fixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccessTokenRepositoryTest extends KernelTestCase
{
    private $clientRepository;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(AccessTokenRepository::class);
        $this->clientRepository = $kernel->getContainer()->get(ClientRepository::class);
    }

    private function getRepository(): AccessTokenRepository
    {
        return $this->repository;
    }

    private function getClientRepository(): ClientRepository
    {
        return $this->clientRepository;
    }

    public function testGetTokensForUser()
    {
        $clientEntity = $this->getAnyClient();
        $this->getRepository()->persistNewAccessToken(Fixtures::newValidAccessToken(
            'valid-access-token-id',
            $clientEntity,
            'user-id'
        ));
        $this->getRepository()->persistNewAccessToken($accessToken = Fixtures::newExpiredAccessToken(
            'expired-access-token-id',
            $clientEntity,
            'user-id'
        ));

        $tokens = $this->getRepository()->getTokensForUser('user-id');
        $this->assertCount(2, $tokens);
    }

    public function testGetActiveTokensForUser()
    {
        $clientEntity = $this->getAnyClient();
        $this->getRepository()->persistNewAccessToken(Fixtures::newValidAccessToken(
            'valid-access-token-id',
            $clientEntity,
            'user-id'
        ));
        $this->getRepository()->persistNewAccessToken($accessToken = Fixtures::newExpiredAccessToken(
            'expired-access-token-id',
            $clientEntity,
            'user-id'
        ));

        $tokens = $this->getRepository()->getActiveTokensForUser('user-id');
        $this->assertCount(1, $tokens);
    }

    public function testRevokeToken()
    {
        $clientEntity = $this->getAnyClient();
        $this->getRepository()->persistNewAccessToken(Fixtures::newValidAccessToken(
            'valid-access-token-id',
            $clientEntity,
            'user-id'
        ));

        $this->assertCount(1, $this->getRepository()->getActiveTokensForUser('user-id'));
        $this->getRepository()->revokeAccessToken('valid-access-token-id');
        $this->assertCount(0, $this->getRepository()->getActiveTokensForUser('user-id'));
        $this->assertTrue($this->getRepository()->isAccessTokenRevoked('valid-access-token-id'));
        $this->assertTrue($this->getRepository()->isAccessTokenRevoked('valid-access-token-id'));
    }

    private function getAnyClient()
    {
        return $this->getClientRepository()->findAll()[0];
    }
}
