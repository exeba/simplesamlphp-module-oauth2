<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ScopeRepository;
use SimpleSAML\Utils\Config;

class OAuth2AuthorizationServer
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');
        $authCodeDuration = $oauth2config->getString('authCodeDuration');
        $refreshTokenDuration = $oauth2config->getString('refreshTokenDuration');
        $authTokenDuration = $oauth2config->getString('authTokenDuration');

        $privateKey = Config::getCertPath('oauth2_module.pem');
        $publicKey = Config::getCertPath('oauth2_module.crt');

        self::$instance = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            $privateKey,
            $publicKey
        );

        $authCodeGrant = new AuthCodeGrant(
            new AuthCodeRepository(),
            new RefreshTokenRepository(),
            new \DateInterval($authCodeDuration)
        );
        $authCodeGrant->setRefreshTokenTTL(new \DateInterval($refreshTokenDuration)); // refresh tokens will expire after 1 month

        self::$instance->enableGrantType(
            $authCodeGrant,
            new \DateInterval($authTokenDuration)
        );

        $implicitGrant = new ImplicitGrant(new \DateInterval($authTokenDuration));

        self::$instance->enableGrantType(
            $implicitGrant,
            new \DateInterval($authTokenDuration)
        );

        return self::$instance;
    }
}