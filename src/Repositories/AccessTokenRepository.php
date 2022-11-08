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

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;

class AccessTokenRepository extends BaseTokenRepository implements ExtendedAccessTokenRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getRepository(AccessTokenEntity::class));
    }

    public function findByIdentifier($identifier): ?AccessTokenEntity
    {
        return parent::findByIdentifier($identifier);
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

    public function getTokensForUser($userId)
    {
        return $this->objectRepository->createQueryBuilder('token')
            ->where('token.userIdentifier = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()->getResult();
    }

    protected function revokedTokensIdsQueryBuilder()
    {
        return $this->withoutRefreshToken(parent::revokedTokensIdsQueryBuilder());
    }

    protected function expiredTokensIdsQueryBuilder()
    {
        return $this->withoutRefreshToken(parent::expiredTokensIdsQueryBuilder());
    }

    private function withoutRefreshToken($queryBuilder)
    {
        return $queryBuilder
            ->leftJoin('t.refreshToken', 'r')
            ->andWhere('r.identifier IS NULL');
    }
}
