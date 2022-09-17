<?php

namespace SimpleSAML\Test\Module\oauth2;

use SimpleSAML\Configuration;
use SimpleSAML\Error\Exception;
use SimpleSAML\Kernel;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;

class TestKernel extends Kernel
{
    public function __construct(string $module)
    {
        parent::__construct($module);
        $this->debug = true;
    }

    public function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $configuration = Configuration::getInstance();
        $baseDir = $configuration->getBaseDir();
        $loader->load($baseDir . '/routing/services/*' . self::CONFIG_EXTS, 'glob');
        $confDir = $this->getModule() . '/routing/services';
        if (is_dir($confDir)) {
            $loader->load($confDir . '/**/*' . self::CONFIG_EXTS, 'glob');
        }

        $container->loadFromExtension('framework', [
            'secret' => Configuration::getInstance()->getString('secretsalt'),
        ]);

        $this->registerModuleControllers($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function registerModuleControllers(ContainerBuilder $container): void
    {
        try {
            $definition = new Definition();
            $definition->setAutowired(true);
            $definition->setPublic(true);

            $controllerDir = $this->getModuleDir() . '/src/Controller';

            if (!is_dir($controllerDir)) {
                return;
            }

            $loader = new DirectoryLoader(
                $container,
                new FileLocator($controllerDir . '/')
            );
            $loader->registerClasses(
                $definition,
                'SimpleSAML\\Module\\' . $this->getModule() . '\\Controller\\',
                $controllerDir . '/*'
            );
        } catch (FileLocatorFileNotFoundException $e) {
        }
    }

    private function getModuleDir()
    {
        return dirname(__DIR__,2);
    }
}