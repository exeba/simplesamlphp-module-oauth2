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
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
{

    private $scopesConfig;
    private $singleValuedAttributes;

    public function __construct($scopesConfig, $singleValuedAttributes = null)
    {
        $this->scopesConfig = $scopesConfig;
        $this->singleValuedAttributes = $singleValuedAttributes ?? [];
    }

    public function isSingleValued($attribute)
    {
        return in_array($attribute, $this->singleValuedAttributes, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        if ($this->hasScope($identifier)) {
            return $this->buildScopeEntity($identifier);
        }

        return null;
    }

    private function hasScope($scope)
    {
        return false !== array_key_exists($scope, $this->scopesConfig);
    }

    private function buildScopeEntity($scope)
    {
        $scopeConfig = $this->scopesConfig[$scope];

        $scope = new ScopeEntity();
        $scope->setIdentifier($scope);
        $scope->setIcon($scopeConfig['icon']);
        $scope->setDescription($scopeConfig['description']);
        $scope->setAttributes($scopeConfig['attributes']);

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
