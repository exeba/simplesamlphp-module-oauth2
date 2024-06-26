<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class BaseRepository
{
    protected $entityManager;
    protected $objectRepository;

    public function __construct(EntityManagerInterface $entityManager, ObjectRepository $objectRepository)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $objectRepository;
    }

    public function findByIdentifier($identifier)
    {
        return $this->objectRepository->find($identifier);
    }

    public function delete($identifier)
    {
        $entity = $this->findByIdentifier($identifier);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function persistToken($token)
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }
}
