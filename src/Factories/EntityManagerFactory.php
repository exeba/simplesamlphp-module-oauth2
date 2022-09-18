<?php

namespace SimpleSAML\Module\oauth2\Factories;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\Setup;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\DoctrineExtensions\TablePrefix;

class EntityManagerFactory
{
    public function buildEntityManager()
    {
        $config = Configuration::getOptionalConfig('module_oauth2.php');

        $prefix = $config->getString('oauth2.dbal.prefix', '');
        $tablePrefix = new TablePrefix($prefix);

        $evm = new EventManager();
        $evm->addEventListener(Events::loadClassMetadata, $tablePrefix);

        $dbParams = [
            'url' => $config->getString('oauth2.dbal.url'),
        ];

        $paths = [__DIR__.'/../../doctrine-mappings'];
        $isDevMode = !Configuration::getInstance()->getBoolean('production', true);

        $config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);

        return EntityManager::create($dbParams, $config, $evm);
    }
}
