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
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\AuthCodeEntity;

class AuthCodeRepository extends AbstractDBALRepository implements AuthCodeRepositoryInterface
{
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
        $scopes = [];
        foreach ($authCodeEntity->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        $this->conn->insert($this->getTableName(), [
            'id' => $authCodeEntity->getIdentifier(),
            'scopes' => $scopes,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'redirect_uri' => $authCodeEntity->getRedirectUri(),
        ], [
            'string',
            'json_array',
            'datetime',
            'string',
            'string',
            'string',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        $this->conn->update($this->getTableName(), ['is_revoked' => true], ['id' => $codeId], ['boolean']);
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        $res = $this->conn->fetchFirstColumn('SELECT COUNT(*) FROM '.$this->getTableName().' WHERE is_revoked AND id = ?', [$codeId]);

        return $res[0] > 0;
    }

    public function removeExpiredAuthCodes()
    {
        $this->conn->executeStatement('
                DELETE FROM '.$this->getTableName().'
                WHERE expires_at < ?
            ',
            [
                new DateTime(),
            ],
            [
                'datetime',
            ]
        );
    }

    public function getTableName()
    {
        return $this->applyPrefix('oauth2_authcode');
    }
}
