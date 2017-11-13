<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Lmc\Matej\Exception\ResponseDecodingException;
use Lmc\Matej\Model\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseDecoder implements ResponseDecoderInterface
{
    public function decode(ResponseInterface $httpResponse): Response
    {
        $responseData = json_decode($httpResponse->getBody()->getContents());

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw ResponseDecodingException::forJsonError(json_last_error_msg(), $httpResponse);
        }

        return new Response(
            (int) $responseData->commands->number_of_commands,
            (int) $responseData->commands->number_of_successful_commands,
            (int) $responseData->commands->number_of_failed_commands,
            $responseData->commands->responses
        );
    }
}
