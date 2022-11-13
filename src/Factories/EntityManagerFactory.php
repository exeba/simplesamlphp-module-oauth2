<?php

namespace SimpleSAML\Module\oauth2\Factories;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\Middleware;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\PostConnectEventListener;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Events as DBALEvents;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events as ORMEvents;
use Doctrine\ORM\ORMSetup;
use SimpleSAML\Configuration as SimpleSAMLConfiguration;
use SimpleSAML\Module\oauth2\DoctrineExtensions\TablePrefix;
use Symfony\Component\HttpKernel\KernelInterface;

class EntityManagerFactory
{
    private $moduleConfig;
    private $isProductionMode;
    private $environment;
    private $proxyDir;

    public function __construct(
        SimpleSAMLConfiguration $moduleConfig,
        bool $isProductionMode,
        KernelInterface $kernel)
    {
        $this->moduleConfig = $moduleConfig;
        $this->isProductionMode = $isProductionMode;
        $this->environment = $kernel->getEnvironment();
        $this->proxyDir = $kernel->getCacheDir().DIRECTORY_SEPARATOR.'doctrine_proxies';
    }

    public function buildEntityManager()
    {
        return EntityManager::create(
            $this->buildDbParams(),
            $this->buildMetadataConfiguration(),
            $this->buildEventManager());
    }

    private function buildDbParams()
    {
        return [
            'url' => $this->moduleConfig->getString('oauth2.dbal.url'),
        ];
    }

    private function buildMetadataConfiguration(): ORMConfiguration
    {
        $paths = [__DIR__.'/../../doctrine-mappings'];
        $config = ORMSetup::createXMLMetadataConfiguration($paths, !$this->isProductionMode, $this->proxyDir);
        if ($this->isTestEnv()) {
            $config->setMiddlewares([new Middleware()]);
        }

        return $config;
    }

    private function buildEventManager(): EventManager
    {
        $evm = new EventManager();
        $evm->addEventListener(ORMEvents::loadClassMetadata, $this->buildTablePrefix());
        if ($this->isTestEnv()) {
            $evm->addEventListener(DBAlEvents::postConnect, new PostConnectEventListener());
        }

        return $evm;
    }

    private function buildTablePrefix()
    {
        $prefix = $this->moduleConfig->getOptionalString('oauth2.dbal.prefix', '');

        return new TablePrefix($prefix);
    }

    private function isTestEnv()
    {
        return 'test' === $this->environment;
    }
}
