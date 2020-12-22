<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2;

use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ScopeRepository;
use SimpleSAML\Utils\Config;

class OAuth2AuthorizationServer
{
    private static $instance;
    private static $refreshTokenRepository;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::buildInstance();
        }

        return self::$instance;
    }

    private static  function  buildInstance() {
        $oauth2config = Configuration::getConfig('module_oauth2.php');
        $passPhrase = $oauth2config->getString('pass_phrase', null);

        $privateKeyPath = Config::getCertPath('oauth2_module.pem');
        $privateKey = new CryptKey($privateKeyPath, $passPhrase);
        $encryptionKey = Config::getSecretSalt();

        self::$instance = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            $privateKey,
            $encryptionKey
        );

        self::setupGrants($oauth2config);
    }

    private static function setupGrants($oauth2config)
    {
        self::setupRefreshTokenGrant($oauth2config);
        self::setupAuthCodeGrant($oauth2config);
        self::setupClientCredentialsGrant($oauth2config);
    }

    private static function setupAuthCodeGrant($oauth2config)
    {
        $authCodeDuration = $oauth2config->getString('authCodeDuration');

        $authCodeGrant = new AuthCodeGrant(
            new AuthCodeRepository(),
            self::getRefreshTokenRepository(),
            new DateInterval($authCodeDuration)
        );
        $refreshTokenDuration = $oauth2config->getString('refreshTokenDuration');
        $authCodeGrant->setRefreshTokenTTL(new DateInterval($refreshTokenDuration));

        $accessTokenDuration = $oauth2config->getString('accessTokenDuration');
        self::$instance->enableGrantType(
            $authCodeGrant,
            new DateInterval($accessTokenDuration)
        );
    }

    private static function setupClientCredentialsGrant($oauth2config)
    {
        $accessTokenDuration = $oauth2config->getString('accessTokenDuration');
        self::$instance->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval($accessTokenDuration)
        );
    }

    private static function setupRefreshTokenGrant($oauth2config)
    {
        $refreshTokenDuration = $oauth2config->getString('refreshTokenDuration');
        $refreshTokenGrant = new RefreshTokenGrant(self::getRefreshTokenRepository());
        $refreshTokenGrant->setRefreshTokenTTL(new DateInterval($refreshTokenDuration));

        $authCodeDuration = $oauth2config->getString('authCodeDuration');
        self::$instance->enableGrantType(
            $refreshTokenGrant,
            new DateInterval($authCodeDuration)
        );
    }

    private static function getRefreshTokenRepository()
    {
        if(is_null(self::$refreshTokenRepository)) {
            self::$refreshTokenRepository = new RefreshTokenRepository();
        }

        return self::$refreshTokenRepository;
    }
}
