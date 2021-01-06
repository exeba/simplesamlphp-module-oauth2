<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\Middleware\MiddlewarePair;
use SimpleSAML\Module\oauth2\Middleware\MiddlewareStack;

class MiddlewareStackTest extends TestCase
{

    public function testProcess()
    {
        $dummyResponse = new JsonResponse(array("response"));
        $dummyHandler = new DummyHandler($dummyResponse);
        $firstMiddleware = new DummyMiddleware(1);
        $secondMiddleware = new DummyMiddleware(2);
        $thirdMiddleware = new DummyMiddleware(3);

        $request = (new ServerRequestFactory())->createServerRequest('GET','test/request');

        $middleware = new MiddlewareStack();
        $middleware->addMiddleware($firstMiddleware);
        $middleware->addMiddleware($secondMiddleware);
        $middleware->addMiddleware($thirdMiddleware);

        $response = $middleware->process($request, $dummyHandler);

        $this->assertEquals($dummyResponse, $response);
    }
}
