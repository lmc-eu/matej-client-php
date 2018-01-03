<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Lmc\Matej\UnitTestCase;
use phpmock\phpunit\PHPMock;

class HmacAuthenticationTest extends UnitTestCase
{
    const TIMESTAMP = 1510230813;
    const APIKEY = 'foobar';

    use PHPMock;

    /**
     * @test
     * @dataProvider provideUris
     */
    public function shouldSignRequest(string $originalPath, string $expectedSingedPath): void
    {
        $time = $this->getFunctionMock(__NAMESPACE__, 'time');
        $time->expects($this->any())->willReturn(static::TIMESTAMP);

        $authentication = new HmacAuthentication(static::APIKEY);
        $unsignedRequest = new \GuzzleHttp\Psr7\Request('GET', 'http://foo.com' . $originalPath);

        $signedRequest = $authentication->authenticate($unsignedRequest);

        $this->assertSame($expectedSingedPath, $signedRequest->getRequestTarget());
    }

    /**
     * @return array[]
     */
    public function provideUris(): array
    {
        // expected values were pre-signed using TIMESTAMP and APIKEY
        return [
            'root path' => [
                '/',
                '/?hmac_timestamp=' . static::TIMESTAMP . '&hmac_sign=88e50f71a0327c7566a34f5e9c0441e0a355d12e',
            ],
            'path without query' => [
                '/endpoint',
                '/endpoint?hmac_timestamp=' . static::TIMESTAMP . '&hmac_sign=03ad5cbdda7539fcd8f918a6630de23e092bf94e',
            ],
            'path with one query param' => [
                '/endpoint?foo=bar',
                '/endpoint?foo=bar&hmac_timestamp=' . static::TIMESTAMP
                . '&hmac_sign=aa6cf8743172b344aa8b75b45b19c219d20298a2',
            ],
            'path with multiple query params' => [
                '/endpoint?foo=bar&ban[]=baz&ban[]=bat',
                '/endpoint?foo=bar&ban%5B0%5D=baz&ban%5B1%5D=bat&hmac_timestamp=' . static::TIMESTAMP
                . '&hmac_sign=486b98665ab883dc79666033a8e0d976fedcf88d',
            ],
        ];
    }
}
