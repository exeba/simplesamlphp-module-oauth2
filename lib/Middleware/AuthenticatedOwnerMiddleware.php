<?php


namespace SimpleSAML\Module\oauth2\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;

class AuthenticatedOwnerMiddleware implements MiddlewareInterface
{
    private $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authenticationService->getUserForRequest($request);

        return $handler->handle($request->withAttribute('user', $user));
    }
}
