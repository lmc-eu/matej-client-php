<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\InvalidDomainModelArgumentException;

class Response
{
    /** @var CommandResponse[] */
    private $commandResponses = [];
    /** @var int */
    private $numberOfCommands;
    /** @var int */
    private $numberOfSuccessfulCommands;
    /** @var int */
    private $numberOfFailedCommands;
    /** @var int */
    private $numberOfSkippedCommands;

    public function __construct(
        int $numberOfCommands,
        int $numberOfSuccessfulCommands,
        int $numberOfFailedCommands,
        int $numberOfSkippedCommands,
        array $commandResponses = []
    ) {
        $this->numberOfCommands = $numberOfCommands;
        $this->numberOfSuccessfulCommands = $numberOfSuccessfulCommands;
        $this->numberOfFailedCommands = $numberOfFailedCommands;
        $this->numberOfSkippedCommands = $numberOfSkippedCommands;

        foreach ($commandResponses as $rawCommandResponse) {
            $this->commandResponses[] = CommandResponse::createFromRawCommandResponseObject($rawCommandResponse);
        }

        if ($this->numberOfCommands !== count($commandResponses)) {
            throw  InvalidDomainModelArgumentException::forInconsistentNumberOfCommands(
                $this->numberOfCommands,
                count($commandResponses)
            );
        }

        $commandSum = $this->numberOfSuccessfulCommands + $this->numberOfFailedCommands
            + $this->numberOfSkippedCommands;

        if ($this->numberOfCommands !== $commandSum) {
            throw InvalidDomainModelArgumentException::forInconsistentNumbersOfCommandProperties(
                $this->numberOfCommands,
                $this->numberOfSuccessfulCommands,
                $this->numberOfFailedCommands,
                $this->numberOfSkippedCommands
            );
        }
    }

    public function getNumberOfCommands(): int
    {
        return $this->numberOfCommands;
    }

    public function getNumberOfSuccessfulCommands(): int
    {
        return $this->numberOfSuccessfulCommands;
    }

    public function getNumberOfFailedCommands(): int
    {
        return $this->numberOfFailedCommands;
    }

    public function getNumberOfSkippedCommands(): int
    {
        return $this->numberOfSkippedCommands;
    }

    /**
     * Each Command which was part of request batch has here corresponding CommandResponse - on the same index on which
     * the Command was added to the request batch.
     *
     * @return CommandResponse[]
     */
    public function getCommandResponses(): array
    {
        return $this->commandResponses;
    }
}
