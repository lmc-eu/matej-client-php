<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\UnitTestCase;

class RecommendationsResponseTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider provideRecommendationResponseData
     */
    public function shouldBeInstantiable(array $recommendationResponseData): void
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
        $recommendationCommandResponse = (object) [
            'status' => CommandResponse::STATUS_OK,
            'message' => 'MOCK_RECOMMENDATION_MESSAGE',
            'data' => $recommendationResponseData,
        ];

        $response = new RecommendationsResponse(3, 3, 0, 0, [
            $interactionCommandResponse,
            $userMergeCommandResponse,
            $recommendationCommandResponse,
        ]);

        $this->assertTrue($response->getInteraction()->isSuccessful());
        $this->assertTrue($response->getUserMerge()->isSuccessful());
        $this->assertTrue($response->getRecommendation()->isSuccessful());

        $this->assertSame(CommandResponse::STATUS_OK, $response->getInteraction()->getStatus());
        $this->assertSame(CommandResponse::STATUS_OK, $response->getUserMerge()->getStatus());
        $this->assertSame(CommandResponse::STATUS_OK, $response->getRecommendation()->getStatus());

        $this->assertSame('MOCK_INTERACTION_MESSAGE', $response->getInteraction()->getMessage());
        $this->assertSame(['MOCK' => 'INTERACTION'], $response->getInteraction()->getData());

        $this->assertSame('MOCK_USER_MERGE_MESSAGE', $response->getUserMerge()->getMessage());
        $this->assertSame(['MOCK' => 'USER_MERGE'], $response->getUserMerge()->getData());

        $this->assertTrue($response->getRecommendation()->isSuccessful());
        $this->assertSame('MOCK_RECOMMENDATION_MESSAGE', $response->getRecommendation()->getMessage());
        $this->assertEquals([(object) ['item_id' => 'MOCK_ITEM_ID']], $response->getRecommendation()->getData());
    }

    public function provideRecommendationResponseData(): array
    {
        return [
            'complex response data' => [
                [(object) ['item_id' => 'MOCK_ITEM_ID']],
            ],
            'flat response data' => [
                ['MOCK_ITEM_ID'],
            ],
        ];
    }
}
