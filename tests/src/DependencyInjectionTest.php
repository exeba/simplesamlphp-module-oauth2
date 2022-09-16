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
    private $loader;

    protected function setUp(): void
    {
        Configuration::setConfigDir(dirname(__DIR__).'/config');
        $this->container = $this->initContainer();
    }

    private function initContainer() {
        $container = new ContainerBuilder();
        $this->registerModuleControllers($container);
        $this->registerServices($container);
        $container->compile();

        return $container;
    }

    private function registerModuleControllers(ContainerBuilder $container)
    {
        $definition = new Definition();
        $definition->setAutowired(true);
        $definition->setPublic(true);

        $controllerDir = dirname(dirname(__DIR__)). '/src/Controller';

        $loader = new DirectoryLoader(
            $container,
            new FileLocator($controllerDir . '/')
        );
        $loader->registerClasses(
            $definition,
            'SimpleSAML\\Module\\oauth2\\Controller\\',
            $controllerDir . '/*'
        );
    }

    private function registerServices(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load(dirname(dirname(__DIR__)).'/routing/services/services.yml');
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