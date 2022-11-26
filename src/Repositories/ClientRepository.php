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

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Utils\Random;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    private $random;

    public function __construct(
        EntityManagerInterface $em,
        Random $random)
    {
        parent::__construct($em, $em->getRepository(ClientEntity::class));
        $this->random = $random;
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
        return $this->findByIdentifier($clientIdentifier);
    }

    public function persistNewClient(ClientEntity $client)
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    /**
     * @return ClientEntity[]
     */
    public function findAll()
    {
        return $this->objectRepository->findAll();
    }

    public function restoreSecret($clientIdentifier)
    {
        $client = $this->getClientEntity($clientIdentifier);
        $client->setSecret($this->random->generateID());
        $this->entityManager->flush();
    }
}
