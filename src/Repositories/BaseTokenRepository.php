<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class BaseTokenRepository extends BaseRepository implements ExtendedTokenRepository
{
    public function __construct(EntityManagerInterface $entityManager, ObjectRepository $repository)
    {
        parent::__construct($entityManager, $repository);
    }

    public function isTokenRevoked($tokenId)
    {
        $token = $this->findByIdentifier($tokenId);

        return $token ? $token->isRevoked() : false;
    }

    public function revokeToken($tokenId)
    {
        $token = $this->findByIdentifier($tokenId);
        if ($token) {
            $token->setRevoked(true);
            $this->entityManager->flush();
        }
    }

    public function removeExpiredTokens()
    {
        $expiredTokenIds = $this->getTokenIds($this->expiredTokensIdsQueryBuilder());

        return $this->removeTokens($expiredTokenIds);
    }

    public function removeRevokedTokens()
    {
        $expiredTokenIds = $this->getTokenIds($this->revokedTokensIdsQueryBuilder());

        return $this->removeTokens($expiredTokenIds);
    }

    protected function getTokenIds($queryBuilder)
    {
        return array_column($queryBuilder->getQuery()->getScalarResult(), 'identifier');
    }

    public function getTokensForUser($userId)
    {
        return $this->objectRepository->createQueryBuilder('token')
            ->where('token.userIdentifier = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()->getResult();
    }

    public function getActiveTokensForUser($userId)
    {
        return $this->objectRepository->createQueryBuilder('token')
            ->where('token.userIdentifier = :userId')
            ->andWhere('token.expiryDateTime > :now')
            ->andWhere('token.isRevoked = 0')
            ->setParameter('userId', $userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()->getResult();
    }

    protected function revokedTokensIdsQueryBuilder()
    {
        return $this->objectRepository->createQueryBuilder('t')
            ->select('t.identifier')
            ->where('t.isRevoked = 1');
    }

    protected function expiredTokensIdsQueryBuilder()
    {
        return $this->objectRepository->createQueryBuilder('t')
            ->select('t.identifier')
            ->where('t.expiryDateTime < :now')
            ->setParameter('now', new \DateTime());
    }

    protected function removeTokens($tokenIds)
    {
        $query = $this->entityManager->createQuery(
            "delete from {$this->objectRepository->getClassName()} t where t.identifier in (:removableIds)"
        );

        return $query->execute(['removableIds' => $tokenIds]);
    }
}
