<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\AbstractRecommendation;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemItemRecommendation;
use Lmc\Matej\Model\Command\ItemSorting;
use Lmc\Matej\Model\Command\ItemUserRecommendation;
use Lmc\Matej\Model\Command\UserItemRecommendation;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserUserRecommendation;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Lmc\Matej\Model\Response\RecommendationsResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideRecommendationCommand
     */
    public function shouldBuildRequestWithCommands(
        AbstractRecommendation $recommendationCommand
    ): void {
        $builder = new RecommendationRequestBuilder($recommendationCommand);

        $interactionCommand = Interaction::withItem('detailviews', 'sourceId1', 'itemId1');
        $builder->setInteraction($interactionCommand);

        $userMergeCommand = UserMerge::mergeFromSourceToTargetUser('sourceId1', 'user_id');
        $builder->setUserMerge($userMergeCommand);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/recommendations', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(3, $requestData);
        $this->assertSame($interactionCommand, $requestData[0]);
        $this->assertSame($userMergeCommand, $requestData[1]);
        $this->assertSame($recommendationCommand, $requestData[2]);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
        $this->assertSame(RecommendationsResponse::class, $request->getResponseClass());
    }

    /**
     * @test
     * @dataProvider provideRecommendationCommand
     */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(
        AbstractRecommendation $recommendationCommand
    ): void {
        $builder = new RecommendationRequestBuilder($recommendationCommand);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Instance of RequestManager must be set to request builder');
        $builder->send();
    }

    /** @test */
    public function shouldSendRequestViaRequestManager(): void
    {
        $requestManagerMock = $this->createMock(RequestManager::class);
        $requestManagerMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->isInstanceOf(Request::class))
            ->willReturn(new Response(0, 0, 0, 0));

        $builder = new SortingRequestBuilder(ItemSorting::create('userId1', ['itemId1', 'itemId2']));
        $builder->setRequestManager($requestManagerMock);
        $builder->send();
    }

    /**
     * @test
     * @dataProvider provideUserRecommendationCommand
     */
    public function shouldThrowExceptionWhenInteractionIsForUnrelatedUser(
        AbstractRecommendation $recommendationCommand
    ): void {
        $builder = new RecommendationRequestBuilder($recommendationCommand);

        $builder->setInteraction(Interaction::withItem('purchases', 'different-user', 'itemId1'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'User in Interaction command ("different-user") must be the same as user in '
            . (new \ReflectionClass($recommendationCommand))->getShortName() . ' command '
            . '("user_id")'
        );
        $builder->build();
    }

    /**
     * @test
     * @dataProvider provideUserRecommendationCommand
     */
    public function shouldThrowExceptionWhenMergeIsForUnrelatedUser(
        AbstractRecommendation $recommendationCommand
    ): void {
        $builder = new RecommendationRequestBuilder($recommendationCommand);

        $builder->setUserMerge(UserMerge::mergeInto('different-user', 'user_id'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'User in UserMerge command ("different-user") must be the same as user in '
            . (new \ReflectionClass($recommendationCommand))->getShortName() . ' command'
            . ' ("user_id")'
        );
        $builder->build();
    }

    /**
     * ([interaction], [user merge], [recommendation]): (A, A -> B, B)
     *
     * @test
     * @dataProvider provideCorrectSequenceOfUsers
     */
    public function shouldPassOnCorrectSequenceOfUsersWhenMerging(
        string $interactionUser,
        string $sourceUserToBeDeleted,
        string $targetUserId,
        string $recommendationUser
    ): void {
        $interactionCommand = Interaction::withItem('purchases', $interactionUser, 'test-item-id');
        $userMergeCommand = UserMerge::mergeFromSourceToTargetUser($sourceUserToBeDeleted, $targetUserId);
        $recommendationsCommand = UserItemRecommendation::create($recommendationUser, 'scenario')
            ->setCount(5)
            ->setRotationRate(0.5)
            ->setRotationTime(3600);

        $builder = new RecommendationRequestBuilder($recommendationsCommand);
        $builder->setUserMerge($userMergeCommand);
        $builder->setInteraction($interactionCommand);
        $this->assertNotEmpty($builder->build());
    }

    /**
     * ([interaction], [user merge], [recommendation]): (A, B -> A, B)
     *
     * @test
     */
    public function shouldFailOnIncorrectSequenceOfUsersWhenMerging(): void
    {
        $interactionCommand = Interaction::withItem('purchases', 'test-user-a', 'test-item-id');
        $userMergeCommand = UserMerge::mergeFromSourceToTargetUser('test-user-b', 'test-user-a');
        $recommendationsCommand = UserItemRecommendation::create('test-user-b', 'scenario')
            ->setCount(5)
            ->setRotationRate(0.5)
            ->setRotationTime(3600);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Source user in UserMerge command ("test-user-b") must be the same as user in Interaction command ("test-user-a")'
        );

        $builder = new RecommendationRequestBuilder($recommendationsCommand);
        $builder->setUserMerge($userMergeCommand);
        $builder->setInteraction($interactionCommand);
        $builder->build();
    }

    /**
     * @return array[]
     */
    public function provideCorrectSequenceOfUsers(): array
    {
        return [
           '(A, A -> B, B)' => ['test-user-a', 'test-user-a', 'test-user-b', 'test-user-b'],
           '(B, A -> B, B)' => ['test-user-b', 'test-user-a', 'test-user-b', 'test-user-b'],
        ];
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

    private function createUserItemRecommendationCommand(): UserItemRecommendation
    {
        return UserItemRecommendation::create('user_id', 'integration-test-scenario')
            ->setCount(5)
            ->setRotationRate(0.50)
            ->setRotationTime(3600);
    }

    private function createUserUserRecommendationCommand(): UserUserRecommendation
    {
        return UserUserRecommendation::create('user_id', 'integration-test-scenario')
            ->setCount(5)
            ->setRotationRate(0.50)
            ->setRotationTime(3600);
    }

    private function createItemUserRecommendationCommand(): ItemUserRecommendation
    {
        return ItemUserRecommendation::create('item_id', 'integration-test-scenario')
            ->setCount(5);
    }

    private function createItemItemRecommendationCommand(): ItemItemRecommendation
    {
        return ItemItemRecommendation::create('item_id', 'integration-test-scenario')
            ->setCount(5);
    }
}
