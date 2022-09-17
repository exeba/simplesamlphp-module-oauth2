<?php

namespace SimpleSAML\Module\oauth2;

use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use SimpleSAML\Error\Exception;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;

class AuthRequestSerializer
{
    use CryptTrait;

    private static $instance;

    private $clientRepository;
    private $scopeRepository;
    private $userRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ScopeRepositoryInterface $scopeRepository,
        UserRepository $userRepository,
        $encryptionKey
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->userRepository = $userRepository;
        $this->encryptionKey = $encryptionKey;
    }

    public function deserialize($serializedAuthRequest)
    {
        $serializable = json_decode($this->decrypt($serializedAuthRequest), true);
        $authRequest =  new AuthorizationRequest();
        $authRequest->setClient($this->clientRepository->getClientEntity($serializable['client_id']));
        $authRequest->setCodeChallenge($serializable['code_challenge']);
        $authRequest->setCodeChallengeMethod($serializable['code_challenge_method']);
        $authRequest->setGrantTypeId($serializable['grant_type_id']);
        $authRequest->setAuthorizationApproved($serializable['is_approved']);
        $authRequest->setRedirectUri($serializable['redirect_uri']);
        $authRequest->setScopes($this->getScopeEntities($serializable['scope_ids']));
        $authRequest->setState($serializable['state']);
        $user = $this->userRepository->getUserEntity($serializable['user_id']);
        if (!$user) {
            throw new Exception("Unknown user identifier {$serializable['user_id']}");
        }
        $authRequest->setUser($user);

        return $authRequest;
    }

    public function serialize(AuthorizationRequest $authorizationRequest)
    {
        $serializable = [
            'client_id' => $authorizationRequest->getClient()->getIdentifier(),
            'code_challenge' => $authorizationRequest->getCodeChallenge(),
            'code_challenge_method' => $authorizationRequest->getCodeChallengeMethod(),
            'grant_type_id' => $authorizationRequest->getGrantTypeId(),
            'is_approved' => $authorizationRequest->isAuthorizationApproved(),
            'redirect_uri' => $authorizationRequest->getRedirectUri(),
            'scope_ids' => $this->getScopeIdsArray($authorizationRequest),
            'state' => $authorizationRequest->getState(),
            'user_id' => $authorizationRequest->getUser()->getIdentifier(),
        ];

        return $this->encrypt(json_encode($serializable));
    }

    private function getScopeIdsArray(AuthorizationRequest $authorizationRequest)
    {
        return array_map(
            function (ScopeEntityInterface $scope) {
            return $scope->getIdentifier();
        },
            $authorizationRequest->getScopes()
        );
    }

    private function getScopeEntities(array $scopeIds)
    {
        return array_values(array_filter(array_map(function ($scopeId) {
            return $this->scopeRepository->getScopeEntityByIdentifier($scopeId);
        }, $scopeIds)));
    }
}
