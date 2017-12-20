<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\Model\Response\SortingResponse;

/**
 * @covers \Lmc\Matej\Model\Response\SortingResponse
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

        $this->assertInstanceOf(SortingResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
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

        $this->assertInstanceOf(SortingResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
        $this->assertShorthandResponse($response, 'OK', 'OK', 'OK');
    }

    private function assertShorthandResponse(SortingResponse $response, $interactionStatus, $userMergeStatus, $sortingStatus): void
    {
        $this->assertInstanceOf(CommandResponse::class, $response->getInteraction());
        $this->assertInstanceOf(CommandResponse::class, $response->getUserMerge());
        $this->assertInstanceOf(CommandResponse::class, $response->getSorting());
        $this->assertSame($interactionStatus, $response->getInteraction()->getStatus());
        $this->assertSame($userMergeStatus, $response->getUserMerge()->getStatus());
        $this->assertSame($sortingStatus, $response->getSorting()->getStatus());
    }
}
