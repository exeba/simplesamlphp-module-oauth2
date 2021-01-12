<?php


namespace SimpleSAML\Module\oauth2\Repositories;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class BaseTokenRepository extends BaseRepository
{

    public function __construct(EntityManagerInterface $entityManager, ObjectRepository $repository)
    {
        parent::__construct($entityManager, $repository);
    }

    public function isTokenRevoked($tokenId)
    {
        $token = $this->findByIdentifier($tokenId);

        return $token ? false : $token->isRevoked();
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
        $query = $this->entityManager->createQuery(
            "delete from {$this->objectRepository->getClassName()} m where m.expiryDateTime < :now");

        return $query->execute([ 'now' => new \DateTimeImmutable() ]);
    }
}
