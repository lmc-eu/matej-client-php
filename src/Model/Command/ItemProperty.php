<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

/**
 * Command to save different item content properties to Matej.
 */
class ItemProperty extends AbstractCommand
{
    /** @var string */
    private $itemId;
    /** @var array */
    private $properties;

    private function __construct(string $itemId, array $properties)
    {
        // TODO: assert itemId format

        $this->itemId = $itemId;
        $this->properties = $properties;
    }

    public static function create(string $itemId, array $properties = []): self
    {
        return new static($itemId, $properties);
    }

    protected function getCommandType(): string
    {
        return 'item-properties';
    }

    protected function getCommandParameters(): array
    {
        $parameters = $this->properties;

        $parameters['item_id'] = $this->itemId;

        return $parameters;
    }
}
