<?php


namespace SimpleSAML\Module\oauth2\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Repositories\ScopeRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Module\oauth2\Services\AttributesUpdater;

class UserInfoRequestHandler implements RequestHandlerInterface
{
    const USER_ID_ATTRIBUTE_NAME = 'oauth_user_id';
    const SCOPES_ATTRIBUTE_NAME = 'oauth_scopes';
    const CLIENT_ID_ATTRIBUTE_NAME = 'oauth_client_id';

    private $userRepository;
    private $scopeRepository;
    private $clientRepository;
    private $attributesUpdater;

    /* TODO: use interfaces */
    public function __construct(
        UserRepository $userRepository,
        ScopeRepository $scopesRepository,
        ClientRepository $clientRepository,
        AttributesUpdater $attributesUpdater
    )
    {
        $this->userRepository = $userRepository;
        $this->scopeRepository = $scopesRepository;
        $this->clientRepository = $clientRepository;
        $this->attributesUpdater = $attributesUpdater;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->getUserAttributes($request));
    }

    private function getUserAttributes(ServerRequestInterface $request): array
    {
        $client = $this->getClient($request);
        $user = $this->getUser($request);
        $this->attributesUpdater->updateAttributes($user, $client);

        $scopes = $this->getScopes($request);
        $scopesAttributes = $this->getScopesAttributes($scopes);

        return $this->getAttributeValues($user->getAttributes(), $scopesAttributes);
    }

    private function getScopesAttributes($scopes)
    {
        $allAttributes = [];
        foreach ($scopes as $scope) {
            $scopeEntity = $this->scopeRepository->getScopeEntityByIdentifier($scope);
            $attributes = $scopeEntity->getAttributes();
            $allAttributes = array_merge($allAttributes, $attributes);
        }
        $allAttributes = array_unique($allAttributes);
        sort($allAttributes);

        return $allAttributes;
    }

    private function getAttributeValues($userAttributes, $scopeAttributes)
    {
        $filteredAttributes = [];
        foreach ($scopeAttributes as $attribute) {
            if ($this->scopeRepository->isSingleValued($attribute)) {
                $filteredAttributes[$attribute] = $userAttributes[$attribute][0];
            } else {
                $filteredAttributes[$attribute] = $userAttributes[$attribute];
            }
        }

        return $filteredAttributes;
    }

    private function getUser(RequestInterface $request): UserEntity
    {
        $userId = $request->getAttributes()[self::USER_ID_ATTRIBUTE_NAME];

        return $this->userRepository->getUser($userId);
    }

    private function getScopes(RequestInterface $request)
    {
        return $request->getAttributes()[self::SCOPES_ATTRIBUTE_NAME];
    }

    private function getClient(RequestInterface $request): ClientEntity
    {
        $clientId = $request->getAttributes()[self::CLIENT_ID_ATTRIBUTE_NAME];

        return $this->clientRepository->getClientEntity($clientId);
    }
}
