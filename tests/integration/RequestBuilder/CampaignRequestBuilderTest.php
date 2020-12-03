<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserItemRecommendation;

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
            ->addRecommendation($this->createRecommendationCommand('a'))
            ->addRecommendations([
                $this->createRecommendationCommand('b'),
                $this->createRecommendationCommand('c'),
            ])
            ->addSorting($this->createSortingCommand('a'))
            ->addSortings([
                $this->createSortingCommand('b'),
                $this->createSortingCommand('c'),
            ])
            ->send();

        $this->assertResponseCommandStatuses($response, ...$this->generateOkStatuses(6));
    }

    private function createRecommendationCommand(string $letter): UserItemRecommendation
    {
        return UserItemRecommendation::create('user-' . $letter, 'integration-test-scenario')
            ->setCount(1)
            ->setRotationRate(1)
            ->setRotationTime(3600);
    }

    private function createSortingCommand(string $letter): Sorting
    {
        return Sorting::create(
            'user-' . $letter,
            ['itemA', 'itemB', 'itemC']
        );
    }
}
