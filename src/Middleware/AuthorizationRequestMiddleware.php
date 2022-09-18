<?php

namespace SimpleSAML\Module\oauth2\Middleware;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;

class AuthorizationRequestMiddleware implements MiddlewareInterface
{
    private $userRepository;
    private $authorizationServer;
    private $authenticationService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AuthorizationServer $authorizationServer,
        AuthenticationService $authenticationService
    ) {
        $this->userRepository = $userRepository;
        $this->authorizationServer = $authorizationServer;
        $this->authenticationService = $authenticationService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authnRequest = $this->authorizationServer->validateAuthorizationRequest($request);
        $userEntity = $this->authenticationService->getUserForAuthnRequest($authnRequest);

        $this->userRepository->insertOrUpdate($userEntity);
        $authnRequest->setUser($userEntity);

        return $handler->handle($request->withAttribute('authnRequest', $authnRequest));
    }
}
