<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

trait ItemsFilteringTrait
{
    /** @var string */
    protected $filterOperator = 'and';
    /** @var string[] */
    private $filters;
    /** @var string[] */
    private $responseProperties;
    /** @var Boost[] */
    private $boosts = [];

    /**
     * Add a filter to already added filters (including the default filter).
     */
    public function addFilter(string $filter): self
    {
        if ($this->filters === null) {
            $this->filters = [];
        }
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Overwrite all filters by custom one. Note this will override also the default filter.
     */
    public function setFilters(array $filters): self
    {
        Assertion::allString($filters);

        $this->filters = $filters;

        return $this;
    }

    /**
     * Add another response property you want returned. item_id is always returned by Matej.
     */
    public function addResponseProperty(string $property): self
    {
        Assertion::typeIdentifier($property);

        if ($this->responseProperties === null) {
            $this->responseProperties = [];
        }
        $this->responseProperties[] = $property;

        return $this;
    }

    /**
     * Set all response properties you want returned. item_id is always returned by Matej, even when you don't specify
     * it.
     *
     * @param string[] $properties
     */
    public function setResponseProperties(array $properties): self
    {
        Assertion::allTypeIdentifier($properties);

        $this->responseProperties = $properties;

        return $this;
    }

    /**
     * Add a boost rule to already added rules.
     */
    public function addBoost(Boost $boost): self
    {
        $this->boosts[] = $boost;

        return $this;
    }

    /**
     * Set boosts. Removes all previously set rules.
     * @param Boost[] $boosts
     */
    public function setBoosts(array $boosts): self
    {
        $this->boosts = array_values($boosts);

        return $this;
    }

    protected function assembleFiltersString(): string
    {
        return implode(' ' . $this->filterOperator . ' ', $this->filters);
    }

    protected function getSerializedBoosts(): array
    {
        return array_map(
            static function (Boost $boost) {
                return $boost->jsonSerialize();
            },
            $this->boosts
        );
    }

    protected function getItemsFilterParameters(): array
    {
        $parameters = [];

        if (!empty($this->boosts)) {
            $parameters['boost_rules'] = $this->getSerializedBoosts();
        }

        if ($this->filters !== null) {
            $parameters['filter'] = $this->assembleFiltersString();
        }

        if ($this->responseProperties !== null) {
            $parameters['properties'] = $this->responseProperties;
        }

        return $parameters;
    }
}
