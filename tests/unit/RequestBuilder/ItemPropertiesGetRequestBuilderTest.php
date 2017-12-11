<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Lmc\Matej\UnitTestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesGetRequestBuilder
 */
class ItemPropertiesGetRequestBuilderTest extends UnitTestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new ItemPropertiesGetRequestBuilder();
        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(RequestMethodInterface::METHOD_GET, $request->getMethod());
        $this->assertSame('/item-properties', $request->getPath());
        $this->assertEmpty($request->getData());
        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Instance of RequestManager must be set to request builder');

        $builder = new ItemPropertiesGetRequestBuilder();
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

        $builder = new ItemPropertiesGetRequestBuilder();
        $builder->setRequestManager($requestManagerMock);
        $builder->send();
    }
}
