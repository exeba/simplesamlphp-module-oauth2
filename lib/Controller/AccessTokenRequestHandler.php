<?php


namespace SimpleSAML\Module\oauth2\Controller;


use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AccessTokenRequestHandler implements RequestHandlerInterface
{
    private $tokenServer;
    private $responseFactory;

    public function __construct(
            AuthorizationServer $tokenServer,
            ResponseFactoryInterface $responseFactory)
    {
        $this->tokenServer = $tokenServer;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->tokenServer->respondToAccessTokenRequest($request, $this->newResponse());
    }

    private function newResponse() {
        return $this->responseFactory->createResponse();
    }
}
