<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\ItemProperty;
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

        $command1 = ItemProperty::create('id-1', ['key1' => 'value1']);
        $command2 = ItemProperty::create('id-2', ['key1' => 'value3']);
        $command3 = ItemProperty::create('id-3', ['key1' => 'value3']);

        $builder->addItemProperty($command1);
        $builder->addItemProperties([$command2, $command3]);

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/events', $request->getPath());
        $this->assertContainsOnlyInstancesOf(ItemProperty::class, $request->getData());
        $this->assertSame($command1, $request->getData()[0]);
        $this->assertSame($command2, $request->getData()[1]);
        $this->assertSame($command3, $request->getData()[2]);
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

        $builder->send();
    }
}
