<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use PHPUnit\Framework\TestCase;

class ItemItemRecommendationTest extends TestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructorWithDefaultValues(): void
    {
        $command = ItemItemRecommendation::create('item-id', 'test-scenario');

        $this->assertEquals(
            [
                'type' => 'item-item-recommendations',
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

        $command = ItemItemRecommendation::create($itemId, $scenario)
            ->setCount($count)
            ->setFilters(['foo = bar', 'baz = ban'])
            ->setModelName($modelName)
            ->addResponseProperty('item_url')
            ->addBoost(Boost::create('valid_to >= NOW()', 1.0))
            ->addBoost(Boost::create('custom = argument', 2.0));

        $this->assertEquals(
            [
                'type' => 'item-item-recommendations',
                'parameters' => [
                    'item_id' => $itemId,
                    'count' => $count,
                    'scenario' => $scenario,
                    'filter' => 'foo = bar and baz = ban',
                    'properties' => ['item_url'],
                    'model_name' => $modelName,
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
        $command = ItemItemRecommendation::create('item-id', 'test-scenario');

        // Default filter
        $this->assertArrayNotHasKey('filter', $command->jsonSerialize()['parameters']);

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
        $command = ItemItemRecommendation::create('item-id', 'test-scenario');
        $command->addResponseProperty('test');
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
        $command = ItemItemRecommendation::create('item-id', 'test-scenario')
            ->addBoost(Boost::create('valid_to >= NOW()', 1.0))
            ->setBoosts(
                [
                    Boost::create('foo = bar', 1.2),
                    Boost::create('baz = ban', 3.4),
                ]
            );

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
        $command = ItemItemRecommendation::create('item-id', 'test-scenario')
            ->setBoosts([]);

        $this->assertArrayNotHasKey('boost_rules', $command->jsonSerialize()['parameters']);
    }
}
