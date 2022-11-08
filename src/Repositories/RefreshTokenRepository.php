<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;

class RefreshTokenRepository extends BaseTokenRepository implements ExtendedRefreshTokenRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getRepository(RefreshTokenEntity::class));
    }

    public function findByIdentifier($identifier): ?RefreshTokenEntity
    {
        return parent::findByIdentifier($identifier);
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
            'accessToken' => $accessTokenId,
            ]);
    }
}
