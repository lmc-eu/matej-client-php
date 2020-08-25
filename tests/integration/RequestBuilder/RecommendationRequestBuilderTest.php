<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command;
use Lmc\Matej\Model\Command\Boost;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\Model\Response\RecommendationsResponse;

/**
 * @covers \Lmc\Matej\Model\Response\RecommendationsResponse
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteRecommendationRequestOnly(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand('user-a')
                ->addBoost(Boost::create('age > 1', 1.2))
            )->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithUserMergeAndInteraction(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand('user-b'))
            ->setUserMerge(UserMerge::mergeInto('user-b', 'user-a'))
            ->setInteraction(
                Interaction::withItem('detailview', 'user-a', 'item-a')
            )
            ->send();
        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
        $this->assertShorthandResponse($response, 'OK', 'OK', 'OK');
    }

    /** @test */
    public function shouldReturnInvalidCommandOnInvalidModelName(): void
    {
        $recommendation = $this->createRecommendationCommand('user-a')
            ->setModelName('invalid-model-name');

        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'INVALID');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'INVALID');
    }

    /** @test */
    public function shouldReturnInvalidCommandOnInvalidPropertyName(): void
    {
        $recommendation = $this->createRecommendationCommand('user-a')
            ->addResponseProperty('unknown-property');

        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'INVALID');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'INVALID');
    }

    /** @test */
    public function shouldFilterByItemProperties(): void
    {
        $matej = static::createMatejInstance();

        $response = $matej
            ->request()
            ->setupItemProperties()
            ->addProperty(Command\ItemPropertySetup::boolean('for_recommendation'))
            ->send();
        $this->assertSame(1, $response->getNumberOfCommands());
        $this->assertSame(1, $response->getNumberOfSuccessfulCommands());
        $response = $matej
            ->request()
            ->recommendation($this->createRecommendationCommand('user-a')
                ->addFilter('for_recommendation = 1')
            )->send();
        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    private function createRecommendationCommand(string $username): UserRecommendation
    {
        return UserRecommendation::create($username, 'integration-test-scenario')
            ->setCount(5)
            ->setRotationRate(0.50)
            ->setRotationTime(3600);
    }

    private function assertShorthandResponse(
        RecommendationsResponse $response,
        string $interactionStatus,
        string $userMergeStatus,
        string $recommendationStatus
    ): void {
        $this->assertInstanceOf(CommandResponse::class, $response->getInteraction());
        $this->assertInstanceOf(CommandResponse::class, $response->getUserMerge());
        $this->assertInstanceOf(CommandResponse::class, $response->getRecommendation());
        $this->assertSame($interactionStatus, $response->getInteraction()->getStatus());
        $this->assertSame($userMergeStatus, $response->getUserMerge()->getStatus());
        $this->assertSame($recommendationStatus, $response->getRecommendation()->getStatus());
    }
}
