<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

/**
 * Deliver personalized recommendations for the given user.
 */
class ItemItemRecommendation extends AbstractItemRecommendation
{
    use ItemsFilteringTrait;

    /**
     * @param string $scenario Name of the place where recommendations are applied - eg. 'search-results-page',
     * 'emailing', 'empty-search-results, 'homepage', ...
     */
    public static function create(string $itemId, string $scenario): self
    {
        return new static($itemId, $scenario);
    }

    protected function getCommandType(): string
    {
        return 'item-item-recommendations';
    }

    protected function getCommandParameters(): array
    {
        $parameters = parent::getCommandParameters();
        $itemsFilterParameters = $this->getItemsFilterParameters();

        return array_merge($parameters, $itemsFilterParameters);
    }
}
