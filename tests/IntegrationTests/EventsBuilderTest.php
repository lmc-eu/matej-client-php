<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Exception\RequestException;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\ItemProperty;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\RequestBuilder\EventsRequestBuilder;

/**
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 */
class EventsBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteInteractionCommandOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendInteractionCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(1));
    }

    /** @test */
    public function shouldExecuteUserMergeCommandOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendUserMergeCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(1));
    }

    /** @test */
    public function shouldExecuteItemPropertyCommandOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendItemPropertyCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(1));
    }

    /** @test */
    public function shouldExecuteInteractionAndUserMergeCommandsOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendInteractionCommands($builder);
        $this->appendUserMergeCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(2));
    }

    /** @test */
    public function shouldExecuteInteractionAndItemPropertyCommandsOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendInteractionCommands($builder);
        $this->appendItemPropertyCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(2));
    }

    /** @test */
    public function shouldExecuteUserMergeAndItemPropertyCommandsOnly(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendUserMergeCommands($builder);
        $this->appendItemPropertyCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(2));
    }

    /** @test */
    public function shouldExecuteInteractionAndUserMergeAndItemPropertyCommands(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendInteractionCommands($builder);
        $this->appendUserMergeCommands($builder);
        $this->appendItemPropertyCommands($builder);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(3));
    }

    /** @test */
    public function shouldExecuteOneThousandCommands(): void
    {
        $builder = $this->createMatejInstance()->request()->events();
        $this->appendInteractionCommands($builder, null, 1);
        $this->appendUserMergeCommands($builder, 1);
        $this->appendItemPropertyCommands($builder, 2);
        $this->appendInteractionCommands($builder, null, 332);
        $this->appendUserMergeCommands($builder, 332);
        $this->appendItemPropertyCommands($builder, 332);
        $response = $builder->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(1000));
    }

    /** @test */
    public function shouldExecuteThreeThousandCommandsAndFail(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('BAD REQUEST');

        try {
            $builder = $this->createMatejInstance()->request()->events();
            $this->appendInteractionCommands($builder, null, 1000);
            $this->appendUserMergeCommands($builder, 1000);
            $this->appendItemPropertyCommands($builder, 1000);
            $builder->send();
        } catch (RequestException $exception) {
            $this->assertContains(
                'Request cannot contain more than 1000 commands; 3000 was sent.',
                (string) $exception->getResponse()->getBody()
            );
            throw $exception;
        }
    }

    private function appendInteractionCommands(EventsRequestBuilder $builder, int $seed = null, int $amount = 1): void
    {
        $map = ['bookmark', 'detailView', 'purchase', 'rating'];
        for ($i = 0; $i < $amount; $i++) {
            $constructor = $map[($seed ?? $i) % 4];
            $builder->addInteraction(Interaction::$constructor('integration-test-php-client-user-id-' . $i, 'test-item-id'));
        }
    }

    private function appendUserMergeCommands(EventsRequestBuilder $builder, int $amount = 1): void
    {
        for ($i = 0; $i < $amount; $i++) {
            $builder->addUserMerge(UserMerge::mergeInto(
                'integration-test-php-client-target-user-id-' . $i,
                'integration-test-php-client-source-user-id-' . $i
            ));
        }
    }

    private function appendItemPropertyCommands(EventsRequestBuilder $builder, int $amount = 1): void
    {
        for ($i = 0; $i < $amount; $i++) {
            $builder->addItemProperty(
                ItemProperty::create('test-item-id' . $i, ['test_boolean' => (bool) ($i % 2)])
            );
        }
    }
}
