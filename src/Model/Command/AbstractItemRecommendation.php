<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Deliver recommendations for given item.
 */
abstract class AbstractItemRecommendation extends AbstractRecommendation
{
    /** @var string */
    private $itemId;

    protected function __construct(string $itemId, string $scenario)
    {
        parent::__construct($scenario);
        $this->setItemId($itemId);
    }

    protected function setItemId(string $itemId): void
    {
        Assertion::typeIdentifier($itemId);

        $this->itemId = $itemId;
    }

    protected function getCommandParameters(): array
    {
        $parameters = parent::getCommandParameters();

        $parameters['item_id'] = $this->itemId;

        return $parameters;
    }
}
