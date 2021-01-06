<?php


namespace SimpleSAML\Module\oauth2\Middleware;


use Laminas\Diactoros\Stream;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RequestExceptionMiddleware implements MiddlewareInterface
{
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->newResponse());
        } catch (\Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $this->newResponse()
                ->withStatus(500)
                ->withBody($body);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->throwingHandler->handle($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->newResponse());
        } catch (\Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $this->newResponse()
                ->withStatus(500)
                ->withBody($exception->getMessage());
        }
    }


    private function newResponse()
    {
        return $this->responseFactory->createResponse();
    }

}
