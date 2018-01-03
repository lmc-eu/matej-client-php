<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Command to add or delete item property in the database.
 */
class ItemPropertySetup extends AbstractCommand
{
    const PROPERTY_TYPE_INT = 'int';
    const PROPERTY_TYPE_DOUBLE = 'double';
    const PROPERTY_TYPE_STRING = 'string';
    const PROPERTY_TYPE_BOOLEAN = 'boolean';
    const PROPERTY_TYPE_TIMESTAMP = 'timestamp';
    const PROPERTY_TYPE_SET = 'set';

    /** @var string */
    private $propertyName;
    /** @var string */
    private $propertyType;

    private function __construct(string $propertyName, string $propertyType)
    {
        $this->setPropertyName($propertyName);
        $this->propertyType = $propertyType;
    }

    /** @return static */
    public static function int(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_INT);
    }

    /** @return static */
    public static function double(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_DOUBLE);
    }

    /** @return static */
    public static function string(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_STRING);
    }

    /** @return static */
    public static function boolean(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_BOOLEAN);
    }

    /** @return static */
    public static function timestamp(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_TIMESTAMP);
    }

    /** @return static */
    public static function set(string $propertyName): self
    {
        return new static($propertyName, static::PROPERTY_TYPE_SET);
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
            'property_type' => $this->propertyType,
        ];
    }
}
