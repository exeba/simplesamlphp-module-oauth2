<?php


namespace SimpleSAML\Module\oauth2;


use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App
{
    private $emitter;
    private $middleware;
    private $handler;

    public function __construct(
        EmitterInterface $emitter,
        MiddlewareInterface $middleware,
        RequestHandlerInterface $handler)
    {

        $this->emitter = $emitter;
        $this->middleware = $middleware;
        $this->handler = $handler;
    }

    public function run()
    {
        $serverRequest = $this->buildServerRequest();
        $response = $this->middleware->process($serverRequest, $this->handler);
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
