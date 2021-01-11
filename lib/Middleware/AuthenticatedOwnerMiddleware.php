<?php


namespace SimpleSAML\Module\oauth2\Middleware;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;


class AuthenticatedOwnerMiddleware implements MiddlewareInterface
{

    private $clientRepository;
    private $oauth2config;

    public function __construct(ClientRepository $clientRepository, Configuration $oauth2config)
    {
        $this->clientRepository = $clientRepository;
        $this->oauth2config = $oauth2config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $auth = new Simple($this->getAuthenticationSourceId($request));
        $auth->requireAuth();

        return $handler->handle($request);
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
