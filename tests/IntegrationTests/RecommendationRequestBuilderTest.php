<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\RequestBuilder\RecommendationRequestBuilder;

/**
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteRecommendationRequestOnly(): void
    {
        $response = $this->createRecommendationRequestBuilder()
            ->send();

        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithInteraction(): void
    {
        $response = $this->createRecommendationRequestBuilder()
            ->setInteraction(Interaction::bookmark('integration-test-php-client-user-id-A', 'itemA'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithUserMerge(): void
    {
        $response = $this->createRecommendationRequestBuilder()
            ->setUserMerge(UserMerge::mergeInto('integration-test-php-client-user-id-A', 'integration-test-php-client-user-id-B'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'OK', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithUserMergeAndInteraction(): void
    {
        $response = $this->createRecommendationRequestBuilder()
            ->setUserMerge(UserMerge::mergeInto('integration-test-php-client-user-id-A', 'integration-test-php-client-user-id-B'))
            ->setInteraction(Interaction::bookmark('integration-test-php-client-user-id-A', 'itemA'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
    }

    private function createRecommendationRequestBuilder(): RecommendationRequestBuilder
    {
        return $this->createMatejInstance()
            ->request()
            ->recommendation(UserRecommendation::create(
                'integration-test-php-client-user-id-A',
                5,
                'integration-test-scenario',
                0.50,
                3600
            ))
        ;
    }
}
