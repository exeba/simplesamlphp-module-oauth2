<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use SimpleSAML\Configuration;
use SimpleSAML\Database;

abstract class AbstractDBALRepository
{
    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var Configuration
     */
    protected $config;


    /**
     * @var Database
     */
    protected $simpleSamlDb;

    /**
     * ClientRepository constructor.
     */
    public function __construct()
    {
        $this->config = Configuration::getOptionalConfig('module_oauth2.php');
        $this->conn = DriverManager::getConnection([
            'url' => $this->config->getString('oauth2.dbal.url')
        ]);
        $this->simpleSamlDb = Database::getInstance();
    }

    protected function applyPrefix($tableName) {
        return $this->simpleSamlDb->applyPrefix($tableName);
    }

    abstract public function getTableName();
}
