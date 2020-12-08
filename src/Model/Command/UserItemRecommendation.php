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

        $itemsFilterParameters = $this->getItemsFilterParameters();

        return array_merge($parameters, $itemsFilterParameters);
    }
}
