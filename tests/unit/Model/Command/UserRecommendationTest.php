<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

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
                    'min_relevance' => UserRecommendation::MINIMAL_RELEVANCE_LOW,
                    'filter' => 'valid_to >= NOW',
                    'properties' => [],
                    // intentionally no model name ==> should be absent when not used
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

        $command->setMinimalRelevance(UserRecommendation::MINIMAL_RELEVANCE_HIGH)
            ->enableHardRotation()
            ->setFilters(['foo = bar', 'baz = ban'])
            ->setModelName($modelName);

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
                    'min_relevance' => UserRecommendation::MINIMAL_RELEVANCE_HIGH,
                    'filter' => 'foo = bar and baz = ban',
                    'properties' => [],
                    'model_name' => $modelName,
                ],
            ],
            $command->jsonSerialize()
        );
    }

    /** @test */
    public function shouldAssembleFilters(): void
    {
        $command = UserRecommendation::create('user-id', 333, 'test-scenario', 1.0, 3600);

        // Default filter
        $this->assertSame('valid_to >= NOW', $command->jsonSerialize()['parameters']['filter']);

        // Add custom filters to the default one
        $command->addFilter('foo = bar')
            ->addFilter('bar = baz');

        $this->assertSame(
            'valid_to >= NOW and foo = bar and bar = baz',
            $command->jsonSerialize()['parameters']['filter']
        );

        // Overwrite all filters
        $command->setFilters(['my_filter = 1', 'other_filter = foo']);

        $this->assertSame('my_filter = 1 and other_filter = foo', $command->jsonSerialize()['parameters']['filter']);
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
}
