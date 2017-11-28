<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Exception\RequestException;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserRecommendation;

/**
 * @covers \Lmc\Matej\RequestBuilder\CampaignRequestBuilder
 */
class CampaignBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteRecommendationCommandOnly(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->campaign()
            ->addRecommendation($this->createRecommendationCommand())
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
    }

    /** @test */
    public function shouldExecuteSortingCommandOnly(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->campaign()
            ->addSorting(Sorting::create('integration-test-php-client-user-id-A', ['itemA', 'itemB', 'itemC']))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationAndSortingCommands(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->campaign()
            ->addRecommendation($this->createRecommendationCommand())
            ->addSorting(Sorting::create('integration-test-php-client-user-id-A', ['itemA', 'itemB', 'itemC']))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK', 'OK');
    }

    /** @test */
    public function shouldExecuteThreeThousandCommandsAndFail(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('BAD REQUEST');

        $builder = $this->createMatejInstance()->request()->campaign();
        for ($i = 0; $i < 3000; $i++) {
            $builder->addSorting(Sorting::create('integration-test-php-client-user-id-A', ['itemA', 'itemB', 'itemC']));
        }
        $builder->send();
    }

    private function createRecommendationCommand(): UserRecommendation
    {
        return UserRecommendation::create(
            'integration-test-php-client-user-id-A',
            1,
            'integration-test-scenario',
            1,
            3600
        );
    }
}
