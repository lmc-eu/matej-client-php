<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\UnitTestCase;

class SortingResponseTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiable(): void
    {
        $interactionCommandResponse = (object) [
            'status' => CommandResponse::STATUS_OK,
            'message' => 'MOCK_INTERACTION_MESSAGE',
            'data' => ['MOCK' => 'INTERACTION'],
        ];
        $userMergeCommandResponse = (object) [
            'status' => CommandResponse::STATUS_OK,
            'message' => 'MOCK_USER_MERGE_MESSAGE',
            'data' => ['MOCK' => 'USER_MERGE'],
        ];
        $sortingCommandReponse = (object) [
            'status' => CommandResponse::STATUS_OK,
            'message' => 'MOCK_SORTING_MESSAGE',
            'data' => ['MOCK' => 'SORTING'],
        ];

        $response = new SortingResponse(3, 3, 0, 0, [$interactionCommandResponse, $userMergeCommandResponse, $sortingCommandReponse]);

        $this->assertTrue($response->getInteraction()->isSuccessful());
        $this->assertTrue($response->getUserMerge()->isSuccessful());
        $this->assertTrue($response->getSorting()->isSuccessful());

        $this->assertSame(CommandResponse::STATUS_OK, $response->getInteraction()->getStatus());
        $this->assertSame(CommandResponse::STATUS_OK, $response->getUserMerge()->getStatus());
        $this->assertSame(CommandResponse::STATUS_OK, $response->getSorting()->getStatus());

        $this->assertSame('MOCK_INTERACTION_MESSAGE', $response->getInteraction()->getMessage());
        $this->assertSame(['MOCK' => 'INTERACTION'], $response->getInteraction()->getData());

        $this->assertSame('MOCK_USER_MERGE_MESSAGE', $response->getUserMerge()->getMessage());
        $this->assertSame(['MOCK' => 'USER_MERGE'], $response->getUserMerge()->getData());

        $this->assertSame('MOCK_SORTING_MESSAGE', $response->getSorting()->getMessage());
        $this->assertSame(['MOCK' => 'SORTING'], $response->getSorting()->getData());
    }
}
