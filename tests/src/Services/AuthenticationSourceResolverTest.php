<?php

namespace SimpleSAML\Module\oauth2\src\Services;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Auth\Source;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Services\AuthenticationSourceResolver;
use SimpleSAML\Module\oauth2\Services\SimpleSamlFactory;

class AuthenticationSourceResolverTest extends TestCase
{
    private $defaultAuthenticationSourceId = 'default_id';
    private $factoryMock;

    private $resolver;

    protected function setUp(): void
    {
        $this->factoryMock = $this->createMock(SimpleSamlFactory::class);

        $this->resolver = new AuthenticationSourceResolver($this->factoryMock, $this->defaultAuthenticationSourceId);
    }

    public function testGetAuthSourceFromClientDefault()
    {
        $client = new ClientEntity();
        $sourceMock = $this->createMock(Source::class);
        $samlMock = $this->createMock(Simple::class);

        $samlMock->method('getAuthSource')->willReturn($sourceMock);
        $this->factoryMock->method('createSimple')
            ->with($this->defaultAuthenticationSourceId)->willReturn($samlMock);

        $resolvedSource = $this->resolver->getAuthSourceFromClient($client);

        $this->assertSame($sourceMock, $resolvedSource);
    }

    public function testGetAuthSourceFromClient()
    {
        $client = new ClientEntity();
        $client->setAuthSource('custom_source');

        $sourceMock = $this->createMock(Source::class);
        $samlMock = $this->createMock(Simple::class);

        $samlMock->method('getAuthSource')->willReturn($sourceMock);
        $this->factoryMock->method('createSimple')
            ->with('custom_source')->willReturn($samlMock);

        $resolvedSource = $this->resolver->getAuthSourceFromClient($client);

        $this->assertSame($sourceMock, $resolvedSource);
    }

    public function testGetAuthSourceFromRequestDefault()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttributes')->willReturn([]);

        $sourceMock = $this->createMock(Source::class);
        $samlMock = $this->createMock(Simple::class);

        $samlMock->method('getAuthSource')->willReturn($sourceMock);
        $this->factoryMock->method('createSimple')
            ->with($this->defaultAuthenticationSourceId)->willReturn($samlMock);

        $resolvedSource = $this->resolver->getAuthSourceFromRequest($request);

        $this->assertSame($sourceMock, $resolvedSource);
    }
}
