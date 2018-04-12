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
    private function buildAndSendPropertySetupRequest(ItemPropertiesSetupRequestBuilder $builder): void
    {
        $builder->addProperty(ItemPropertySetup::string('test_property_a'))
            ->addProperty(ItemPropertySetup::string('test_property_b'))
            ->addProperty(ItemPropertySetup::string('test_property_c'))
            ->send();
    }

    protected function setup(): void
    {
        $builder = $this->createMatejInstance()->request()->setupItemProperties();

        $this->buildAndSendPropertySetupRequest($builder);
    }

    protected function tearDown(): void
    {
        $builder = $this->createMatejInstance()->request()->deleteItemProperties();

        $this->buildAndSendPropertySetupRequest($builder);
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingBlankRequest(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder before sending the request');

        $this->createMatejInstance()
            ->request()
            ->events()
            ->send();
    }

    /** @test */
    public function shouldExecuteInteractionAndUserMergeAndItemPropertyCommands(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(Interaction::bookmark('user-a', 'item-a'))
            ->addInteractions([
                Interaction::detailView('user-b', 'item-a'),
                Interaction::rating('user-c', 'item-a'),
                Interaction::purchase('user-d', 'item-a'),
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

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(10));
    }
}
