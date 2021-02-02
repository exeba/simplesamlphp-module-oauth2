<?php


namespace SimpleSAML\Module\oauth2\Controller;


use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

class AccessTokenRequestHandler implements RequestHandlerInterface
{
    private $tokenServer;
    private $responseFactory;
    private $psrHttpFactory;

    public function __construct(
            AuthorizationServer $tokenServer,
            ResponseFactoryInterface $responseFactory,
            PsrHttpFactory $psrHttpFactory)
    {
        $this->tokenServer = $tokenServer;
        $this->responseFactory = $responseFactory;
        $this->psrHttpFactory = $psrHttpFactory;
    }

    public function accessToken(Request $request)
    {
        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createResponse(
                $this->handle($this->psrHttpFactory->createRequest($request)));
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->tokenServer->respondToAccessTokenRequest($request, $this->newResponse());
    }

    private function newResponse() {
        return $this->responseFactory->createResponse();
    }
}
