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

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Utils\Random;

class ClientRepository implements ClientRepositoryInterface
{

    private $entityManager;
    private $objectManager;

    public function __construct()
    {
        $this->entityManager = EntityManagerProvider::getEntityManager();
        $this->objectManager = $this->entityManager->getRepository(ClientEntity::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $entity = $this->getClientEntity($clientIdentifier);

        return $entity && $clientSecret === $entity->getSecret();
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        return $this->objectManager->find($clientIdentifier);
    }

    public function persistNewClient(ClientEntity $client)
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush($client);
    }

    public function updateClient(ClientEntity $client)
    {
        $oldClient = $this->getClientEntity($client->getIdentifier());
        $oldClient->setName($client->getName());
        $oldClient->setDescription($client->getDescription());
        $oldClient->setAuthSource($client->getAuthSource());
        $oldClient->setRedirectUri($client->getRedirectUri());
        $oldClient->setScopes($client->getScopes());
        $this->entityManager->flush();
    }

    public function delete($clientIdentifier)
    {
        $client = $this->getClientEntity($clientIdentifier);
        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }

    /**
     * @return ClientEntity[]
     */
    public function findAll()
    {
        return $this->objectManager->findAll();
    }

    public function restoreSecret($clientIdentifier)
    {
        $client = $this->getClientEntity($clientIdentifier);
        $client->setSecret(Random::generateID());
        $this->entityManager->flush();
    }
}
