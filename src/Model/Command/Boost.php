<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Boosting items is a way how to modify results returend by Matej by specifying
 * rules to increase items relevance.
 */
class Boost
{
    /** @var string */
    private $query;
    /** @var float */
    private $multiplier;

    private function __construct(string $query, float $multiplier)
    {
        $this->setQuery($query);
        $this->setMultiplier($multiplier);
    }

    /**
     * Create boost rule to prioritize items
     *
     * @return static
     */
    public static function create(string $query, float $multiplier): self
    {
        return new static($query, $multiplier);
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function setMultiplier(float $multiplier): void
    {
        Assertion::greaterThan($multiplier, 0);

        $this->multiplier = $multiplier;
    }

    public function jsonSerialize(): array
    {
        return [
            'query' => $this->query,
            'multiplier' => $this->multiplier,
        ];
    }
}
