<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Lmc\Matej\Model\Response\RecommendationsResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 */
class RecommendationRequestBuilderTest extends TestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $recommendationsCommand = UserRecommendation::create('userId1', 5, 'test-scenario', 0.5, 3600);
        $builder = new RecommendationRequestBuilder($recommendationsCommand);

        $interactionCommand = Interaction::detailView('userId1', 'itemId1');
        $builder->setInteraction($interactionCommand);

        $userMergeCommand = UserMerge::mergeFromSourceToTargetUser('sourceId1', 'userId1');
        $builder->setUserMerge($userMergeCommand);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/recommendations', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(3, $requestData);
        $this->assertSame($interactionCommand, $requestData[0]);
        $this->assertSame($userMergeCommand, $requestData[1]);
        $this->assertSame($recommendationsCommand, $requestData[2]);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
        $this->assertSame(RecommendationsResponse::class, $request->getResponseClass());
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $recommendationsCommand = UserRecommendation::create('userId1', 5, 'test-scenario', 0.5, 3600);
        $builder = new RecommendationRequestBuilder($recommendationsCommand);

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

        $builder = new SortingRequestBuilder(Sorting::create('userId1', ['itemId1', 'itemId2']));
        $builder->setRequestManager($requestManagerMock);
        $builder->send();
    }

    /** @test */
    public function shouldThrowExceptionWhenUserOfInteractionDiffersFromSorting(): void
    {
        $builder = new RecommendationRequestBuilder(
            $recommendationsCommand = UserRecommendation::create('userId1', 5, 'scenario', 0.5, 3600)
        );

        $builder->setInteraction(Interaction::purchase('different-user', 'itemId1'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'User in Interaction command ("different-user") must be the same as user in UserRecommendation command '
            . '("userId1")'
        );
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenUserOfUserMergeDiffersFromSorting(): void
    {
        $builder = new RecommendationRequestBuilder(
            $recommendationsCommand = UserRecommendation::create('userId1', 5, 'scenario', 0.5, 3600)
        );

        $builder->setUserMerge(UserMerge::mergeInto('different-user', 'userId1'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'User in UserMerge command ("different-user") must be the same as user in UserRecommendation command'
            . ' ("userId1")'
        );
        $builder->build();
    }
}
