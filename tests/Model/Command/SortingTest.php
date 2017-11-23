<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\TestCase;

class SortingTest extends TestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructor(): void
    {
        $userId = 'user-id';
        $itemIds = ['item-1', 'item-3', 'item-2'];

        $command = Sorting::create($userId, $itemIds);
        $this->assertSortingObject($command, $userId, $itemIds);
    }

    /**
     * Execute asserts against user merge object
     * @param object $object
     */
    private function assertSortingObject($object, string $userId, array $itemIds): void
    {
        $this->assertInstanceOf(Sorting::class, $object);
        $this->assertSame(
            [
                'type' => 'sorting',
                'parameters' => [
                    'user_id' => $userId,
                    'item_ids' => $itemIds,
                ],
            ],
            $object->jsonSerialize()
        );
    }
}
