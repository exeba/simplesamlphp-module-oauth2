<?php

namespace SimpleSAML\Test\Module\oauth2\Services;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Services\AttributesProcessor;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use SimpleSAML\Module\oauth2\Services\AuthenticationSourceResolver;
use SimpleSAML\Module\oauth2\Services\SimpleSamlFactory;

class AuthenticationServiceTest extends TestCase
{
    private $userIdAttribute = 'user_id_attribute';
    private $factoryMock;
    private $resolverMock;
    private $processorMock;

    private $authSourceId = 'test_auth_source';
    private $simpleMock;

    private $service;

    public function setUp(): void
    {
        $this->factoryMock = $this->createMock(SimpleSamlFactory::class);
        $this->resolverMock = $this->createMock(AuthenticationSourceResolver::class);
        $this->processorMock = $this->createMock(AttributesProcessor::class);

        $this->simpleMock = $this->createMock(Simple::class);
        $this->factoryMock->method('createSimple')->with($this->authSourceId)->willReturn($this->simpleMock);

        $this->service = new AuthenticationService(
            $this->userIdAttribute,
            $this->factoryMock,
            $this->resolverMock,
            $this->processorMock
        );
    }

    public function testGetUserForRequest()
    {
        $originalAttributes = [
            "$this->userIdAttribute" => ['user_id'],
        ];
        $processedAttributes = [
            'processed' => 'attributes',
        ];

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $this->resolverMock->method('getAuthSourceIdFromRequest')->with($requestMock)->willReturn($this->authSourceId);
        $this->processorMock->method('processAttributes')->with($originalAttributes)->willReturn($processedAttributes);
        $this->simpleMock->method('getAttributes')->willReturn($originalAttributes);

        $user = $this->service->getUserForRequest($requestMock);
        $this->assertEquals('user_id', $user->getIdentifier());
        $this->assertEquals($processedAttributes, $user->getAttributes());
    }

    public function testGetUserForAuthnRequest()
    {
        $originalAttributes = [
            "$this->userIdAttribute" => ['user_id'],
        ];
        $processedAttributes = [
            'processed' => 'attributes',
        ];

        $dummyClient = new ClientEntity();
        $dummyClient->setIdentifier('id');
        $authnRequest = new AuthorizationRequest();
        $authnRequest->setClient($dummyClient);

        $this->resolverMock->method('getAuthSourceIdFromClient')->with($dummyClient)->willReturn($this->authSourceId);
        $this->processorMock->method('processAttributes')->with($originalAttributes)->willReturn($processedAttributes);
        $this->simpleMock->method('getAttributes')->willReturn($originalAttributes);

        $user = $this->service->getUserForAuthnRequest($authnRequest);
        $this->assertEquals('user_id', $user->getIdentifier());
        $this->assertEquals($processedAttributes, $user->getAttributes());
    }

    public function testErrorOnMissingUserId()
    {
        $originalAttributes = [
            'original' => 'attributes',
        ];
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $this->resolverMock->method('getAuthSourceIdFromRequest')->with($requestMock)->willReturn($this->authSourceId);
        $this->simpleMock->method('getAttributes')->willReturn($originalAttributes);

        $this->expectException(\Exception::class);
        $this->service->getUserForRequest($requestMock);
    }
}
