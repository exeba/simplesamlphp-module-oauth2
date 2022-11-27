<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractTokenRepositoryTest extends KernelTestCase
{
    public function testGetTokensForUser()
    {
        $this->newValidToken('valid-access-token-id');
        $this->newExpiredToken('expired-access-token');
        $this->newRevokedToken('revoked-access-token');

        $tokens = $this->getRepository()->getTokensForUser('user-id');
        $this->assertCount(3, $tokens, 'All user tokens (valid, expired, revoked) should be returned');
    }

    public function testGetActiveTokensForUser()
    {
        $this->newValidToken('valid-access-token-id');
        $this->newExpiredToken('expired-access-token');
        $this->newRevokedToken('revoked-access-token');

        $tokens = $this->getRepository()->getActiveTokensForUser('user-id');
        $this->assertCount(1, $tokens, 'Only non-expired, non-revoked tokens must be considered active');
    }

    public function testRevokeToken()
    {
        $this->newValidToken('valid-access-token-id');

        $this->assertFalse($this->isTokenRevoked('valid-access-token-id'));
        $this->revokeToken('valid-access-token-id');
        $this->assertTrue($this->isTokenRevoked('valid-access-token-id'));
    }

    abstract protected function isTokenRevoked($tokenId);

    abstract protected function revokeToken($tokenId);

    abstract protected function getRepository();

    abstract protected function newValidToken($tokenId);

    abstract protected function newExpiredToken($tokenId);

    abstract protected function newRevokedToken($tokenId);
}
