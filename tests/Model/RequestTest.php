<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\TestCase;

class RequestTest extends TestCase
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
}
