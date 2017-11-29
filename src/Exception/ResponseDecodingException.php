<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown when Matej response cannot be decoded.
 */
class ResponseDecodingException extends \RuntimeException implements MatejExceptionInterface
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

    public static function forInconsistentNumberOfCommands(int $numberOfCommands, int $commandResponsesCount): self
    {
        return new self(
            sprintf(
                'Provided numberOfCommands (%d) is inconsistent with actual count of command responses (%d)',
                $numberOfCommands,
                $commandResponsesCount
            )
        );
    }

    public static function forInconsistentNumbersOfCommandProperties(
        int $numberOfCommands,
        $numberOfSuccessfulCommands,
        $numberOfFailedCommands,
        $numberOfSkippedCommands
    ): self {
        return new self(
            sprintf(
                'Provided numberOfCommands (%d) is inconsistent with provided sum of '
                . 'numberOfSuccessfulCommands (%d) + numberOfFailedCommands (%d)'
                . ' + numberOfSkippedCommands (%d)',
                $numberOfCommands,
                $numberOfSuccessfulCommands,
                $numberOfFailedCommands,
                $numberOfSkippedCommands
            )
        );
    }
}
