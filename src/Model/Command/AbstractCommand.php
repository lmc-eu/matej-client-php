<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

abstract class AbstractCommand implements \JsonSerializable
{
    /**
     * Get command type identifier. Must be one of those defined by Matej API schema.
     */
    abstract protected function getCommandType(): string;

    /**
     * Get data content of the command. Must follow the format defined by Matej API schema.
     */
    abstract protected function getCommandParameters(): array;

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getCommandType(),
            'parameters' => $this->getCommandParameters(),
        ];
    }
}
