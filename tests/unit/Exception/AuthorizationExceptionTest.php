<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lmc\Matej\UnitTestCase;

class AuthorizationExceptionTest extends UnitTestCase
{
    /** @test */
    public function shouldCreateExceptionFromJsonResponse(): void
    {
        $request = new Request('GET', 'http://foo.com/endpoint');

        $response = new Response(
            StatusCodeInterface::STATUS_UNAUTHORIZED,
            ['Content-Type' => 'application/json'],
            '{"message": "Invalid signature. Check your secret key","result": "ERROR"}'
        );

        $exception = AuthorizationException::fromRequestAndResponse($request, $response);

        $this->assertInstanceOf(AuthorizationException::class, $exception);
        $this->assertSame(
            'Matej API authorization error for url "/endpoint" (Invalid signature. Check your secret key)',
            $exception->getMessage()
        );
        $this->assertSame(StatusCodeInterface::STATUS_UNAUTHORIZED, $exception->getCode());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
    }
}
