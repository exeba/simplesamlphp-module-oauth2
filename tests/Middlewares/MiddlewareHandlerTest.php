<?php


namespace SimpleSAML\Module\oauth2\Middlewares;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Middleware\MiddlewareHandler;

class MiddlewareHandlerTest extends TestCase
{

    public function testProcess()
    {
        $dummyRequest = (new ServerRequestFactory())->createServerRequest('GET','test/request');
        $dummyResponse = new JsonResponse(array("test response"));
        $dummyHandler = $this->createMock(RequestHandlerInterface::class);
        $middlewareMock = $this->createMock(MiddlewareInterface::class);

        $middlewareMock->expects($this->once())
            ->method('process')->with($dummyRequest, $dummyHandler)
            ->willReturn($dummyResponse);

        $middleware = new MiddlewareHandler($middlewareMock, $dummyHandler);
        $response = $middleware->handle($dummyRequest);

        $this->assertEquals($dummyResponse, $response);
    }

}
