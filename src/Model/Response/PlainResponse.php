<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\Response;

/**
 * Response for endpoints always returning data for only one command.
 */
class PlainResponse extends Response
{
    public function getData(): array
    {
        return $this->getCommandResponse(0)->getData();
    }

    public function getMessage(): string
    {
        return $this->getCommandResponse(0)->getMessage();
    }

    public function getStatus(): string
    {
        return $this->getCommandResponse(0)->getStatus();
    }
}
