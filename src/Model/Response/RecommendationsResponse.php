<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\Model\Response;

class RecommendationsResponse extends Response
{
    public function getInteraction(): CommandResponse
    {
        return $this->getCommandResponse(0);
    }

    public function getUserMerge(): CommandResponse
    {
        return $this->getCommandResponse(1);
    }

    public function getRecommendation(): CommandResponse
    {
        return $this->getCommandResponse(2);
    }
}
