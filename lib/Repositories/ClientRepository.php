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

use InvalidArgumentException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use mysql_xdevapi\Exception;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Utils\Random;

class ClientRepository extends AbstractDBALRepository implements ClientRepositoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $entity = $this->getClientEntity($clientIdentifier);

        if (!$entity) {
            return false;
        }
        if ($clientSecret && $clientSecret !== $entity->getSecret()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        /** @var ClientEntity $entity */
        $entity = $this->find($clientIdentifier);
        if (!$entity) {
            return null;
        }

        return $this->mapToClass($entity);
    }

    private function find($clientIdentifier)
    {
        return $this->conn->fetchAssociative(
            'SELECT * FROM '.$this->getTableName().' WHERE id = ?',
            [ $clientIdentifier ], [ 'string' ]);
    }

    private function mapToClass($entityRow): ClientEntity
    {
        $client = new ClientEntity();
        $client->setIdentifier($entityRow['id']);
        $client->setName($entityRow['name']);
        $client->setDescription($entityRow['description']);
        $client->setRedirectUri($this->conn->convertToPHPValue($entityRow['redirect_uri'], 'json_array'));
        //$client['scopes'] = $this->conn->convertToPHPValue($client['scopes'], 'json_array');
        $client->setSecret($entityRow['secret']);
        $client->setAuthSource($entityRow['auth_source']);

        return $client;
    }

    public function persistNewClient(ClientEntity $client)
    {
        $this->conn->insert($this->getTableName(), [
            'id' => $client->getIdentifier(),
            'secret' => $client->getSecret(),
            'name' => $client->getName(),
            'description' => $client->getDescription(),
            'auth_source' => $client->getAuthSource(),
            'redirect_uri' => $client->getRedirectUri(),
            'scopes' => ['basic'],
        ], [
            'string',
            'string',
            'string',
            'string',
            'string',
            'json_array',
            'json_array',
        ]);
    }

    public function updateClient(ClientEntity $client)
    {
        $this->conn->update($this->getTableName(), [
            'name' => $client->getName(),
            'description' => $client->getDescription(),
            'auth_source' => $client->getAuthSource(),
            'redirect_uri' => $client->getRedirectUri(),
            'scopes' => ['basic'],
        ], [
            'id' => $client->getIdentifier(),
        ], [
            'string',
            'string',
            'string',
            'json_array',
            'json_array',
        ]);
    }

    public function delete($clientIdentifier)
    {
        $this->conn->delete($this->getTableName(), [
            'id' => $clientIdentifier,
        ], [ 'string' ]);
    }

    /**
     * @return ClientEntity[]
     */
    public function findAll()
    {
        $clients = $this->conn->fetchAllAssociative(
            'SELECT * FROM '.$this->getTableName()
        );

        return array_map([$this, 'mapToClass'], $clients);
    }

    public function getTableName()
    {
        return $this->applyPrefix('oauth2_client');
    }

    public function restoreSecret($clientIdentifier)
    {
        $secret = Random::generateID();
        $this->conn->update($this->getTableName(), [
            'secret' => $secret,
        ], [
            'id' => $clientIdentifier,
        ], [
            'string',
        ]);
    }
}
