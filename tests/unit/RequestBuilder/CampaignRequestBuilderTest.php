<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\CampaignRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 */
class CampaignRequestBuilderTest extends TestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new CampaignRequestBuilder();

        $recommendationCommand1 = UserRecommendation::create('userId1', 1, 'scenario1', 1.0, 600);
        $recommendationCommand2 = UserRecommendation::create('userId2', 2, 'scenario2', 0.5, 700);
        $recommendationCommand3 = UserRecommendation::create('userId3', 3, 'scenario3', 0.0, 800);
        $builder->addRecommendation($recommendationCommand1);
        $builder->addRecommendations([$recommendationCommand2, $recommendationCommand3]);

        $sortingCommand1 = Sorting::create('userId1', ['itemId1', 'itemId2']);
        $sortingCommand2 = Sorting::create('userId2', ['itemId2', 'itemId3']);
        $sortingCommand3 = Sorting::create('userId3', ['itemId3', 'itemId4']);

        $builder->addSorting($sortingCommand1);
        $builder->addSortings([$sortingCommand2, $sortingCommand3]);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/campaign', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(6, $requestData);
        $this->assertContains($recommendationCommand1, $requestData);
        $this->assertContains($recommendationCommand2, $requestData);
        $this->assertContains($recommendationCommand3, $requestData);
        $this->assertContains($sortingCommand1, $requestData);
        $this->assertContains($sortingCommand2, $requestData);
        $this->assertContains($sortingCommand3, $requestData);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /** @test */
    public function shouldThrowExceptionWhenBuildingEmptyCommands(): void
    {
        $builder = new CampaignRequestBuilder();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenBatchSizeIsTooBig(): void
    {
        $builder = new CampaignRequestBuilder();

        for ($i = 0; $i < 501; $i++) {
            $builder->addRecommendation(UserRecommendation::create('userId1', 1, 'scenario1', 1.0, 600));
            $builder->addSorting(Sorting::create('userId1', ['itemId1', 'itemId2']));
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Request contains 1002 commands, but at most 1000 is allowed in one request.');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $builder = new CampaignRequestBuilder();

        $builder->addSorting(Sorting::create('userId1', ['itemId1', 'itemId2']));

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

        $builder = new CampaignRequestBuilder();
        $builder->setRequestManager($requestManagerMock);

        $builder->addRecommendation(UserRecommendation::create('userId1', 1, 'scenario1', 1.0, 3600));
        $builder->addSorting(Sorting::create('userId1', ['itemId1', 'itemId2']));

        $builder->send();
    }
}
