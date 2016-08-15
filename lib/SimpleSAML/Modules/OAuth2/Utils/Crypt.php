<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Utils;


use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use SimpleSAML\Utils\Config;

class Crypt
{
    use CryptTrait;

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $privateKey = Config::getCertPath('oauth2_module.pem');
        $publicKey = Config::getCertPath('oauth2_module.crt');

        self::$instance = new Crypt();

        self::$instance->setPrivateKey(new CryptKey($privateKey));
        self::$instance->setPublicKey(new CryptKey($publicKey));

        return self::$instance;
    }

    public function cryptAttributes(array $attributes)
    {
        return $this->encrypt(json_encode($attributes));
    }

    public function decryptAttributes($message)
    {
        return json_decode($this->decrypt($message), true);
    }
}