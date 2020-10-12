<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserForget;
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

        // Make sure the builder has been properly configured and it can execute send() via RequestManager mock:
        $this->assertNotEmpty($builder->send());
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

        $forgetInit = function (ForgetRequestBuilder $builder): void {
            $builder->addUser(UserForget::anonymize('test-user-for-anonymization'));
        };

        $voidInit = function ($builder): void {};

        $userRecommendation = UserRecommendation::create('user-id', 'test-scenario')
            ->setCount(5)
            ->setRotationRate(0.5)
            ->setRotationTime(3600);

        return [
            'getItemProperties' => ['getItemProperties', ItemPropertiesGetRequestBuilder::class, $voidInit],
            'setupItemProperties' => [
                'setupItemProperties',
                ItemPropertiesSetupRequestBuilder::class,
                $itemPropertiesSetupInit,
            ],
            'deleteItemProperties' => [
                'deleteItemProperties',
                ItemPropertiesSetupRequestBuilder::class,
                $itemPropertiesSetupInit,
            ],
            'events' => ['events', EventsRequestBuilder::class, $eventInit],
            'campaign' => ['campaign', CampaignRequestBuilder::class, $campaignInit],
            'sorting' => [
                'sorting',
                SortingRequestBuilder::class,
                $voidInit,
                Sorting::create('user-a', ['item-a', 'item-b', 'item-c']),
            ],
            'recommendation' => ['recommendation', RecommendationRequestBuilder::class, $voidInit, $userRecommendation],
            'forget' => ['forget', ForgetRequestBuilder::class, $forgetInit],
            'resetDatabase' => ['resetDatabase', ResetDatabaseRequestBuilder::class, $voidInit],
        ];
    }
}
