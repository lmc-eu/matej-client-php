<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\ItemItemRecommendation;
use Lmc\Matej\Model\Command\ItemSorting;
use Lmc\Matej\Model\Command\ItemUserRecommendation;
use Lmc\Matej\Model\Command\UserItemRecommendation;
use Lmc\Matej\Model\Command\UserUserRecommendation;

/**
 * @covers \Lmc\Matej\RequestBuilder\CampaignRequestBuilder
 */
class CampaignRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldThrowExceptionWhenSendingBlankRequest(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('At least one command must be added to the builder before sending the request');

        static::createMatejInstance()
            ->request()
            ->campaign()
            ->send();
    }

    /** @test */
    public function shouldExecuteRecommendationAndSortingCommands(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->campaign()
            ->addRecommendation($this->createUserItemRecommendationCommand('a'))
            ->addRecommendation($this->createUserUserRecommendationCommand('b'))
            ->addRecommendations([
                $this->createItemItemRecommendationCommand('c'),
                $this->createItemUserRecommendationCommand('d'),
            ])
            ->addSorting($this->createSortingCommand('a'))
            ->addSortings([
                $this->createSortingCommand('b'),
                $this->createSortingCommand('c'),
            ])
            ->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(7));
    }

    private function createUserItemRecommendationCommand(string $letter): UserItemRecommendation
    {
        return UserItemRecommendation::create('user-' . $letter, 'integration-test-scenario')
            ->setCount(1)
            ->setRotationRate(1)
            ->setRotationTime(3600);
    }

    private function createUserUserRecommendationCommand(string $letter): UserUserRecommendation
    {
        return UserUserRecommendation::create('user-' . $letter, 'integration-test-scenario')
            ->setCount(1)
            ->setRotationRate(1)
            ->setRotationTime(3600);
    }

    private function createItemUserRecommendationCommand(string $letter): ItemUserRecommendation
    {
        return ItemUserRecommendation::create('item-' . $letter, 'integration-test-scenario')
            ->setCount(1)
            ->setAllowSeen(true);
    }

    private function createItemItemRecommendationCommand(string $letter): ItemItemRecommendation
    {
        return ItemItemRecommendation::create('item-' . $letter, 'integration-test-scenario')
            ->setCount(1);
    }

    private function createSortingCommand(string $letter): ItemSorting
    {
        return ItemSorting::create(
            'user-' . $letter,
            ['itemA', 'itemB', 'itemC']
        );
    }
}
