<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Response\SortingResponse;
use Lmc\Matej\UnitTestCase;

class RequestTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiable(): void
    {
        $path = '/foo/endpoint';
        $method = RequestMethodInterface::METHOD_GET;
        $data = ['foo' => 'bar', ['lorem' => 'ipsum']];

        $request = new Request($path, $method, $data);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame($path, $request->getPath());
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($data, $request->getData());

        // no custom request id was set => random UUID v4 was generated
        $this->assertSame(36, mb_strlen($request->getRequestId()));
    }

    /** @test */
    public function shouldStoreCustomRequestId(): void
    {
        $request = new Request('/foo', RequestMethodInterface::METHOD_GET, [], 'custom-request-id');

        $this->assertSame('custom-request-id', $request->getRequestId());
    }

    /** @test */
    public function shouldStoreResponseClass(): void
    {
        $request = new Request(
            '/foo',
            RequestMethodInterface::METHOD_GET,
            [],
            null,
            SortingResponse::class
        );

        $this->assertSame(SortingResponse::class, $request->getResponseClass());
    }

    /** @test */
    public function shouldThrowExceptionWhenSettingInvalidResponseClass(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s has to be instance or subclass of %s.',
            \stdClass::class,
            Response::class
        ));

        $request = new Request('/foo', RequestMethodInterface::METHOD_GET, [], null, \stdClass::class);
    }
}
