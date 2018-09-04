<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;
use Lmc\Matej\Model\Command\Constants\PropertyType;

/**
 * Command to add or delete item property in the database.
 */
class ItemPropertySetup extends AbstractCommand
{
    /** @var string */
    private $propertyName;
    /** @var PropertyType */
    private $propertyType;

    private function __construct(string $propertyName, PropertyType $propertyType)
    {
        $this->setPropertyName($propertyName);
        $this->propertyType = $propertyType;
    }

    /** @return static */
    public static function int(string $propertyName): self
    {
        return new static($propertyName, PropertyType::INT());
    }

    /** @return static */
    public static function double(string $propertyName): self
    {
        return new static($propertyName, PropertyType::DOUBLE());
    }

    /** @return static */
    public static function string(string $propertyName): self
    {
        return new static($propertyName, PropertyType::STRING());
    }

    /** @return static */
    public static function boolean(string $propertyName): self
    {
        return new static($propertyName, PropertyType::BOOLEAN());
    }

    /** @return static */
    public static function timestamp(string $propertyName): self
    {
        return new static($propertyName, PropertyType::TIMESTAMP());
    }

    /** @return static */
    public static function set(string $propertyName): self
    {
        return new static($propertyName, PropertyType::SET());
    }

    protected function setPropertyName(string $propertyName): void
    {
        Assertion::typeIdentifier($propertyName);
        Assertion::notEq(
            $propertyName,
            'item_id',
            'Cannot manipulate with property "item_id" - it is used by Matej to identify items.'
        );

        $this->propertyName = $propertyName;
    }

    protected function getCommandType(): string
    {
        return 'item-properties-setup';
    }

    protected function getCommandParameters(): array
    {
        return [
            'property_name' => $this->propertyName,
            'property_type' => $this->propertyType->jsonSerialize(),
        ];
    }
}
