<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Lmc\Matej\Exception\ResponseDecodingException;
use Lmc\Matej\Model\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseDecoder implements ResponseDecoderInterface
{
    public function decode(ResponseInterface $httpResponse, string $responseClass = Response::class): Response
    {
        $responseData = json_decode($httpResponse->getBody()->getContents());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ResponseDecodingException::forJsonError(json_last_error_msg(), $httpResponse);
        }

        if (!$this->isResponseValid($responseData)) {
            throw ResponseDecodingException::forInvalidData($httpResponse);
        }

        $responseId = $httpResponse->getHeader(RequestManager::RESPONSE_ID_HEADER)[0] ?? null;

        return new $responseClass(
            (int) $responseData->commands->number_of_commands,
            (int) $responseData->commands->number_of_successful_commands,
            (int) $responseData->commands->number_of_failed_commands,
            (int) $responseData->commands->number_of_skipped_commands,
            $responseData->commands->responses,
            $responseId
        );
    }

    private function isResponseValid(\stdClass $responseData): bool
    {
        return isset(
            $responseData->commands,
            $responseData->commands->number_of_commands,
            $responseData->commands->number_of_successful_commands,
            $responseData->commands->number_of_failed_commands,
            $responseData->commands->number_of_skipped_commands,
            $responseData->commands->responses,
            $responseData->message,
            $responseData->status
        );
    }
}
