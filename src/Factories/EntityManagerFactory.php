<?php

namespace SimpleSAML\Module\oauth2\Factories;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMSetup;
use SimpleSAML\Configuration as SimpleSAMLConfiguration;
use SimpleSAML\Module\oauth2\DoctrineExtensions\TablePrefix;

class EntityManagerFactory
{
    private $moduleConfig;
    private $isProductionMode;

    public function __construct(SimpleSAMLConfiguration $moduleConfig, bool $isProductionMode)
    {
        $this->moduleConfig = $moduleConfig;
        $this->isProductionMode = $isProductionMode;
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

        return ORMSetup::createXMLMetadataConfiguration($paths, !$this->isProductionMode);
    }

    private function buildEventManager(): EventManager
    {
        $evm = new EventManager();
        $evm->addEventListener(Events::loadClassMetadata, $this->buildTablePrefix());

        return $evm;
    }

    private function buildTablePrefix()
    {
        $prefix = $this->moduleConfig->getOptionalString('oauth2.dbal.prefix', '');

        return new TablePrefix($prefix);
    }
}
