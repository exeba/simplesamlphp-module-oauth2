<?php

namespace SimpleSAML\Test\Module\oauth2;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Kernel;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\Controller\AccessTokenRequestHandler;
use SimpleSAML\Module\oauth2\Controller\NewClientHandler;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DependencyInjectionTest extends TestCase
{

    private $container;

    protected function setUp(): void
    {
        Configuration::setConfigDir(dirname(__DIR__) . '/workdir/config');

        $ken = new TestKernel('oauth2');
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
            'RevokeTokensHandler'
        ];

        foreach ($handlers as $handler) {
            $this->assertNotNull($this->container->get($handler));
        }

    }
}