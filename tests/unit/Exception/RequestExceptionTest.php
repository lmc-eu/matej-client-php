<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lmc\Matej\UnitTestCase;

class RequestExceptionTest extends UnitTestCase
{
    /** @test */
    public function shouldConstructNewException(): void
    {
        $message = 'Foo bar baz message';
        $request = new Request('GET', 'http://foo.com');
        $response = new Response(StatusCodeInterface::STATUS_NOT_FOUND);

        $exception = new RequestException($message, $request, $response);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $exception->getCode());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
    }
}
