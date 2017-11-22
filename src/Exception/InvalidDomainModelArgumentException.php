<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

/**
 * Exception thrown when invalid argument is passed while creating domain model.
 */
class InvalidDomainModelArgumentException extends LogicException
{
    public static function forInconsistentNumberOfCommands(
        int $numberOfCommands,
        int $commandResponsesCount
    ): self {
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
