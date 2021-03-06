<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $oauth2config = Configuration::getConfig('module_oauth2.php');

        $scopes = $oauth2config->getArray('scopes');

        if (array_key_exists($identifier, $scopes) === false) {
            return null;
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        $scope->setIcon($scopes[$identifier]['icon']);
        $scope->setDescription($scopes[$identifier]['description']);
        $scope->setAttributes($scopes[$identifier]['attributes']);

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        return $this->commonScopes($scopes, $clientEntity);
    }

    private function commonScopes($requestedScopes, ClientEntity $clientEntity)
    {
        return array_filter($requestedScopes, function ($scope) use (&$clientEntity) {
            return (false !== array_search($scope->getIdentifier(), $clientEntity->getScopes()));
        });
    }
}
