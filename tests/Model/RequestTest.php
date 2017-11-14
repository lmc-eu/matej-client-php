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
    }
}
