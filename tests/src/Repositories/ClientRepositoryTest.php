<?php

namespace SimpleSAML\Module\oauth2\src\Repositories;

use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Test\Module\oauth2\Fixtures\Fixtures;
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
        $clientEntity = Fixtures::newClientEntity('new-client', true);
        $this->getRepository()->persistNewClient($clientEntity);

        $retrievedClientEntity = $this->getRepository()->findByIdentifier('new-client');

        $this->assertEquals($clientEntity, $retrievedClientEntity);
    }

    public function testRestoreSecret()
    {
        $clientEntity = Fixtures::newClientEntity('new-client', true);
        $oldSecret = $clientEntity->getSecret();
        $this->getRepository()->persistNewClient($clientEntity);

        $this->getRepository()->restoreSecret('new-client');

        $retrievedClientEntity = $this->getRepository()->findByIdentifier('new-client');
        $newSecret = $retrievedClientEntity->getSecret();

        $this->assertNotEquals($oldSecret, $newSecret);
    }
}
