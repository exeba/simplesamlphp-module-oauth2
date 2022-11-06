<?php

namespace SimpleSAML\Module\oauth2\Repositories;

interface ExtendedTokenRepository
{
    public function getTokensForUser($userId);

    public function getActiveTokensForUser($userId);

    public function removeExpiredTokens();

    public function removeRevokedTokens();
}
