<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DummyHandler implements RequestHandlerInterface
{

    private $processedRequest;
    private $cannedResponse;

    public function __construct(ResponseInterface $cannedResponse)
    {
        $this->cannedResponse = $cannedResponse;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->processedRequest = $request;

        return $this->cannedResponse;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->processedRequest;
    }
}
