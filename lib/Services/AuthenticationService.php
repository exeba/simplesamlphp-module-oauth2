<?php


namespace SimpleSAML\Module\oauth2\Services;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;

class AuthenticationService
{

    private $clientRepository;
    private $userIdAttribute;
    private $defaultAuthenticationSource;

    public function __construct(
            ClientRepository $clientRepository,
            $userIdAttribute,
            $defaultAuthenticationSource)
    {
        $this->clientRepository = $clientRepository;
        $this->userIdAttribute = $userIdAttribute;
        $this->defaultAuthenticationSource = $defaultAuthenticationSource;
    }

    public function getUserEntity(ServerRequestInterface $request): UserEntity
    {
        $auth = new Simple($this->getAuthenticationSourceId($request));
        $auth->requireAuth();

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    private function buildUserFromAttributes($attributes): UserEntity
    {
        if (!isset($attributes[$this->userIdAttribute])) {
            throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
        }
        $userid = $attributes[$this->userIdAttribute][0];

        $user = new UserEntity($userid);
        $user->setAttributes($attributes);

        return $user;
    }

    private function getAuthenticationSourceId(ServerRequestInterface $request): string
    {
        $clientEntity = $this->getClientEntityOrThrow($request);

        return $clientEntity->getAuthSource() ?? $this->defaultAuthenticationSource;
    }

    private function getClientEntityOrThrow(ServerRequestInterface $request): ClientEntityInterface
    {
        $clientId = $this->getClientIdOrThrow($request);
        $clientEntity = $this->clientRepository->getClientEntity($clientId);
        if (is_null($clientEntity)) {
            throw new BadRequest("Client does not exist");
        }

        return $clientEntity;
    }
    private function getClientIdOrThrow(ServerRequestInterface $request): string
    {
        $parameters = $request->getQueryParams();
        $clientId = $parameters['client_id'] ?? null;
        if (is_null($clientId)) {
            throw new BadRequest("Missing 'client_id' query param");
        }

        return $clientId;
    }

}
