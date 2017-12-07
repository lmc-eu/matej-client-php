<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 */
class ItemPropertiesSetupRequestBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideBuilderVariants
     */
    public function shouldBuildRequestWithCommands(bool $shouldDelete, string $expectedMethod): void
    {
        $builder = new ItemPropertiesSetupRequestBuilder($shouldDelete);

        $command1 = ItemPropertySetup::timestamp('property1');
        $command2 = ItemPropertySetup::string('property2');
        $command3 = ItemPropertySetup::string('property3');

        $builder->addProperty($command1);
        $builder->addProperties([$command2, $command3]);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame($expectedMethod, $request->getMethod());
        $this->assertSame('/item-properties', $request->getPath());
        $this->assertContainsOnlyInstancesOf(ItemPropertySetup::class, $request->getData());
        $this->assertSame($command1, $request->getData()[0]);
        $this->assertSame($command2, $request->getData()[1]);
        $this->assertSame($command3, $request->getData()[2]);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /**
     * @return array[]
     */
    public function provideBuilderVariants(): array
    {
        return [
            'builder to create item properties' => [false, RequestMethodInterface::METHOD_PUT],
            'builder to delete item properties' => [true, RequestMethodInterface::METHOD_DELETE],
        ];
    }

    /** @test */
    public function shouldThrowExceptionWhenBuildingEmptyCommands(): void
    {
        $builder = new ItemPropertiesSetupRequestBuilder();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one ItemPropertySetup command must be added to the builder');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenBatchSizeIsTooBig(): void
    {
        $builder = new ItemPropertiesSetupRequestBuilder();

        for ($i = 0; $i < 1001; $i++) {
            $builder->addProperty(ItemPropertySetup::timestamp('property1'));
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Request contains 1001 commands, but at most 1000 is allowed in one request.');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $builder = new ItemPropertiesSetupRequestBuilder();

        $builder->addProperty(ItemPropertySetup::timestamp('property1'));

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

        $builder = new ItemPropertiesSetupRequestBuilder();
        $builder->setRequestManager($requestManagerMock);

        $builder->addProperty(ItemPropertySetup::timestamp('property1'));

        $builder->send();
    }
}
