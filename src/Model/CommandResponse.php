<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\InvalidDomainModelArgumentException;

/**
 * Response to one single command which was part of request batch.
 */
class CommandResponse
{
    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';
    const STATUS_SKIPPED = 'SKIPPED';
    const STATUS_INVALID = 'INVALID';

    /** @var string */
    private $status;
    /** @var string */
    private $message;
    /** @var array */
    private $data = [];

    private function __construct()
    {
    }

    public static function createFromRawCommandResponseObject(\stdClass $rawCommandResponseObject): self
    {
        if (!isset($rawCommandResponseObject->status)) {
            throw new InvalidDomainModelArgumentException('Status field is missing in command response object');
        }

        $commandResponse = new self();
        $commandResponse->status = $rawCommandResponseObject->status;
        $commandResponse->message = $rawCommandResponseObject->message ?? '';
        $commandResponse->data = $rawCommandResponseObject->data ?? [];

        return $commandResponse;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isSuccessful(): bool
    {
        return $this->getStatus() === self::STATUS_OK;
    }
}
