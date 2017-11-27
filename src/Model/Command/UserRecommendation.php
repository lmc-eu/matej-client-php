<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

/**
 * Deliver personalized recommendations for the given user.
 */
class UserRecommendation extends AbstractCommand
{
    public const MINIMAL_RELEVANCE_LOW = 'low';
    public const MINIMAL_RELEVANCE_MEDIUM = 'medium';
    public const MINIMAL_RELEVANCE_HIGH = 'high';

    /** @var string */
    protected $filterOperator = 'and';
    /** @var string */
    private $userId;
    /** @var int */
    private $count;
    /** @var string */
    private $scenario;
    /** @var float */
    private $rotationRate;
    /** @var int */
    private $rotationTime;
    /** @var bool */
    private $hardRotation = false;
    /** @var string */
    private $minimalRelevance = self::MINIMAL_RELEVANCE_LOW;
    /** @var array */
    private $filters = ['valid_to >= NOW'];

    private function __construct(string $userId, int $count, string $scenario, float $rotationRate, int $rotationTime)
    {
        $this->userId = $userId; // TODO: assert format
        $this->count = $count; // TODO: assert greater than 0
        $this->scenario = $scenario; // TODO: assert format
        $this->rotationRate = $rotationRate; // TODO: assert value between 0.0 and 1.0
        $this->rotationTime = $rotationTime; // TODO: assert valid time interval
    }

    /**
     * @param string $userId
     * @param int $count Number of requested recommendations. The real number of recommended items could be lower or
     * even zero when there are no items relevant for the user.
     * @param string $scenario Name of the place where recommendations are applied - eg. 'search-results-page',
     * 'emailing', 'empty-search-results, 'homepage', ...
     * @param float $rotationRate How much should the item be penalized for being recommended again in the near future.
     * Set from 0.0 for no rotation (same items will be recommended) up to 1.0 (same items should not be recommended).
     * @param int $rotationTime Specify for how long will the item's rotationRate be taken in account and so the item
     * is penalized for recommendations.
     * @return UserRecommendation
     */
    public static function create(
        string $userId,
        int $count,
        string $scenario,
        float $rotationRate,
        int $rotationTime
    ): self {
        return new static($userId, $count, $scenario, $rotationRate, $rotationTime);
    }

    /**
     * Even with rotation rate 1.0 user could still obtain the same recommendations in some edge cases.
     * To prevent this, enable hard rotation - recommended items are then excluded until rotation time is expired.
     * By default hard rotation is not enabled.
     */
    public function enableHardRotation(): self
    {
        $this->hardRotation = true;

        return $this;
    }

    /**
     * Define threshold of how much relevant must the recommended items be to be returned.
     * Default minimal relevance is "low".
     */
    public function setMinimalRelevance(string $minimalRelevance): self
    {
        // TODO: assert one of MIN_RELEVANCE_*
        $this->minimalRelevance = $minimalRelevance;

        return $this;
    }

    /**
     * Add a filter to already added filters (including the default filter).
     */
    public function addFilter(string $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Overwrite all filters by custom one. Note this will override also the default filter.
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    protected function getCommandType(): string
    {
        return 'user-based-recommendations';
    }

    protected function getCommandParameters(): array
    {
        return [
            'user_id' => $this->userId,
            'count' => $this->count,
            'scenario' => $this->scenario,
            'rotation_rate' => $this->rotationRate,
            'rotation_time' => $this->rotationTime,
            'hard_rotation' => $this->hardRotation,
            'min_relevance' => $this->minimalRelevance,
            'filter' => $this->assembleFiltersString(),
        ];
    }

    protected function assembleFiltersString(): string
    {
        return implode(' ' . $this->filterOperator . ' ', $this->filters);
    }
}
