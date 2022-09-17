<?php

namespace SimpleSAML\Test\Module\oauth2\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class DummyScopeRepository implements ScopeRepositoryInterface
{

    private $scopesById;

    public function __construct(ScopeEntityInterface ...$scopes)
    {
        foreach ($scopes as $scope) {
            $this->scopesById[$scope->getIdentifier()] = $scope;
        }
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->scopesById[$identifier];
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        // TODO: Implement finalizeScopes() method.
    }
}