<?php

namespace SimpleSAML\Module\oauth2\Repositories;


use Exception;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\UserEntity;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function __construct()
    {
        $entityManager = EntityManagerProvider::getEntityManager();
        parent::__construct(
            $entityManager,
            $entityManager->getRepository(UserEntity::class));
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        throw new Exception('Not supported');
    }

    public function getUser($userIdentifier)
    {
        return $this->findByIdentifier($userIdentifier);
    }

    public function delete($userIdentifier)
    {
        $this->conn->delete($this->getTableName(), [
            'id' => $userIdentifier,
        ]);
    }

    public function insertOrCreate(UserEntity $user)
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
