<?php

namespace SimpleSAML\Test\Module\oauth2\Services;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Auth\Source;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Module\oauth2\Services\AttributesProcessor;
use SimpleSAML\Module\oauth2\Services\AttributesUpdater;
use SimpleSAML\Module\oauth2\Services\AuthenticationSourceResolver;

class AttributesUpdaterTest extends TestCase
{

    private $processorMock;
    private $resolverMock;
    private $repositoryMock;

    private $userEntity;
    private $clientEntity;

    private $updater;

    public function setUp(): void
    {
        $this->processorMock = $this->createMock(AttributesProcessor::class);
        $this->resolverMock = $this->createMock(AuthenticationSourceResolver::class);
        $this->repositoryMock = $this->createMock(UserRepository::class);

        $this->userEntity = new UserEntity('user_id');
        $this->clientEntity = new ClientEntity();
        $this->clientEntity->setIdentifier('client_id');

        $this->updater = new AttributesUpdater($this->repositoryMock, $this->resolverMock, $this->processorMock);
    }

    public function testNoOpOnMissingAttributes()
    {
        $this->repositoryMock->expects($this->never())->method($this->anything());
        $this->processorMock->expects($this->never())->method($this->anything());

        $dummySource = $this->createMock(Source::class);
        $this->resolverMock->method('getAuthSourceFromClient')->with($this->clientEntity)->willReturn($dummySource);

        $this->updater->updateAttributes($this->userEntity, $this->clientEntity);
    }

    public function testUpdateAttributesWhenAvailable()
    {
        $originalAttributes = [
            'original' => 'attribute'
        ];
        $processedAttributes = [
            'processed' => 'attribute'
        ];

        $dummySource = $this->createMock(SourceWithAttributes::class);
        $this->resolverMock->method('getAuthSourceFromClient')->with($this->clientEntity)->willReturn($dummySource);
        $dummySource->method('getAttributes')->with($this->userEntity->getIdentifier())->willReturn($originalAttributes);
        $this->processorMock->method('processAttributes')->with($originalAttributes)->willReturn($processedAttributes);
        $this->repositoryMock->expects($this->once())->method('insertOrUpdate')->with($this->userEntity);

        $this->updater->updateAttributes($this->userEntity, $this->clientEntity);

        $this->assertEquals($processedAttributes, $this->userEntity->getAttributes(),
        'UserEntity attributes must be updated');
    }
}