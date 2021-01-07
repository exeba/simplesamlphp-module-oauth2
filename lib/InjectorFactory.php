<?php


namespace SimpleSAML\Module\oauth2;


use Laminas\Di\Injector;
use Laminas\Di\Config as DIConfig;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Diactoros\ResponseFactory;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseFactoryInterface;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\ScopeRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use SimpleSAML\Utils\Config as SAMLConfig;

class InjectorFactory
{

    public static function getInjector() {
        return new Injector(new DIConfig([
            'preferences' => [
                ResponseFactoryInterface::class => ResponseFactory::class,
                ClientRepositoryInterface::class => ClientRepository::class,
                UserRepositoryInterface::class => UserRepository::class,
                ScopeRepositoryInterface::class => ScopeRepository::class,
                AccessTokenRepositoryInterface::class => AccessTokenRepository::class,
                AuthCodeRepositoryInterface::class => AuthCodeRepository::class,
                RefreshTokenRepositoryInterface::class => RefreshTokenRepository::class,
            ],
            'types' => [
                AuthorizationServerConfigurator::class => [
                    'parameters' => [
                        'refreshTokenDuration' => new TypeInjection('AuthCodeDuration'),
                        'accessTokenDuration' => new TypeInjection('AuthCodeDuration'),
                    ],
                ],

                AuthorizationServer::class => [
                    'parameters' => [
                        'privateKey' => new TypeInjection('OAuth2.PrivateKey'),
                        'encryptionKey' => SAMLConfig::getSecretSalt(),
                    ],
                ],

                ResourceServer::class => [
                    'parameters' => [
                        'publicKey' => new TypeInjection('OAuth2.PublicKey'),
                    ]
                ],

                AuthenticationService::class => [
                    'parameters' => [
                        'userIdAttribute' => Configuration::getConfig('module_oauth2.php')->getString('useridattr'),
                        'defaultAuthenticationSource' => Configuration::getConfig('module_oauth2.php')->getString('auth'),
                    ],
                ],

                AuthCodeGrant::class => [
                    'preferences' => [
                        \DateInterval::class => 'AuthCodeDuration',
                    ],
                ],

                'AuthCodeDuration' => [
                    'typeOf' => \DateInterval::class,
                    'parameters' => [
                        'interval_spec' => Configuration::getConfig('module_oauth2.php')->getString('authCodeDuration')
                    ]
                ],

                'RefreshTokenDuration' => [
                    'typeOf' => \DateInterval::class,
                    'parameters' => [
                        'interval_spec' => Configuration::getConfig('module_oauth2.php')->getString('refreshTokenDuration')
                    ]
                ],

                'AccessTokenDuration' => [
                    'typeOf' => \DateInterval::class,
                    'parameters' => [
                        'interval_spec' => Configuration::getConfig('module_oauth2.php')->getString('accessTokenDuration')
                    ]
                ],

                AuthRequestSerializer::class => [
                    'parameters' => [
                        'encryptionKey' => SAMLConfig::getSecretSalt(),
                    ],
                ],

                'OAuth2.PrivateKey' => [
                    'typeOf' => CryptKey::class,
                    'parameters' => [
                        'keyPath' => SAMLConfig::getCertPath('oauth2_module.pem'),
                        'passPhrase' => Configuration::getConfig('module_oauth2.php')->getString('pass_phrase', null),
                    ],
                ],

                'OAuth2.PublicKey' => [
                    'typeOf' => CryptKey::class,
                    'parameters' => [
                        'keyPath' => SAMLConfig::getCertPath('oauth2_module.crt'),
                    ],
                ],


            ],
        ]));
    }
}
