<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Command\Constants\MinimalRelevance;
use PHPUnit\Framework\TestCase;

class UserRecommendationTest extends TestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructorWithDefaultValues(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600);

        $this->assertInstanceOf(UserRecommendation::class, $command);
        $this->assertSame(
            [
                'type' => 'user-based-recommendations',
                'parameters' => [
                    'user_id' => 'user-id',
                    'count' => 333,
                    'scenario' => 'test-scenario',
                    'rotation_rate' => 1.0,
                    'rotation_time' => 3600,
                    'hard_rotation' => false,
                    'min_relevance' => MinimalRelevance::LOW,
                    'filter' => '',
                    'filter_type' => UserRecommendation::FILTER_TYPE_MQL,
                    'properties' => [],
                    // when using default model name, parameter "model_name" should be absent.
                    // by default, allow_seen is absent
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame('user-id', $command->getUserId());
    }

    /** @test */
    public function shouldUseCustomParameters(): void
    {
        $userId = 'user-' . md5(microtime());
        $count = random_int(1, 100);
        $scenario = 'scenario-' . md5(microtime());
        $rotationRate = mt_rand() / mt_getrandmax();
        $rotationTime = random_int(1, 86400);
        $modelName = 'test-model-' . md5(microtime());

        $command = UserRecommendation::create($userId, $count, $scenario, $rotationRate, $rotationTime);

        $command->setMinimalRelevance(MinimalRelevance::HIGH())
            ->enableHardRotation()
            ->setFilters(['foo = bar', 'baz = ban'])
            ->setModelName($modelName)
            ->setAllowSeen(true)
            ->addBoost(Boost::create('valid_to >= NOW()', 1.0))
            ->addBoost(Boost::create('custom = argument', 2.0));

        $this->assertInstanceOf(UserRecommendation::class, $command);
        $this->assertSame(
            [
                'type' => 'user-based-recommendations',
                'parameters' => [
                    'user_id' => $userId,
                    'count' => $count,
                    'scenario' => $scenario,
                    'rotation_rate' => $rotationRate,
                    'rotation_time' => $rotationTime,
                    'hard_rotation' => true,
                    'min_relevance' => MinimalRelevance::HIGH,
                    'filter' => 'foo = bar and baz = ban',
                    'filter_type' => UserRecommendation::FILTER_TYPE_MQL,
                    'properties' => [],
                    'model_name' => $modelName,
                    'allow_seen' => true,
                    'boost_rules' => [
                        ['query' => 'valid_to >= NOW()', 'multiplier' => 1.0],
                        ['query' => 'custom = argument', 'multiplier' => 2.0],
                    ],
                ],
            ],
            $command->jsonSerialize()
        );
    }

    /** @test */
    public function shouldAssembleMqlFilters(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600);

        // Default filter
        $this->assertSame('', $command->jsonSerialize()['parameters']['filter']);

        // Add custom filters to the default one
        $command->addFilter("first_string_property = 'bar'")
            ->addFilter("second_string_property LIKE 'bar%'")
            ->addFilter("third_string_property NOT LIKE '%bar'")
            ->addFilter('bool_property = true')
            ->addFilter('nullable_property IS NULL')
            ->addFilter("'some_value' in set_property");

        $this->assertSame(
            "first_string_property = 'bar' and " .
            "second_string_property LIKE 'bar%' and " .
            "third_string_property NOT LIKE '%bar' and " .
            'bool_property = true and ' .
            'nullable_property IS NULL and ' .
            "'some_value' in set_property",
            $command->jsonSerialize()['parameters']['filter']
        );

        // Overwrite all filters
        $command->setFilters(['my_filter = 1', 'other_filter IS NULL']);

        $this->assertSame('my_filter = 1 and other_filter IS NULL', $command->jsonSerialize()['parameters']['filter']);
    }

    /** @test */
    public function shouldAllowModificationOfResponseProperties(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600, ['test']);
        $this->assertSame(['test'], $command->jsonSerialize()['parameters']['properties']);

        // Add some properties
        $command->addResponseProperty('url');

        $this->assertSame(['test', 'url'], $command->jsonSerialize()['parameters']['properties']);

        // Overwrite all properties
        $command->setResponseProperties(['position_title']);
        $this->assertSame(['position_title'], $command->jsonSerialize()['parameters']['properties']);
    }

    /** @test */
    public function shouldResetBoostRules(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600)
            ->addBoost(Boost::create('valid_to >= NOW()', 1.0));

        $command->setBoosts([
            Boost::create('foo = bar', 1.2),
            Boost::create('baz = ban', 3.4),
        ]);

        $this->assertSame(
            [
                ['query' => 'foo = bar', 'multiplier' => 1.2],
                ['query' => 'baz = ban', 'multiplier' => 3.4],
            ],
            $command->jsonSerialize()['parameters']['boost_rules']
        );
    }

    /** @test */
    public function shouldNotIncludeEmptyBoosts(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600)
            ->setBoosts([]);

        $this->assertArrayNotHasKey('boost_rules', $command->jsonSerialize()['parameters']);
    }
}
