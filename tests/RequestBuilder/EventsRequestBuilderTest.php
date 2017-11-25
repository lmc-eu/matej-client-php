<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

class EventsRequestBuilderTest extends TestCase
{
    /** @test */
    public function shouldThrowExceptionWhenBuildingEmptyCommands(): void
    {
        $builder = new EventsRequestBuilder();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder');
        $builder->build();
    }

    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new EventsRequestBuilder();

        $itemPropertyCommand1 = ItemProperty::create('id-1', ['key1' => 'value1']);
        $itemPropertyCommand2 = ItemProperty::create('id-2', ['key1' => 'value3']);
        $itemPropertyCommand3 = ItemProperty::create('id-3', ['key1' => 'value3']);

        $builder->addItemProperty($itemPropertyCommand1);
        $builder->addItemProperties([$itemPropertyCommand2, $itemPropertyCommand3]);

        $userMergeCommand1 = UserMerge::mergeFromSourceToTargetUser('sourceId1', 'targetId1');
        $userMergeCommand2 = UserMerge::mergeFromSourceToTargetUser('sourceId2', 'targetId2');
        $userMergeCommand3 = UserMerge::mergeFromSourceToTargetUser('sourceId3', 'targetId3');

        $builder->addUserMerge($userMergeCommand1);
        $builder->addUserMerges([$userMergeCommand2, $userMergeCommand3]);

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/events', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(6, $requestData);
        $this->assertSame($itemPropertyCommand1, $requestData[0]);
        $this->assertSame($itemPropertyCommand2, $requestData[1]);
        $this->assertSame($itemPropertyCommand3, $requestData[2]);
        $this->assertSame($userMergeCommand1, $requestData[3]);
        $this->assertSame($userMergeCommand2, $requestData[4]);
        $this->assertSame($userMergeCommand3, $requestData[5]);
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
