<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\UserEntity;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getRepository(UserEntity::class));
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        throw new Exception('Not supported');
    }

    public function getUser($userIdentifier): UserEntity
    {
        return $this->findByIdentifier($userIdentifier);
    }

    public function insertOrUpdate(UserEntity $user)
    {
        $oldUser = $this->findByIdentifier($user->getIdentifier());
        if (is_null($oldUser)) {
            $this->entityManager->persist($user);
        } else {
            $oldUser->setAttributes($user->getAttributes());
            $oldUser->setUpdatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->flush();
    }
}
