<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Model\Command\Constants\PropertyType;
use PHPUnit\Framework\TestCase;

class ItemPropertySetupTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConstructorName
     */
    public function shouldBeInstantiableViaNamedConstructors(
        string $constructorName,
        string $expectedPropertyType
    ): void {
        $propertyName = 'examplePropertyName';

        /** @var ItemPropertySetup $command */
        $command = ItemPropertySetup::$constructorName($propertyName);

        $this->assertInstanceOf(ItemPropertySetup::class, $command);
        $this->assertSame(
            [
                'type' => 'item-properties-setup',
                'parameters' => [
                    'property_name' => 'examplePropertyName',
                    'property_type' => $expectedPropertyType,
                ],
            ],
            $command->jsonSerialize()
        );
    }

    /**
     * @test
     * @dataProvider provideConstructorName
     */
    public function shouldNotAllowItemIdAsPropertyName(string $constructorName): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Cannot manipulate with property "item_id" - it is used by Matej to identify items.'
        );
        ItemPropertySetup::$constructorName('item_id');
    }

    /**
     * @return array[]
     */
    public function provideConstructorName(): array
    {
        return [
            ['int', PropertyType::INT],
            ['double', PropertyType::DOUBLE],
            ['string', PropertyType::STRING],
            ['boolean', PropertyType::BOOLEAN],
            ['timestamp', PropertyType::TIMESTAMP],
            ['set', PropertyType::SET],
            ['geolocation', PropertyType::GEOLOCATION],
        ];
    }
}
