<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use PHPUnit\Framework\TestCase;

class UserUserRecommendationTest extends TestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructorWithDefaultValues(): void
    {
        $command = UserUserRecommendation::create('user-id', 'test-scenario');

        $this->assertEquals(
            [
                'type' => 'user-user-recommendations',
                'parameters' => [
                    'user_id' => 'user-id',
                    'scenario' => 'test-scenario',
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

        $command = UserUserRecommendation::create($userId, $scenario)
            ->setCount($count)
            ->setRotationRate($rotationRate)
            ->setRotationTime($rotationTime)
            ->enableHardRotation()
            ->setModelName($modelName);

        $this->assertEquals(
            [
                'type' => 'user-user-recommendations',
                'parameters' => [
                    'user_id' => $userId,
                    'count' => $count,
                    'scenario' => $scenario,
                    'rotation_rate' => $rotationRate,
                    'rotation_time' => $rotationTime,
                    'hard_rotation' => true,
                    'model_name' => $modelName,
                ],
            ],
            $command->jsonSerialize()
        );
    }
}
