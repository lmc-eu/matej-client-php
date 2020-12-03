<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command;
use Lmc\Matej\Model\Command\AbstractRecommendation;
use Lmc\Matej\Model\Command\AbstractUserRecommendation;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemItemRecommendation;
use Lmc\Matej\Model\Command\ItemUserRecommendation;
use Lmc\Matej\Model\Command\UserItemRecommendation;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserUserRecommendation;
use Lmc\Matej\Model\Response\RecommendationsResponse;

/**
 * @covers \Lmc\Matej\Model\Response\RecommendationsResponse
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends IntegrationTestCase
{
    /**
     * @test
     * @dataProvider provideRecommendationCommand
     */
    public function shouldExecuteRecommendationRequestOnly(
        AbstractRecommendation $recommendationCommand
    ): void {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendationCommand)
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /**
     * @test
     * @dataProvider provideUserRecommendationCommand
     */
    public function shouldExecuteRecommendationRequestWithUserMergeAndInteraction(
        AbstractUserRecommendation $recommendationCommand
    ): void {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendationCommand)
            ->setUserMerge(UserMerge::mergeInto('user-id', 'user-id2'))
            ->setInteraction(
                Interaction::withItem('detailview', 'user-id2', 'user-id2')
            )
            ->send();
        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
        $this->assertShorthandResponse($response, 'OK', 'OK', 'OK');
    }

    /**
     * @test
     * @dataProvider provideRecommendationCommand
     */
    public function shouldReturnInvalidCommandOnInvalidModelName(
        AbstractRecommendation $recommendationCommand
    ): void {
        $recommendation = $recommendationCommand->setModelName('invalid-model-name');

        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'INVALID');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'INVALID');
    }

    /**
     * @test
     * @dataProvider provideItemFilteringCommand
     * @param mixed $recommendationCommand
     */
    public function shouldReturnInvalidCommandOnInvalidPropertyName($recommendationCommand): void
    {
        $recommendation = $recommendationCommand->addResponseProperty('unknown-property');

        $response = static::createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'INVALID');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'INVALID');
    }

    /**
     * @test
     * @dataProvider provideItemFilteringCommand
     * @param mixed $recommendationCommand
     */
    public function shouldFilterByItemProperties($recommendationCommand): void
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
            ->recommendation($recommendationCommand->addFilter('for_recommendation = 1'))
            ->send();
        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    private function assertShorthandResponse(
        RecommendationsResponse $response,
        string $interactionStatus,
        string $userMergeStatus,
        string $recommendationStatus
    ): void {
        $this->assertSame($interactionStatus, $response->getInteraction()->getStatus());
        $this->assertSame($userMergeStatus, $response->getUserMerge()->getStatus());
        $this->assertSame($recommendationStatus, $response->getRecommendation()->getStatus());
    }

    public function provideRecommendationCommand(): array
    {
        return [
            'user-item' => [$this->createUserItemRecommendationCommand()],
            'user-user' => [$this->createUserUserRecommendationCommand()],
            'item-item' => [$this->createItemItemRecommendationCommand()],
            'item-user' => [$this->createItemUserRecommendationCommand()],
        ];
    }

    public function provideUserRecommendationCommand(): array
    {
        return [
            'user-item' => [$this->createUserItemRecommendationCommand()],
            'user-user' => [$this->createUserUserRecommendationCommand()],
        ];
    }

    public function provideItemFilteringCommand(): array
    {
        return [
            'user-item' => [$this->createUserItemRecommendationCommand()],
            'item-item' => [$this->createItemItemRecommendationCommand()],
        ];
    }

    private function createUserItemRecommendationCommand(): UserItemRecommendation
    {
        return UserItemRecommendation::create('user-id', 'integration-test-scenario')
            ->setCount(5)
            ->setRotationRate(0.50)
            ->setRotationTime(3600);
    }

    private function createUserUserRecommendationCommand(): UserUserRecommendation
    {
        return UserUserRecommendation::create('user-id', 'integration-test-scenario')
            ->setCount(5)
            ->setRotationRate(0.50)
            ->setRotationTime(3600);
    }

    private function createItemUserRecommendationCommand(): ItemUserRecommendation
    {
        return ItemUserRecommendation::create('item-id', 'integration-test-scenario')
            ->setCount(5);
    }

    private function createItemItemRecommendationCommand(): ItemItemRecommendation
    {
        return ItemItemRecommendation::create('item-id', 'integration-test-scenario')
            ->setCount(5);
    }
}
