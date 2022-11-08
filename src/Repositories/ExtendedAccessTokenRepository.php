<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\AccessTokenEntity;

interface ExtendedAccessTokenRepository extends AccessTokenRepositoryInterface
{
    public function findByIdentifier($identifier): ?AccessTokenEntity;
}
