<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use League\OAuth2\Server\CryptKey;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RefreshTokenRepositoryTest extends KernelTestCase
{

    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(RefreshTokenRepository::class);

    }

    private function getRepository(): RefreshTokenRepository
    {
        return $this->repository;
    }

    public function testDummy()
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setIdentifier('access-token-id');
        $accessToken->setPrivateKey(new CryptKey());

        $refreshToken = $this->getRepository()->getNewRefreshToken();
        $refreshToken->setAccessToken($accessToken);
        $refreshToken->setIdentifier('test-refresh-token');
        $refreshToken->setRevoked(false);
        $refreshToken->setExpiryDateTime(new \DateTimeImmutable());

        $this->getRepository()->persistNewRefreshToken($refreshToken);

        $this->assertNotNull($this->getRepository()->findByIdentifier('aaaa'));
    }

}