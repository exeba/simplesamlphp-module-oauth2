<?php

use SimpleSAML\Kernel;
use SimpleSAML\Logger;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;

/**
 * @param array &$croninfo
 *
 * @return void
 */
function oauth2_hook_cron(&$croninfo)
{
    assert('is_array($croninfo)');
    assert('array_key_exists("summary", $croninfo)');
    assert('array_key_exists("tag", $croninfo)');
    
    $kernel = new Kernel('oauth2');
    $kernel->boot();
    $container = $kernel->getContainer();

    try {
        $accessTokenRepository = $container->get(AccessTokenRepository::class);
        $accessTokenRepository->removeExpiredTokens();
        $accessTokenRepository->removeRevokedTokens();

        $authCodeRepository = $container->get(AuthCodeRepository::class);
        $authCodeRepository->removeExpiredTokens();
        $authCodeRepository->removeRevokedTokens();

        $refreshTokenRepository = $container->get(RefreshTokenRepository::class);
        $refreshTokenRepository->removeExpiredTokens();
        $refreshTokenRepository->removeRevokedTokens();

        $croninfo['summary'][] = 'Module `oauth2` clean up. Removed expired and revoked entries from storage.';
    } catch (Exception $e) {
        $message = 'Module `oauth2` clean up cron script failed: ' . $e->getMessage();
        Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
