<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Response;
use Lmc\Matej\Exception\ResponseDecodingException;
use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\UnitTestCase;

class ResponseDecoderTest extends UnitTestCase
{
    /** @var ResponseDecoder */
    protected $decoder;

    /** @before */
    public function init(): void
    {
        $this->decoder = new ResponseDecoder();
    }

    /** @test */
    public function shouldDecodeSimpleOkResponse(): void
    {
        $response = $this->createJsonResponseFromFile(__DIR__ . '/Fixtures/response-one-successful-command.json');

        $decodedResponse = $this->decoder->decode($response);

        $this->assertSame(1, $decodedResponse->getNumberOfCommands());
        $this->assertSame(1, $decodedResponse->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $decodedResponse->getNumberOfFailedCommands());
        $this->assertSame(0, $decodedResponse->getNumberOfSkippedCommands());
        $this->assertNull($decodedResponse->getResponseId());

        $commandResponses = $decodedResponse->getCommandResponses();
        $this->assertCount(1, $commandResponses);
        $this->assertInstanceOf(CommandResponse::class, $commandResponses[0]);
        $this->assertSame(CommandResponse::STATUS_OK, $commandResponses[0]->getStatus());
    }

    /** @test */
    public function shouldDecodeResponseId(): void
    {
        $response = new Response(
            StatusCodeInterface::STATUS_OK,
            [RequestManager::RESPONSE_ID_HEADER => 'received-response-id', 'Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/Fixtures/response-one-successful-command.json')
        );

        $decodedResponse = $this->decoder->decode($response);

        $this->assertSame('received-response-id', $decodedResponse->getResponseId());
    }

    /** @test */
    public function shouldDecodeResponseMultipleResponses(): void
    {
        $response = $this->createJsonResponseFromFile(__DIR__ . '/Fixtures/response-item-properties.json');

        $decodedResponse = $this->decoder->decode($response);

        $this->assertSame(3, $decodedResponse->getNumberOfCommands());
        $this->assertSame(2, $decodedResponse->getNumberOfSuccessfulCommands());
        $this->assertSame(1, $decodedResponse->getNumberOfFailedCommands());
        $this->assertSame(0, $decodedResponse->getNumberOfSkippedCommands());

        $commandResponses = $decodedResponse->getCommandResponses();
        $this->assertCount(3, $commandResponses);
        $this->assertContainsOnlyInstancesOf(CommandResponse::class, $commandResponses);
        $this->assertSame(CommandResponse::STATUS_OK, $commandResponses[0]->getStatus());
        $this->assertSame(CommandResponse::STATUS_ERROR, $commandResponses[1]->getStatus());
        $this->assertSame(CommandResponse::STATUS_OK, $commandResponses[2]->getStatus());
    }

    /** @test */
    public function shouldThrowExceptionWhenDecodingFails(): void
    {
        $notJsonData = file_get_contents(__DIR__ . '/Fixtures/invalid-json.html');
        $response = new Response(StatusCodeInterface::STATUS_NOT_FOUND, [], $notJsonData);

        $this->expectException(ResponseDecodingException::class);
        $this->expectExceptionMessage('Error decoding Matej response');
        $this->expectExceptionMessage('Status code: 404 Not Found');
        $this->expectExceptionMessage('<p>The requested URL /foo was not found on this server.</p>');
        $this->decoder->decode($response);
    }

    /** @test */
    public function shouldThrowExceptionWhenJsonWithInvalidDataIsDecoded(): void
    {
        $notJsonData = file_get_contents(__DIR__ . '/Fixtures/invalid-response-format.json');
        $response = new Response(StatusCodeInterface::STATUS_NOT_FOUND, [], $notJsonData);

        $this->expectException(ResponseDecodingException::class);
        $this->expectExceptionMessage('Error decoding Matej response: required data missing.');
        $this->expectExceptionMessage('"invalid": [],');
        $this->decoder->decode($response);
    }
}
