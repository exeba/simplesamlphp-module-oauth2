<?php

namespace SimpleSAML\Test\Module\oauth2\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;

class Fixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(self::newClientEntity('client1', true));
        $manager->persist(self::newClientEntity('client2', true));
        $manager->persist(self::newClientEntity('client3', false));

        $manager->flush();
    }

    public static function newClientEntity(string $identifier, bool $confidential)
    {
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($identifier);
        $clientEntity->setName("Name of $identifier");
        $clientEntity->setSecret("secret-$identifier");
        $clientEntity->setScopes([]);
        $clientEntity->setAuthSource('test-auth-source');
        $clientEntity->setConfidential($confidential);
        $clientEntity->setRedirectUri('http://localhost/clients/redirct/uri/'.$identifier);

        return $clientEntity;
    }

    public static function newRevokedAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = self::newAccessToken($identifier, $client, $userIdentifier);
        self::nonExpired($token);
        self::revoked($token);

        return $token;
    }

    public static function newExpiredAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = self::newAccessToken($identifier, $client, $userIdentifier);
        self::expired($token);
        self::nonRevoked($token);

        return $token;
    }

    public static function newValidAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = self::newAccessToken($identifier, $client, $userIdentifier);
        self::nonExpired($token);
        self::nonRevoked($token);

        return $token;
    }

    private static function newAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = new AccessTokenEntity();
        $token->setIdentifier($identifier);
        $token->setClient($client);
        $token->setUserIdentifier($userIdentifier);

        return $token;
    }

    public static function newRevokedRefreshToken(string $identifier, AccessTokenEntity $accessToken)
    {
        $token = self::newRefreshToken($identifier, $accessToken);
        self::nonExpired($token);
        self::revoked($token);

        return $token;
    }

    public static function newExpiredRefreshToken(string $identifier, AccessTokenEntity $accessToken)
    {
        $token = self::newRefreshToken($identifier, $accessToken);
        self::expired($token);
        self::nonRevoked($token);

        return $token;
    }

    public static function newValidRefreshToken(string $identifier, AccessTokenEntity $accessToken)
    {
        $token = self::newRefreshToken($identifier, $accessToken);
        self::nonExpired($token);
        self::nonRevoked($token);

        return $token;
    }

    private static function newRefreshToken(string $identifier, AccessTokenEntity $accessToken)
    {
        $token = new RefreshTokenEntity();
        $token->setIdentifier($identifier);
        $token->setAccessToken($accessToken);

        return $token;
    }

    private static function nonExpired($token)
    {
        $token->setExpiryDateTime((new \DateTimeImmutable())->modify('+1 month'));
    }

    private static function expired($token)
    {
        $token->setExpiryDateTime((new \DateTimeImmutable())->modify('-1 month'));
    }

    private static function revoked($token)
    {
        $token->setRevoked(true);
    }

    private static function nonRevoked($token)
    {
        $token->setRevoked(false);
    }
}
