<?php

namespace SimpleSAML\Module\oauth2\Repositories;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\RefreshTokenEntity;

interface ExtendedRefreshTokenRepository extends RefreshTokenRepositoryInterface
{
    public function findByIdentifier($identifier): ?RefreshTokenEntity;
}
