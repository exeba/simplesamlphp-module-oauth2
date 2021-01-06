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
    private $oauth2config;

    public function __construct(ClientRepository $clientRepository, Configuration $oauth2config)
    {
        $this->clientRepository = $clientRepository;
        $this->oauth2config = $oauth2config;
    }

    public function getUserEntity(ServerRequestInterface $request): UserEntity
    {
        $auth = new Simple($this->getAuthenticationSourceId($request));
        $auth->requireAuth();

        return $this->buildUserFromAttributes($auth->getAttributes());
    }

    private function buildUserFromAttributes($attributes): UserEntity
    {
        $useridattr = $this->oauth2config->getString('useridattr');
        if (!isset($attributes[$useridattr])) {
            throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
        }
        $userid = $attributes[$useridattr][0];

        $user = new UserEntity($userid);
        $user->setAttributes($attributes);

        return $user;
    }

    private function getAuthenticationSourceId(ServerRequestInterface $request): string
    {
        $clientEntity = $this->getClientEntityOrThrow($request);

        return $clientEntity->getAuthSource() ?? $this->oauth2config->getString('auth');;
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
