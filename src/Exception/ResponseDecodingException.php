<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown when Matej response cannot be decoded.
 */
class ResponseDecodingException extends AbstractMatejException
{
    public static function forJsonError(string $jsonErrorMsg, ResponseInterface $response): self
    {
        return new self(
            sprintf(
                "Error decoding Matej response: %s\n\nStatus code: %s %s\nBody:\n%s",
                $jsonErrorMsg,
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getBody()
            )
        );
    }

    public static function forInvalidData(ResponseInterface $response): self
    {
        return new self(
            sprintf(
                "Error decoding Matej response: required data missing.\n\nBody:\n%s",
                $response->getBody()
            )
        );
    }
}
