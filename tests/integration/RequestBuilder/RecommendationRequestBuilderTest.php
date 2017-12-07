<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;

/**
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteRecommendationRequestOnly(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand())
            ->send();

        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithUserMergeAndInteraction(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand())
            ->setUserMerge(UserMerge::mergeInto('user-a', 'user-b'))
            ->setInteraction(Interaction::bookmark('user-a', 'item-a'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
    }

    private function createRecommendationCommand(): UserRecommendation
    {
        return UserRecommendation::create(
            'user-a',
            5,
            'integration-test-scenario',
            0.50,
            3600
        );
    }
}
