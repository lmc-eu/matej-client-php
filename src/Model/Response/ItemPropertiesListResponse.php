<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\Response;

class ItemPropertiesListResponse extends Response
{
    public function isSuccessful(): bool
    {
        return $this->getCommandResponse(0)->isSuccessful();
    }

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
