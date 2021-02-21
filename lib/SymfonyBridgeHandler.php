<?php


namespace SimpleSAML\Module\oauth2;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Middleware\NoOpMiddleware;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyBridgeHandler implements MiddlewareInterface
{
    private $httpFoundationFactory;
    private $psrHttpFactory;
    private $handler;
    private $middleware;

    public function __construct(
        HttpFoundationFactory $httpFoundationFactory,
        PsrHttpFactory $psrHttpFactory,
        RequestHandlerInterface $handler,
        MiddlewareInterface $middleware = null
    )
    {
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->psrHttpFactory = $psrHttpFactory;
        $this->handler = $handler;
        $this->middleware = $middleware ?? new NoOpMiddleware();
    }

    public function symfonyHandle(Request $request): Response
    {
        $psrRequest = $this->asPsrRequest($request);
        $psrResponse = $this->process($psrRequest, $this->handler);

        return $this->asSymfonyResponse($psrResponse);
    }

    private function asPsrRequest(Request $request): ServerRequestInterface
    {
        return $this->psrHttpFactory->createRequest($request);
    }

    private function asSymfonyResponse(ResponseInterface $response): Response
    {
        return $this->httpFoundationFactory->createResponse($response);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->middleware->process($request, $this->handler);
    }
}
