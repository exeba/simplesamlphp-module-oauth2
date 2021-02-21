<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;

class RefreshTokenRepository extends BaseTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct()
    {
        $entityManager = EntityManagerProvider::getEntityManager();
        parent::__construct(
            $entityManager,
            $entityManager->getRepository(RefreshTokenEntity::class)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $this->entityManager->persist($refreshTokenEntity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->revokeToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->isTokenRevoked($tokenId);
    }

    public function getRefreshTokenFromAccessToken($accessTokenId)
    {
        return $this->objectRepository->findOneBy([
            'accessToken' => $accessTokenId
            ]);
    }
}
