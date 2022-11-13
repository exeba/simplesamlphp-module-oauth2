<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientRepositoryTest extends KernelTestCase
{
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->repository = $kernel->getContainer()->get(ClientRepository::class);
    }

    private function getRepository(): ClientRepository
    {
        return $this->repository;
    }

    public function testPersistNewClient()
    {
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier('test-client');
        $clientEntity->setName('Test Client');
        $clientEntity->setSecret('test-client-secret');
        $clientEntity->setScopes([]);
        $clientEntity->setAuthSource('test-auth-source');
        $clientEntity->setConfidential(true);
        $clientEntity->setRedirectUri('http://localhost/redirct/uri');

        $this->getRepository()->persistNewClient($clientEntity);
    }
}
