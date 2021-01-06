<?php


namespace SimpleSAML\Module\oauth2\Controller;

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AbstractController implements RequestHandlerInterface
{

    public function handleSimpleSAMLPhpRequest(): ResponseInterface {
        $request = ServerRequestFactory::fromGlobals();
        $response = $this->handle($request);

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $emiter = new SapiEmitter();
        $emiter->emit($response);
        // TODO: Implement handle() method.
    }

    protected function doHandle(ServerRequestInterface $request)
    {

    }
}
