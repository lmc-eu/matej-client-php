<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Command\Constants\ItemMinimalRelevance;

/**
 * Deliver item recommendations for given user.
 */
class UserItemRecommendation extends AbstractUserRecommendation
{
    use ItemsFilteringTrait;

    /** @var ItemMinimalRelevance */
    private $minimalRelevance;
    /** @var bool */
    private $allowSeen = false;

    /**
     * @param string $scenario Name of the place where recommendations are applied - eg. 'search-results-page',
     * 'emailing', 'empty-search-results, 'homepage', ...
     */
    public static function create(string $userId, string $scenario): self
    {
        return new static($userId, $scenario);
    }

    /**
     * Define threshold of how much relevant must the recommended items be to be returned.
     * Default minimal relevance is "low".
     */
    public function setMinimalRelevance(ItemMinimalRelevance $minimalRelevance): self
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
     *
     * @return $this
     */
    public function setAllowSeen(bool $seen): self
    {
        $this->allowSeen = $seen;

        return $this;
    }

    protected function getCommandType(): string
    {
        return 'user-item-recommendations';
    }

    protected function getCommandParameters(): array
    {
        $parameters = parent::getCommandParameters();

        if ($this->minimalRelevance !== null) {
            $parameters['min_relevance'] = $this->minimalRelevance->jsonSerialize();
        }

        if ($this->allowSeen !== false) {
            $parameters['allow_seen'] = $this->allowSeen;
        }

        $itemsFilterParameters = $this->getItemsFilterParameters();

        return array_merge($parameters, $itemsFilterParameters);
    }
}
