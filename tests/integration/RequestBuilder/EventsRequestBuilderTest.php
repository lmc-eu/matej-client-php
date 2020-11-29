<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Matej;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Command\UserMerge;

/**
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 */
class EventsRequestBuilderTest extends IntegrationTestCase
{
    private static function getPropertiesList(): array
    {
        return [
            'test_property_a',
            'test_property_b',
            'test_property_c',
        ];
    }

    public static function setUpBeforeClass(): void
    {
        $matej = static::createMatejInstance();
        static::setupItemProperties($matej);
        static::waitForItemPropertiesSetup($matej);
    }

    public static function tearDownAfterClass(): void
    {
        $matej = static::createMatejInstance();
        static::removeItemProperties($matej);
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

    private static function setupItemProperties(Matej $matej): void
    {
        $request = $matej->request()->setupItemProperties();
        foreach (static::getPropertiesList() as $property) {
            $request->addProperty(ItemPropertySetup::string($property));
        }
        $request->send();
    }

    private static function waitForItemPropertiesSetup(Matej $matej): void
    {
        while (true) {
            $request = $matej->request()->getItemProperties();
            $resp = $request->send();

            $properties = [];
            foreach ($resp->getData() as $property) {
                $properties[] = $property->name;
            }

            if (!array_diff(static::getPropertiesList(), $properties)) {
                return;
            }
            usleep(1000000); # 0.1s
        }
    }

    private static function removeItemProperties(Matej $matej): void
    {
        $request = $matej->request()->deleteItemProperties();
        foreach (static::getPropertiesList() as $property) {
            $request->addProperty(ItemPropertySetup::string($property));
        }
        $request->send();
    }
}
