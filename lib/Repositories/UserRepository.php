<?php
/*
 * This file is part of the jt2016-uco-spa.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Repositories;

use DateTime;
use Exception;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\UserEntity;

class UserRepository extends AbstractDBALRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        throw new Exception('Not supported');
    }

    public function delete($userIdentifier)
    {
        $this->conn->delete($this->getTableName(), [
            'id' => $userIdentifier,
        ]);
    }

    public function insertOrCreate(UserEntity $user)
    {
        if (0 === $this->updateUser($user)) {
            $this->persistNewUser($user);
        }
    }

    public function updateUser(UserEntity $user)
    {
        return $this->conn->update($this->getTableName(),
            [
                'attributes' => $user->getAttributes(),
                'updated_at' => new \DateTime()
            ], [
                'id' => $user->getIdentifier()
            ], [
                'json_array',
                'datetime',
            ]
        );
    }

    public function persistNewUser(UserEntity $user)
    {
        $now = new DateTime();

        $this->conn->insert($this->getTableName(),
            [
                'id' => $user->getIdentifier(),
                'attributes' => $user->getAttributes(),
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'string',
                'json_array',
                'datetime',
                'datetime',
            ]
        );
    }

    public function getAttributes($userId)
    {
        $attributes = $this->conn->fetchOne(
            'SELECT attributes FROM '.$this->getTableName().' WHERE id = ?',
            [$userId]
        );

        return $this->conn->convertToPHPValue($attributes, 'json_array');
    }

    public function getTableName()
    {
        return $this->applyPrefix('oauth2_user');
    }
}
