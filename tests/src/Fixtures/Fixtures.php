<?php

namespace SimpleSAML\Test\Module\oauth2\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;

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

    public static function newExpiredAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = self::newAccessToken($identifier, $client, $userIdentifier);
        $token->setExpiryDateTime((new \DateTimeImmutable())->modify('-1 month'));
        $token->setRevoked(false);

        return $token;
    }

    public static function newValidAccessToken(string $identifier, ClientEntity $client, $userIdentifier)
    {
        $token = self::newAccessToken($identifier, $client, $userIdentifier);
        $token->setExpiryDateTime((new \DateTimeImmutable())->modify('+1 month'));
        $token->setRevoked(false);

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
}
