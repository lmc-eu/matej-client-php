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
        $modelName = 'test-model-name';

        $command = Sorting::create($userId, $itemIds);
        $this->assertSortingCommand($command, $userId, $itemIds);

        $command->setModelName($modelName);
        $this->assertSortingCommand($command, $userId, $itemIds, $modelName);
    }

    /**
     * Execute asserts against user merge command
     *
     * @param Sorting $command
     */
    private function assertSortingCommand($command, string $userId, array $itemIds, ?string $modelName = null): void
    {
        $parameters = [
            'user_id' => $userId,
            'item_ids' => $itemIds,
        ];

        if ($modelName !== null) {
            $parameters['model_name'] = $modelName;
        }

        $this->assertInstanceOf(Sorting::class, $command);
        $this->assertSame(
            [
                'type' => 'sorting',
                'parameters' => $parameters,
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($userId, $command->getUserId());
    }
}
