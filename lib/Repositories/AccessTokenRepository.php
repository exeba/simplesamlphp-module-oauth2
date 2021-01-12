<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Repositories;

use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use SimpleSAML\Error\Exception;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;

class AccessTokenRepository extends BaseTokenRepository implements AccessTokenRepositoryInterface
{

    public function __construct()
    {
        $entityManager = EntityManagerProvider::getEntityManager();
        parent::__construct(
            $entityManager,
            $entityManager->getRepository(AccessTokenEntity::class));
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->entityManager->persist($accessTokenEntity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $this->revokeToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->isTokenRevoked($tokenId);
    }

    public function getActiveTokensForUser($userId)
    {
        return $this->objectRepository->createQueryBuilder('token')
            ->where('token.userIdentifier = :userId')
            ->andWhere('token.expiryDateTime > :now')
            ->setParameter('userId', $userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()->getResult();
    }

}
