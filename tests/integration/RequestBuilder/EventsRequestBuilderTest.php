<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder;

/**
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 */
class EventsRequestBuilderTest extends IntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        $request = static::createMatejInstance()->request()->setupItemProperties();

        static::addPropertiesToPropertySetupRequest($request);

        $request->send();
    }

    public static function tearDownAfterClass(): void
    {
        $request = static::createMatejInstance()->request()->deleteItemProperties();

        static::addPropertiesToPropertySetupRequest($request);

        $request->send();
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingBlankRequest(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder before sending the request');

        static::createMatejInstance()
            ->request()
            ->events()
            ->send();
    }

    /** @test */
    public function shouldExecuteInteractionAndUserMergeAndItemPropertyCommands(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(Interaction::withItem('search', 'user-a', 'item-a'))
            ->addInteractions([
                Interaction::withItem('detailview', 'user-b', 'item-a'),
                Interaction::withItem('purchase', 'user-d', 'item-a'),
            ])
            ->addUserMerge(UserMerge::mergeInto('user-a', 'user-b'))
            ->addUserMerges([
                UserMerge::mergeInto('user-a', 'user-c'),
                UserMerge::mergeInto('user-a', 'user-d'),
            ])
            ->addItemProperty(ItemProperty::create('item-a', ['test_property_a' => 'test-value-a']))
            ->addItemProperties([
                ItemProperty::create('item-a', ['test_property_b' => 'test-value-b']),
                ItemProperty::create('item-a', ['test_property_c' => 'test-value-c']),
            ])
            ->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(9));
    }

    private static function addPropertiesToPropertySetupRequest(ItemPropertiesSetupRequestBuilder $builder): void
    {
        $builder->addProperties([
            ItemPropertySetup::string('test_property_a'),
            ItemPropertySetup::string('test_property_b'),
            ItemPropertySetup::string('test_property_c'),
        ]);
    }
}
