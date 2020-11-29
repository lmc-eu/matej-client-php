<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

/**
 * Deliver personalized recommendations for the given user.
 */
class UserUserRecommendation extends AbstractUserRecommendation
{
    /**
     * @param string $scenario Name of the place where recommendations are applied - eg. 'search-results-page',
     * 'emailing', 'empty-search-results, 'homepage', ...
     */
    public static function create(string $userId, string $scenario): self
    {
        return new static($userId, $scenario);
    }

    protected function getCommandType(): string
    {
        return 'user-user-recommendations';
    }
}
