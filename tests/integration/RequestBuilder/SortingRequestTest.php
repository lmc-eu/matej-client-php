<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserMerge;

/**
 * @covers \Lmc\Matej\RequestBuilder\SortingRequestBuilder
 */
class SortingRequestTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteSortingRequestOnly(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->sorting(Sorting::create('user-a', ['itemA', 'itemB', 'itemC']))
            ->send();

        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteSortingRequestWithUserMergeAndInteraction(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->sorting(Sorting::create('user-a', ['item-a', 'item-b', 'itemC-c']))
            ->setUserMerge(UserMerge::mergeInto('user-a', 'user-b'))
            ->setInteraction(Interaction::bookmark('user-a', 'item-a'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
    }
}
