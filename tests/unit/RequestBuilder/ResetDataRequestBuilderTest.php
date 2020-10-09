<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Lmc\Matej\UnitTestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\ResetDataRequestBuilder
 */
class ResetDataRequestBuilderTest extends UnitTestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new ResetDataRequestBuilder();
        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertSame(RequestMethodInterface::METHOD_DELETE, $request->getMethod());
        $this->assertSame('/data', $request->getPath());
        $this->assertEmpty($request->getData());
        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Instance of RequestManager must be set to request builder');

        $builder = new ResetDataRequestBuilder();
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

        $builder = new ResetDataRequestBuilder();
        $builder->setRequestManager($requestManagerMock);
        $builder->send();
    }
}
