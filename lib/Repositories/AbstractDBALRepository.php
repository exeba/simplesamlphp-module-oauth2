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
use SimpleSAML\Configuration;
use SimpleSAML\Error\Exception;
use SimpleSAML\Module\dbal\Store\DBAL;
use SimpleSAML\Store;

abstract class AbstractDBALRepository
{
    /**
     * @var DBAL
     */
    protected $store;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * ClientRepository constructor.
     */
    public function __construct()
    {
        $this->config = Configuration::getOptionalConfig('module_oauth2.php');
        $this->store = Store::getInstance();

        if (!$this->store instanceof DBAL) {
            throw new Exception('OAuth2 module: Only DBAL Store is supported');
        }

        $this->conn = $this->store->getConnection();
    }

    abstract public function getTableName();
}
