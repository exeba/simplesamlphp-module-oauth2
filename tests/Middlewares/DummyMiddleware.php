<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DummyMiddleware implements MiddlewareInterface
{

    private $callOrder;

    public function __construct($callOrder)
    {
        $this->callOrder = $callOrder;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $callNumber = $request->getAttribute('callNumber', 1);
        if ($callNumber !== $this->callOrder) {
            throw new \Exception("Wrong call order: expected {$this->callOrder}, got {$callNumber}");
        }

        $newRequest = $request->withAttribute('callNumber', $callNumber + 1);

        return $handler->handle($newRequest);
    }
}
