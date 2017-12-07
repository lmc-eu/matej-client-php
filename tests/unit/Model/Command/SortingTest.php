<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\UnitTestCase;

class SortingTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructor(): void
    {
        $userId = 'user-id';
        $itemIds = ['item-1', 'item-3', 'item-2'];

        $command = Sorting::create($userId, $itemIds);
        $this->assertSortingCommand($command, $userId, $itemIds);
    }

    /**
     * Execute asserts against user merge command
     * @param Sorting $command
     */
    private function assertSortingCommand($command, string $userId, array $itemIds): void
    {
        $this->assertInstanceOf(Sorting::class, $command);
        $this->assertSame(
            [
                'type' => 'sorting',
                'parameters' => [
                    'user_id' => $userId,
                    'item_ids' => $itemIds,
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($userId, $command->getUserId());
    }
}
