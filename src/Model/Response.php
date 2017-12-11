<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\ResponseDecodingException;

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
    /** @var string|null */
    private $responseId;

    public function __construct(
        int $numberOfCommands,
        int $numberOfSuccessfulCommands,
        int $numberOfFailedCommands,
        int $numberOfSkippedCommands,
        array $commandResponses = [],
        string $responseId = null
    ) {
        $this->numberOfCommands = $numberOfCommands;
        $this->numberOfSuccessfulCommands = $numberOfSuccessfulCommands;
        $this->numberOfFailedCommands = $numberOfFailedCommands;
        $this->numberOfSkippedCommands = $numberOfSkippedCommands;
        $this->responseId = $responseId;

        foreach ($commandResponses as $rawCommandResponse) {
            $this->commandResponses[] = CommandResponse::createFromRawCommandResponseObject($rawCommandResponse);
        }

        if ($this->numberOfCommands !== count($commandResponses)) {
            throw ResponseDecodingException::forInconsistentNumberOfCommands(
                $this->numberOfCommands,
                count($commandResponses)
            );
        }

        $commandSum = $this->numberOfSuccessfulCommands + $this->numberOfFailedCommands
            + $this->numberOfSkippedCommands;

        if ($this->numberOfCommands !== $commandSum) {
            throw ResponseDecodingException::forInconsistentNumbersOfCommandProperties(
                $this->numberOfCommands,
                $this->numberOfSuccessfulCommands,
                $this->numberOfFailedCommands,
                $this->numberOfSkippedCommands
            );
        }
        $this->responseId = $responseId;
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
     * Return individual command response by its index (0 indexed)
     */
    public function getCommandResponse(int $index): CommandResponse
    {
        return $this->commandResponses[$index];
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

    public function getResponseId(): ?string
    {
        return $this->responseId;
    }
}
