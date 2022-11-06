<?php

namespace SimpleSAML\Module\oauth2\src\Services;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;
use SimpleSAML\Module\oauth2\Entity\AuthCodeEntity;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;
use SimpleSAML\Module\oauth2\Repositories\ExtendedAccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ExtendedAuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\ExtendedRefreshTokenRepository;
use SimpleSAML\Module\oauth2\Services\RevokerService;

class RevokerServiceTest extends TestCase
{
    private $accessTokenRepoMock;
    private $refreshTokenRepoMock;
    private $authCodeRepoMock;

    private $revokerService;

    protected function setUp(): void
    {
        $this->accessTokenRepoMock = $this->createMock(ExtendedAccessTokenRepository::class);
        $this->refreshTokenRepoMock = $this->createMock(ExtendedRefreshTokenRepository::class);
        $this->authCodeRepoMock = $this->createMock(ExtendedAuthCodeRepository::class);

        $this->revokerService = new RevokerService(
            $this->accessTokenRepoMock,
            $this->authCodeRepoMock,
            $this->refreshTokenRepoMock
        );
    }

    public function testRevokeAccessTokenUnallowed()
    {
        $accessToken = $this->newAccessToken('tokenId', 'owner');
        $this->accessTokenRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($accessToken);

        $this->accessTokenRepoMock->expects($this->never())->method('revokeAccessToken');
        $this->expectException(\Exception::class);

        $this->revokerService->revokeAccessTokenIfOwner('tokenId', 'notOwner');
    }

    public function testRevokeAccessTokenAllowed()
    {
        $accessToken = $this->newAccessToken('tokenId', 'owner');
        $this->accessTokenRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($accessToken);

        $this->accessTokenRepoMock->expects($this->once())->method('revokeAccessToken')->with('tokenId');

        $this->revokerService->revokeAccessTokenIfOwner('tokenId', 'owner');
    }

    public function testRevokeRefreshTokenUnallowed()
    {
        $refreshToken = $this->newRefreshToken('tokenId', 'owner');
        $this->refreshTokenRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($refreshToken);

        $this->refreshTokenRepoMock->expects($this->never())->method('revokeRefreshToken');
        $this->expectException(\Exception::class);

        $this->revokerService->revokeRefreshTokenIfOwner('tokenId', 'notOwner');
    }

    public function testRevokeRefreshTokenAllowed()
    {
        $refreshToken = $this->newRefreshToken('tokenId', 'owner');
        $this->refreshTokenRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($refreshToken);

        $this->refreshTokenRepoMock->expects($this->once())->method('revokeRefreshToken')->with('tokenId');

        $this->revokerService->revokeRefreshTokenIfOwner('tokenId', 'owner');
    }

    public function testRevokeAuthCodeUnallowed()
    {
        $authCode = $this->newAuthCode('tokenId', 'owner');
        $this->authCodeRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($authCode);

        $this->authCodeRepoMock->expects($this->never())->method('revokeAuthCode');
        $this->expectException(\Exception::class);

        $this->revokerService->revokeAuthCodeIfOwner('tokenId', 'notOwner');
    }

    public function testRevokeAuthCodeAllowed()
    {
        $authCode = $this->newAuthCode('tokenId', 'owner');
        $this->authCodeRepoMock->method('findByIdentifier')->with('tokenId')->willReturn($authCode);

        $this->authCodeRepoMock->expects($this->once())->method('revokeAuthCode')->with('tokenId');

        $this->revokerService->revokeAuthCodeIfOwner('tokenId', 'owner');
    }

    private function newAuthCode($tokenId, $owner)
    {
        $authCode = new AuthCodeEntity();
        $authCode->setIdentifier($tokenId);
        $authCode->setUserIdentifier($owner);

        return $authCode;
    }

    private function newAccessToken($tokenId, $owner)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setIdentifier($tokenId);
        $accessToken->setUserIdentifier($owner);

        return $accessToken;
    }

    private function newRefreshToken($tokenId, $owner)
    {
        $refreshToken = new RefreshTokenEntity();
        $refreshToken->setIdentifier($tokenId);
        $refreshToken->setAccessToken($this->newAccessToken('at'.$tokenId, $owner));

        return $refreshToken;
    }
}
