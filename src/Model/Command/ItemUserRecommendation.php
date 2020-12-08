<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Command\Constants\UserMinimalRelevance;

/**
 * Deliver user recommendations for given item.
 */
class ItemUserRecommendation extends AbstractItemRecommendation
{
    /** @var bool */
    private $allowSeen = false;
    /** @var UserMinimalRelevance */
    private $minimalRelevance;

    /**
     * @param string $scenario Name of the place where recommendations are applied - eg. 'search-results-page',
     * 'emailing', 'empty-search-results, 'homepage', ...
     */
    public static function create(string $itemId, string $scenario): self
    {
        return new static($itemId, $scenario);
    }

    /**
     * Define threshold of how much relevant must the recommended items be to be returned.
     * Default minimal relevance is "low".
     */
    public function setMinimalRelevance(UserMinimalRelevance $minimalRelevance): self
    {
        $this->minimalRelevance = $minimalRelevance;

        return $this;
    }

    /**
     * Allow items, that the user has already "seen"
     *
     * By default user won't see any items, that it has visited (and we have recorded DetailView  interaction.)
     * If you want to circumvent this, and get recommendations including the ones, that the user has already visited,
     * you can set the "seen" allowance here.
     */
    public function setAllowSeen(bool $seen): self
    {
        $this->allowSeen = $seen;

        return $this;
    }

    protected function getCommandType(): string
    {
        return 'item-user-recommendations';
    }

    protected function getCommandParameters(): array
    {
        $parameters = parent::getCommandParameters();

        if ($this->allowSeen !== false) {
            $parameters['allow_seen'] = $this->allowSeen;
        }

        if ($this->minimalRelevance !== null) {
            $parameters['min_relevance'] = $this->minimalRelevance->jsonSerialize();
        }

        return $parameters;
    }
}
