<?php


namespace SimpleSAML\Module\oauth2;


use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Middleware\NoOpMiddleware;

class App
{
    private $emitter;

    private $middleware;

    public function __construct(MiddlewareInterface $middleware = null)
    {
        $this->middleware = $middleware ?? new NoOpMiddleware();
        $this->emitter = new SapiEmitter();
    }

    public function run(RequestHandlerInterface $handler)
    {
        $serverRequest = $this->buildServerRequest();
        $response = $this->middleware->process($serverRequest, $handler);
        $this->sendResponse($response);
    }

    private function buildServerRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }

    private function sendResponse(ResponseInterface $response)
    {
        $this->emitter->emit($response);
    }

}
