<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use SimpleSAML\Module\oauth2\Entity\AuthCodeEntity;

class AuthCodeRepository extends BaseTokenRepository implements ExtendedAuthCodeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getRepository(AuthCodeEntity::class));
    }

    public function findByIdentifier($identifier):? AuthCodeEntity
    {
        return parent::findByIdentifier($identifier);
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
        return $this->isTokenRevoked($codeId);
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
