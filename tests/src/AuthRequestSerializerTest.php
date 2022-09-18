<?php

namespace SimpleSAML\Test\Module\oauth2;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\ScopeEntity;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;
use SimpleSAML\Test\Module\oauth2\Repositories\DummyScopeRepository;

class AuthRequestSerializerTest extends TestCase
{
    private $authRequest;

    private $serializer;

    protected function setUp(): void
    {
        $scopes = [$this->createScope('id'), $this->createScope('mail')];
        $user = new UserEntity('user_id');
        $client = new ClientEntity();
        $client->setIdentifier('client_id');

        $clientRepoMock = $this->createClientRepoMock($client);
        $userRepoMock = $this->createUserRepoMock($user);
        $scopeRepoMock = new DummyScopeRepository(...$scopes);
        $encryptionKey = 'key';

        $this->serializer = new AuthRequestSerializer(
            $clientRepoMock,
            $scopeRepoMock,
            $userRepoMock,
            $encryptionKey
        );

        $this->authRequest = new AuthorizationRequest();
        $this->authRequest->setClient($client);
        $this->authRequest->setAuthorizationApproved(true);
        $this->authRequest->setCodeChallenge('challenge');
        $this->authRequest->setScopes($scopes);
        $this->authRequest->setCodeChallengeMethod('ccmethod');
        $this->authRequest->setCodeChallenge('challenge');
        $this->authRequest->setState('state');
        $this->authRequest->setRedirectUri('http://127.0.0.1');
        $this->authRequest->setUser($user);
        $this->authRequest->setGrantTypeId('gtype');
    }

    private function createClientRepoMock(ClientEntity $client)
    {
        $clientRepo = $this->createMock(ClientRepositoryInterface::class);
        $clientRepo->method('getClientEntity')
            ->with($client->getIdentifier())
            ->willReturn($client);

        return $clientRepo;
    }

    private function createUserRepoMock(UserEntity $user)
    {
        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('getUserEntity')
            ->with($user->getIdentifier())
             ->willReturn($user);

        return $userRepo;
    }

    private function createScope($scopeId)
    {
        $scope = new ScopeEntity();
        $scope->setIdentifier($scopeId);

        return $scope;
    }

    public function testEqualsAfterSerializationCycle()
    {
        $serialized = $this->serializer->serialize($this->authRequest);
        $deserialized = $this->serializer->deserialize($serialized);

        $this->assertEquals($this->authRequest, $deserialized);
    }
}
