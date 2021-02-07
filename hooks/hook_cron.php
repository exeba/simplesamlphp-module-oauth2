<?php

use SimpleSAML\Configuration;;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;

function oauth2_hook_cron(&$croninfo)
{
    assert('is_array($croninfo)');
    assert('array_key_exists("summary", $croninfo)');
    assert('array_key_exists("tag", $croninfo)');

    $oauth2config = Configuration::getOptionalConfig('module_oauth2.php');

    if (is_null($oauth2config->getValue('cron_tag', 'hourly'))) {
        return;
    }
    if ($oauth2config->getValue('cron_tag', null) !== $croninfo['tag']) {
        return;
    }

    try {
        $accessTokenRepository = new AccessTokenRepository();
        $accessTokenRepository->removeExpiredTokens();

        $authTokenRepository = new AuthCodeRepository();
        $authTokenRepository->removeExpiredTokens();

        $refreshTokenRepository = new RefreshTokenRepository();
        $refreshTokenRepository->removeExpiredTokens();

        $croninfo['summary'][] = 'OAuth2 clean up. Removed expired entries from OAuth2 storage.';
    } catch (Exception $e) {
        $message = 'OAuth2 clean up cron script failed: '.$e->getMessage();
        SimpleSAML\Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
