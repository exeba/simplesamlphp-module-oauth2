<?php


namespace SimpleSAML\Module\oauth2\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareStack implements MiddlewareInterface
{

    private $combinedMiddlewares;

    public function addMiddleware(MiddlewareInterface $middleware)
    {
        if (empty($this->combinedMiddlewares)) {
            $this->combinedMiddlewares = $middleware;
        } else {
            $this->combinedMiddlewares = new MiddlewarePair($this->combinedMiddlewares, $middleware);
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->combinedMiddlewares->process($request, $handler);
    }
}
