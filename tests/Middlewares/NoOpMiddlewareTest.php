<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\Middleware\NoOpMiddleware;

class NoOpMiddlewareTest extends TestCase
{

    public function testProcess()
    {
        $dummyRequest = (new ServerRequestFactory())->createServerRequest('GET','test/request');
        $dummyResponse = new JsonResponse(array("test"));
        $dummyHandler = new DummyHandler($dummyResponse);

        $middleeare = new NoOpMiddleware();
        $response = $middleeare->process($dummyRequest, $dummyHandler);

        $this->assertEquals($dummyRequest, $dummyHandler->getRequest());
        $this->assertEquals($dummyResponse, $response);
    }

}
