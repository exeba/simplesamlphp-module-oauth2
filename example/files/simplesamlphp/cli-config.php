<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'vendor/autoload.php';

$entityManager = SimpleSAML\Module\oauth2\Repositories\EntityManagerProvider::getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
