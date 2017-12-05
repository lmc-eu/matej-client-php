<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Fig\Http\Message\RequestMethodInterface;
use Http\Mock\Client;
use Lmc\Matej\Matej;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Lmc\Matej\UnitTestCase;

/**
 * @covers \Lmc\Matej\Http\RequestManager
 */
class RequestManagerTest extends UnitTestCase
{
    /**
     * Test sending request and decoding response - but isolated from the real HTTP using Http\Mock\Client.
     * However all other RequestManager dependencies are real, making this partially integration test.
     *
     * @test
     */
    public function shouldSendAndDecodeRequest(): void
    {
        $dummyHttpResponse = $this->createJsonResponseFromFile(__DIR__ . '/Fixtures/response-one-successful-command.json');

        $mockClient = new Client();
        $mockClient->addResponse($dummyHttpResponse);

        $requestManager = new RequestManager('account-id', 'api-key');
        $requestManager->setHttpClient($mockClient);

        $request = new Request(
            '/foo/endpoint',
            RequestMethodInterface::METHOD_PUT,
            ['foo' => 'bar', 'list' => ['lorem' => 'ipsum', 'dolor' => 333]],
            'custom-request-id'
        );

        $matejResponse = $requestManager->sendRequest($request);

        // Request should be decoded to Matej Response; decoding itself is comprehensively tested in ResponseDecoderTest
        $this->assertInstanceOf(Response::class, $matejResponse);

        // Assert properties of the send request
        $recordedRequests = $mockClient->getRequests();
        $this->assertCount(1, $recordedRequests);
        $this->assertRegExp(
            '~https\://account\-id\.matej\.lmc\.cz/foo/endpoint\?hmac_timestamp\=[0-9]+&hmac_sign\=[[:alnum:]]~',
            $recordedRequests[0]->getUri()->__toString()
        );
        $this->assertSame(RequestMethodInterface::METHOD_PUT, $recordedRequests[0]->getMethod());
        $this->assertJsonStringEqualsJsonString(
            '{"foo":"bar","list":{"lorem":"ipsum","dolor":333}}',
            $recordedRequests[0]->getBody()->__toString()
        );
        $this->assertSame(['application/json'], $recordedRequests[0]->getHeader('Content-Type'));
        $this->assertSame(['custom-request-id'], $recordedRequests[0]->getHeader(RequestManager::REQUEST_ID_HEADER));
        $this->assertSame(
            Matej::CLIENT_ID . '/' . Matej::VERSION,
            $recordedRequests[0]->getHeader(RequestManager::CLIENT_VERSION_HEADER)[0]
        );
    }
}
