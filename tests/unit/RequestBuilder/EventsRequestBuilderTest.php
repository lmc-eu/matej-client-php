<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 */
class EventsRequestBuilderTest extends TestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new EventsRequestBuilder();

        $interactionCommand1 = Interaction::detailView('userId1', 'itemId1');
        $interactionCommand2 = Interaction::bookmark('userId1', 'itemId1');
        $interactionCommand3 = Interaction::purchase('userId1', 'itemId1');
        $builder->addInteraction($interactionCommand1);
        $builder->addInteractions([$interactionCommand2, $interactionCommand3]);

        $itemPropertyCommand1 = ItemProperty::create('itemId1', ['key1' => 'value1']);
        $itemPropertyCommand2 = ItemProperty::create('itemId2', ['key1' => 'value3']);
        $itemPropertyCommand3 = ItemProperty::create('itemId3', ['key1' => 'value3']);
        $builder->addItemProperty($itemPropertyCommand1);
        $builder->addItemProperties([$itemPropertyCommand2, $itemPropertyCommand3]);

        $userMergeCommand1 = UserMerge::mergeFromSourceToTargetUser('sourceId1', 'targetId1');
        $userMergeCommand2 = UserMerge::mergeFromSourceToTargetUser('sourceId2', 'targetId2');
        $userMergeCommand3 = UserMerge::mergeFromSourceToTargetUser('sourceId3', 'targetId3');
        $builder->addUserMerge($userMergeCommand1);
        $builder->addUserMerges([$userMergeCommand2, $userMergeCommand3]);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/events', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(9, $requestData);
        $this->assertContains($interactionCommand1, $requestData);
        $this->assertContains($interactionCommand2, $requestData);
        $this->assertContains($interactionCommand3, $requestData);
        $this->assertContains($itemPropertyCommand1, $requestData);
        $this->assertContains($itemPropertyCommand2, $requestData);
        $this->assertContains($itemPropertyCommand3, $requestData);
        $this->assertContains($userMergeCommand1, $requestData);
        $this->assertContains($userMergeCommand2, $requestData);
        $this->assertContains($userMergeCommand3, $requestData);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /** @test */
    public function shouldThrowExceptionWhenBuildingEmptyCommands(): void
    {
        $builder = new EventsRequestBuilder();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenBatchSizeIsTooBig(): void
    {
        $builder = new EventsRequestBuilder();

        for ($i = 0; $i < 334; $i++) {
            $builder->addInteraction(Interaction::detailView('userId1', 'itemId1'));
            $builder->addItemProperty(ItemProperty::create('itemId1', ['key1' => 'value1']));
            $builder->addUserMerge(UserMerge::mergeFromSourceToTargetUser('sourceId1', 'targetId1'));
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Request contains 1002 commands, but at most 1000 is allowed in one request.');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $builder = new EventsRequestBuilder();

        $builder->addItemProperty(ItemProperty::create('id-1', ['key1' => 'value1']));

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

        $builder = new EventsRequestBuilder();
        $builder->setRequestManager($requestManagerMock);

        $builder->addItemProperty(ItemProperty::create('id-1', ['key1' => 'value1']));
        $builder->addUserMerge(UserMerge::mergeFromSourceToTargetUser('sourceId', 'targetId'));

        $builder->send();
    }
}
