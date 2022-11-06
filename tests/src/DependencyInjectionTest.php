<?php

namespace SimpleSAML\Test\Module\oauth2;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;

class DependencyInjectionTest extends TestCase
{
    private $container;

    protected function setUp(): void
    {
        Configuration::setConfigDir(dirname(__DIR__).'/workdir/config');

        $ken = new TestKernel();
        $ken->boot();

        $this->container = $ken->getContainer();
    }

    public function testDI()
    {
        $handlers = [
            'AccessTokenRequestHandler',
            'AuthorizeRequestHandler',
            'AuthorizeChoiceHandler',
            'UserInfoRequestHandler',
            'RegistryIndexHandler',
            'EditClientHandler',
            'NewClientHandler',
            'ShowUserTokensHandler',
            'RevokeTokensHandler',
        ];

        foreach ($handlers as $handler) {
            $this->assertNotNull($this->container->get($handler));
        }
    }
}
