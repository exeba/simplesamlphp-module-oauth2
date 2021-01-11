<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Repositories;

use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;

class AccessTokenRepository extends AbstractDBALRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $scopes = [];
        foreach ($accessTokenEntity->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        $this->conn->insert(
            $this->getTableName(),
            [
                'id' => $accessTokenEntity->getIdentifier(),
                'scopes' => $scopes,
                'expires_at' => $accessTokenEntity->getExpiryDateTime(),
                'user_id' => $accessTokenEntity->getUserIdentifier(),
                'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            ], [
                'string',
                'json_array',
                'datetime',
                'string',
                'string',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $this->conn->update($this->getTableName(), ['is_revoked' => true], ['id' => $tokenId]);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->conn->fetchOne(
            'SELECT is_revoked FROM '.$this->getTableName().' WHERE id = ?',
            [$tokenId]
        );
    }
    
    public function getActiveTokensForUser($userId)
    {
        $accessTokens = $this->conn->fetchAllAssociative(
            'SELECT * FROM '.$this->getTableName().' WHERE user_id = ? AND expires_at > ?',
            [ $userId, new \DateTime() ],
            [ 'string', 'datetime' ]);

        return $accessTokens;
    }

    public function removeExpiredAccessTokens()
    {
        $this->conn->executeStatement(
            'DELETE FROM '.$this->getTableName().' WHERE expires_at < ?',
            [new DateTime()],
            ['datetime']
        );
    }

    public function getTableName()
    {
        return $this->applyPrefix('oauth2_accesstoken');
    }
}
