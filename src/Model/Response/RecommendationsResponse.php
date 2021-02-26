<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\Model\RecommendationCommandResponse;
use Lmc\Matej\Model\Response;

class RecommendationsResponse extends Response
{
    private const INTERACTION_INDEX = 0;
    private const USER_MERGE_INDEX = 1;
    private const RECOMMENDATION_INDEX = 2;

    public function getInteraction(): CommandResponse
    {
        return $this->getCommandResponse(static::INTERACTION_INDEX);
    }

    public function getUserMerge(): CommandResponse
    {
        return $this->getCommandResponse(static::USER_MERGE_INDEX);
    }

    public function getRecommendation(): CommandResponse
    {
        return $this->getCommandResponse(static::RECOMMENDATION_INDEX);
    }

    protected function decodeRawCommandResponses(array $commandResponses): array
    {
        $decodedResponses = [];
        foreach ($commandResponses as $index => $rawCommandResponse) {
            if ($index === static::RECOMMENDATION_INDEX) {
                $decodedResponses[] = RecommendationCommandResponse::createFromRawCommandResponseObject(
                    $rawCommandResponse
                );
            } else {
                $decodedResponses[] = CommandResponse::createFromRawCommandResponseObject($rawCommandResponse);
            }
        }

        return $decodedResponses;
    }
}
