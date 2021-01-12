<?php

namespace SimpleSAML\Module\oauth2\Repositories;


use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\AuthCodeEntity;

class AuthCodeRepository extends BaseTokenRepository implements AuthCodeRepositoryInterface
{

    public function __construct()
    {
        $entityManager = EntityManagerProvider::getEntityManager();
        parent::__construct(
            $entityManager,
            $entityManager->getRepository(AuthCodeEntity::class));
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $this->entityManager->persist($authCodeEntity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        $this->revokeToken($codeId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        $this->isTokenRevoked($codeId);
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
