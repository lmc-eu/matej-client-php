<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Command\Constants\UserMinimalRelevance;
use PHPUnit\Framework\TestCase;

class ItemUserRecommendationTest extends TestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructorWithDefaultValues(): void
    {
        $command = ItemUserRecommendation::create('item-id', 'test-scenario');

        $this->assertEquals(
            [
                'type' => 'item-user-recommendations',
                'parameters' => [
                    'item_id' => 'item-id',
                    'scenario' => 'test-scenario',
                ],
            ],
            $command->jsonSerialize()
        );
    }

    /** @test */
    public function shouldUseCustomParameters(): void
    {
        $itemId = 'item-' . md5(microtime());
        $count = random_int(1, 100);
        $scenario = 'scenario-' . md5(microtime());
        $modelName = 'test-model-' . md5(microtime());

        $command = ItemUserRecommendation::create($itemId, $scenario)
            ->setCount($count)
            ->setAllowSeen(true)
            ->setMinimalRelevance(UserMinimalRelevance::HIGH())
            ->setModelName($modelName);

        $this->assertEquals(
            [
                'type' => 'item-user-recommendations',
                'parameters' => [
                    'item_id' => $itemId,
                    'count' => $count,
                    'scenario' => $scenario,
                    'min_relevance' => UserMinimalRelevance::HIGH,
                    'model_name' => $modelName,
                    'allow_seen' => true,
                ],
            ],
            $command->jsonSerialize()
        );
    }
}
