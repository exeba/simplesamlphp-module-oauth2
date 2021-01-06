<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\Middleware\MiddlewarePair;

class MiddlewarePairTest extends TestCase
{

    public function testProcess()
    {
        $dummyResponse = new JsonResponse(array("response"));
        $dummyHandler = new DummyHandler($dummyResponse);
        $firstMiddleware = new DummyMiddleware(1);
        $secondMiddleware = new DummyMiddleware(2);

        $request = (new ServerRequestFactory())->createServerRequest('GET','test/request');

        $middleware = new MiddlewarePair($firstMiddleware, $secondMiddleware);
        $response = $middleware->process($request, $dummyHandler);

        $this->assertEquals($dummyResponse, $response);
    }
}
