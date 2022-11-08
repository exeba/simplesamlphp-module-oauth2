<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\AuthCodeEntity;

interface ExtendedAuthCodeRepository extends AuthCodeRepositoryInterface
{
    public function findByIdentifier($identifier): ?AuthCodeEntity;
}
