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
        return $request->getAttributes()['authSource'] ?? $this->oauth2config->getString('auth');
    }

}
