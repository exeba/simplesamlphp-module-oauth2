<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'vendor/autoload.php';

$entityManager = (new SimpleSAML\Module\oauth2\Factories\EntityManagerFactory())->buildEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
