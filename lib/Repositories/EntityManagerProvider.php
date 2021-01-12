<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use SimpleSAML\Configuration;

abstract class EntityManagerProvider
{

    private static $em;

    public static function getEntityManager()
    {
        if (!self::$em) {
            self::$em = self::buildEntityManager();
        }

        return self::$em;
    }

    private static function buildEntityManager()
    {

        $config = Configuration::getOptionalConfig('module_oauth2.php');
        $dbParams = array(
            'url' => $config->getString('oauth2.dbal.url')
        );

        $paths = array(__DIR__."/../../doctrine-mappings");
        $isDevMode = true;

        $config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
        return EntityManager::create($dbParams, $config);
    }

}
