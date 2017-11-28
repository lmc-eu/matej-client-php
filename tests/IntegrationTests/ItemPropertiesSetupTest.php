<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Exception\RequestException;
use Lmc\Matej\Model\Command;
use Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder;

/**
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesSetupRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 */
class ItemPropertiesSetupTest extends IntegrationTestCase
{
    /**
     * In Matej's Mongo Worker, this will throw an error, because the property isn't defined.
     * The API will however return OK. This is desired behaviour.
     *
     * @test
     */
    public function shouldExecuteRequestContainingUnknownPropertyButPass(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->events()
            ->addItemProperty(
                Command\ItemProperty::create(
                    'integration-test-php-clientitem-id',
                    ['integration_test_php_client_property_1' => true, 'integration_test_php_client_property_2' => false]
                )
            )
            ->send();

        $this->assertSame(1, $response->getNumberOfCommands());
        $this->assertSame(1, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());

        $commandResponse = $response->getCommandResponses()[0];
        $this->assertSame('OK', $commandResponse->getStatus());
        $this->assertSame([], $commandResponse->getData());
        $this->assertSame('', $commandResponse->getMessage());
    }

    /**
     * @test
     * @depends shouldExecuteRequestContainingUnknownPropertyButPass
     */
    public function shouldCreateNewPropertiesInMatej(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->setupItemProperties()
            ->addProperty(Command\ItemPropertySetup::boolean('integration_test_php_client_bool'))
            ->addProperty(Command\ItemPropertySetup::double('integration_test_php_client_double'))
            ->addProperty(Command\ItemPropertySetup::int('integration_test_php_client_int'))
            ->addProperty(Command\ItemPropertySetup::string('integration_test_php_client_string'))
            ->addProperties([
                Command\ItemPropertySetup::timestamp('integration_test_php_client_timestamp'),
                Command\ItemPropertySetup::set('integration_test_php_client_set'),
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
    public function shouldExecuteEventWithJustCreatedProperties(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->events()
            ->addItemProperty(
                Command\ItemProperty::create(
                    'integration-test-php-clientitem-id',
                    [
                        'integration_test_php_client_bool' => true,
                        'integration_test_php_client_double' => 0.15,
                        'integration_test_php_client_int' => 15,
                        'integration_test_php_client_string' => 'some_string',
                        'integration_test_php_client_timestamp' => 123456789,
                        'integration_test_php_client_set' => ['some', 'set'],
                    ]
                )
            )
            ->send();

        $this->assertSame(1, $response->getNumberOfCommands());
        $this->assertSame(1, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());
    }

    /**
     * @test
     * @depends shouldExecuteEventWithJustCreatedProperties
     */
    public function shouldDeleteCreatedPropertiesFromMatej(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->deleteItemProperties()
            ->addProperty(Command\ItemPropertySetup::boolean('integration_test_php_client_bool'))
            ->addProperty(Command\ItemPropertySetup::double('integration_test_php_client_double'))
            ->addProperty(Command\ItemPropertySetup::int('integration_test_php_client_int'))
            ->addProperty(Command\ItemPropertySetup::string('integration_test_php_client_string'))
            ->addProperties([
                Command\ItemPropertySetup::timestamp('integration_test_php_client_timestamp'),
                Command\ItemPropertySetup::set('integration_test_php_client_set'),
            ])
            ->send();

        $this->assertSame(6, $response->getNumberOfCommands());
        $this->assertSame(6, $response->getNumberOfSuccessfulCommands());
        $this->assertSame(0, $response->getNumberOfFailedCommands());
        $this->assertSame(0, $response->getNumberOfSkippedCommands());
    }

    /**
     * @dataProvider provideBuilders
     * @test
     */
    public function shouldExecuteThreeThousandSetupCommandsAndFail(ItemPropertiesSetupRequestBuilder $builder): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('BAD REQUEST');

        for ($i = 0; $i < 3000; $i++) {
            $builder->addProperty(Command\ItemPropertySetup::boolean('integration_test_php_client_property_' . $i));
        }

        $builder->send();
    }

    public function provideBuilders(): array
    {
        return [
            'setup properties' => [$this->createMatejInstance()->request()->setupItemProperties()],
            'delete properties' => [$this->createMatejInstance()->request()->deleteItemProperties()],
        ];
    }
}
