<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\RequestBuilderFactory
 */
class RequestBuilderFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideBuilderMethods
     */
    public function shouldInstantiateBuilderToBuildAndSendRequest(
        string $factoryMethod,
        string $expectedBuilderClass,
        \Closure $minimalBuilderInit,
        ...$factoryArguments
    ): void {
        $requestManagerMock = $this->createMock(RequestManager::class);
        $requestManagerMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->isInstanceOf(Request::class))
            ->willReturn(new Response(0, 0, 0, 0));

        $factory = new RequestBuilderFactory($requestManagerMock);

        /** @var AbstractRequestBuilder $builder */
        $builder = $factory->$factoryMethod(...$factoryArguments);

        // Builders may require some minimal setup to be able to execute the build() method
        $minimalBuilderInit($builder);

        $this->assertInstanceOf($expectedBuilderClass, $builder);
        $this->assertInstanceOf(Request::class, $builder->build());

        // Make sure the builder has been properly configured and it can execute send() via RequestManager mock:
        $this->assertInstanceOf(Response::class, $builder->send());
    }

    /**
     * @return array[]
     */
    public function provideBuilderMethods(): array
    {
        $itemPropertiesSetupInit = function (ItemPropertiesSetupRequestBuilder $builder): void {
            $builder->addProperty(ItemPropertySetup::timestamp('valid_from'));
        };

        $eventInit = function (EventsRequestBuilder $builder): void {
            $builder->addItemProperty(ItemProperty::create('item-id', []));
        };

        $campaignInit = function (CampaignRequestBuilder $builder): void {
            $builder->addSorting(Sorting::create('item-id', ['item1', 'item2']));
        };

        $voidInit = function ($builder): void {};

        $userRecommendation = UserRecommendation::create('user-id', 1, 'test-scenario', 0.5, 3600);

        return [
            ['getItemProperties', ItemPropertiesGetRequestBuilder::class, $voidInit],
            ['setupItemProperties', ItemPropertiesSetupRequestBuilder::class, $itemPropertiesSetupInit],
            ['deleteItemProperties', ItemPropertiesSetupRequestBuilder::class, $itemPropertiesSetupInit],
            ['events', EventsRequestBuilder::class, $eventInit],
            ['campaign', CampaignRequestBuilder::class, $campaignInit],
            ['sorting', SortingRequestBuilder::class, $voidInit, Sorting::create('user-a', ['item-a', 'item-b', 'item-c'])],
            ['recommendation', RecommendationRequestBuilder::class, $voidInit, $userRecommendation],
        ];
    }
}
