<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command;
use Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder;

/**
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder
 */
class ItemPropertiesSetupRequestBuilderTest extends IntegrationTestCase
{
    /**
     * @test
     * @dataProvider provideBuilders
     */
    public function shouldThrowExceptionWhenSendingBlankRequests(ItemPropertiesSetupRequestBuilder $builder): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one ItemPropertySetup command must be added to the builder before sending the request');
        $builder->send();
    }

    public function provideBuilders(): array
    {
        return [
            'setup properties' => [$this->createMatejInstance()->request()->setupItemProperties()],
            'delete properties' => [$this->createMatejInstance()->request()->deleteItemProperties()],
        ];
    }

    /** @test */
    public function shouldCreateNewPropertiesInMatej(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->setupItemProperties()
            ->addProperty(Command\ItemPropertySetup::boolean('test_property_bool'))
            ->addProperty(Command\ItemPropertySetup::double('test_property_double'))
            ->addProperty(Command\ItemPropertySetup::int('test_property_int'))
            ->addProperty(Command\ItemPropertySetup::string('test_property_string'))
            ->addProperties([
                Command\ItemPropertySetup::timestamp('test_property_timestamp'),
                Command\ItemPropertySetup::set('test_property_set'),
            ])
            ->send();

        $this->assertSame(6, $response->getNumberOfCommands());
        $this->assertSame(6, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());
    }

    /**
     * @test
     * @depends shouldCreateNewPropertiesInMatej
     */
    public function shouldDeleteCreatedPropertiesFromMatej(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->deleteItemProperties()
            ->addProperty(Command\ItemPropertySetup::boolean('test_property_bool'))
            ->addProperty(Command\ItemPropertySetup::double('test_property_double'))
            ->addProperty(Command\ItemPropertySetup::int('test_property_int'))
            ->addProperty(Command\ItemPropertySetup::string('test_property_string'))
            ->addProperties([
                Command\ItemPropertySetup::timestamp('test_property_timestamp'),
                Command\ItemPropertySetup::set('test_property_set'),
            ])
            ->send();

        $this->assertSame(6, $response->getNumberOfCommands());
        $this->assertSame(6, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());
    }
}
