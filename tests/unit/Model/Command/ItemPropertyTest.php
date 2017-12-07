<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Exception\DomainException;
use PHPUnit\Framework\TestCase;

class ItemPropertyTest extends TestCase
{
    /**
     * @test
     */
    public function shouldNotAllowItemIdInProperties(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot update value of "item_id" property - it is used by Matej to identify the item and cannot be altered once created.');
        ItemProperty::create('exampleItemId', ['item_id' => 'customItemId']);
    }

    /**
     * @test
     * @dataProvider provideProperties
     */
    public function shouldBeInstantiableViaNamedConstructor(array $properties, array $expectedParameters): void
    {
        $command = ItemProperty::create('exampleItemId', $properties);

        $this->assertInstanceOf(ItemProperty::class, $command);
        $this->assertSame(
            [
                'type' => 'item-properties',
                'parameters' => $expectedParameters,
            ],
            $command->jsonSerialize()
        );
    }

    /**
     * @return array[]
     */
    public function provideProperties(): array
    {
        return [
            'No item properties' => [[], ['item_id' => 'exampleItemId']],
            'One item property' => [['date' => 1510756952], ['date' => 1510756952, 'item_id' => 'exampleItemId']],
            'Multiple item properties' => [
                ['item1' => 'value1', 'item2' => 'value2'],
                ['item1' => 'value1', 'item2' => 'value2', 'item_id' => 'exampleItemId'],
            ],
        ];
    }
}
