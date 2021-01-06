<?php


namespace SimpleSAML\Module\oauth2\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewarePair implements MiddlewareInterface
{

    private $first;
    private $second;

    public function __construct(MiddlewareInterface $first, MiddlewareInterface $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->first->process($request, new MiddlewareHandler($this->second, $handler));
    }
}
