<?php declare(strict_types=1);

namespace Lmc\Matej;

use GuzzleHttp\Psr7\Request;
use Http\Discovery\Psr18Client;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\CommandResponse;

/**
 * @covers \Lmc\Matej\Matej
 */
class MatejTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiable(): void
    {
        $matej = new Matej('account-id', 'apiKey');
        $this->assertInstanceOf(Matej::class, $matej);
    }

    /** @test */
    public function shouldExecuteRequestViaBuilder(): void
    {
        $dummyHttpResponse = $this->createJsonResponseFromFile(
            __DIR__ . '/Http/Fixtures/response-one-successful-command.json'
        );

        $mockClient = $this->createMock(Psr18Client::class);
        $mockClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($dummyHttpResponse);

        $matej = new Matej('account-id', 'apiKey');
        $matej->setHttpClient($mockClient);

        $response = $matej->request()
            ->setupItemProperties()
            ->addProperty(ItemPropertySetup::timestamp('valid_from'))
            ->send();

        $this->assertSame(1, $response->getNumberOfCommands());
        $this->assertSame(1, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertCount(1, $response->getCommandResponses());
        $this->assertInstanceOf(CommandResponse::class, $response->getCommandResponses()[0]);
        $this->assertSame(CommandResponse::STATUS_OK, $response->getCommandResponses()[0]->getStatus());
    }

    /** @test */
    public function shouldOverwriteBaseUrl(): void
    {
        $dummyHttpResponse = $this->createJsonResponseFromFile(
            __DIR__ . '/Http/Fixtures/response-one-successful-command.json'
        );

        $mockClient = $this->createMock(Psr18Client::class);
        $mockClient->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (Request $request) {
                $this->assertStringStartsWith(
                    'https://nobody.nowhere.com/account-id',
                    $request->getUri()->__toString()
                );

                return true;
            }))
            ->willReturn($dummyHttpResponse);

        $matej = new Matej('account-id', 'apiKey');
        $matej->setHttpClient($mockClient);

        $matej->setBaseUrl('https://nobody.nowhere.com/%s');

        $matej->request()
            ->setupItemProperties()
            ->addProperty(ItemPropertySetup::timestamp('valid_from'))
            ->send();
    }
}
