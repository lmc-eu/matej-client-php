<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Deliver personalized recommendations for the given user.
 */
class UserRecommendation extends AbstractCommand implements UserAwareInterface
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
    /** @var string|null */
    private $modelName = null;

    private function __construct(string $userId, int $count, string $scenario, float $rotationRate, int $rotationTime, string $modelName = null)
    {
        $this->setUserId($userId);
        $this->setCount($count);
        $this->setScenario($scenario);
        $this->setRotationRate($rotationRate);
        $this->setRotationTime($rotationTime);

        if ($modelName !== null) {
            $this->setModelName($modelName);
        }
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
     * @param string|null $modelName Specify which model you want to use
     * @return static
     */
    public static function create(
        string $userId,
        int $count,
        string $scenario,
        float $rotationRate,
        int $rotationTime,
        string $modelName = null
    ): self {
        return new static($userId, $count, $scenario, $rotationRate, $rotationTime, $modelName);
    }

    /**
     * Even with rotation rate 1.0 user could still obtain the same recommendations in some edge cases.
     * To prevent this, enable hard rotation - recommended items are then excluded until rotation time is expired.
     * By default hard rotation is not enabled.
     *
     * @return $this
     */
    public function enableHardRotation(): self
    {
        $this->hardRotation = true;

        return $this;
    }

    /**
     * Define threshold of how much relevant must the recommended items be to be returned.
     * Default minimal relevance is "low".
     *
     * @return $this
     */
    public function setMinimalRelevance(string $minimalRelevance): self
    {
        Assertion::choice(
            $minimalRelevance,
            [static::MINIMAL_RELEVANCE_LOW, static::MINIMAL_RELEVANCE_MEDIUM, static::MINIMAL_RELEVANCE_HIGH]
        );

        $this->minimalRelevance = $minimalRelevance;

        return $this;
    }

    /**
     * Add a filter to already added filters (including the default filter).
     *
     * @return $this
     */
    public function addFilter(string $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Overwrite all filters by custom one. Note this will override also the default filter.
     *
     * @return $this
     */
    public function setFilters(array $filters): self
    {
        Assertion::allString($filters);

        $this->filters = $filters;

        return $this;
    }

    /***
     * Set A/B model name
     *
     * @return $this
     */
    public function setModelName(string $modelName): self
    {
        Assertion::typeIdentifier($modelName);

        $this->modelName = $modelName;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function setCount(int $count): void
    {
        Assertion::greaterThan($count, 0);

        $this->count = $count;
    }

    protected function setScenario(string $scenario): void
    {
        Assertion::typeIdentifier($scenario);

        $this->scenario = $scenario;
    }

    protected function setRotationRate(float $rotationRate): void
    {
        Assertion::between($rotationRate, 0, 1);

        $this->rotationRate = $rotationRate;
    }

    protected function setRotationTime(int $rotationTime): void
    {
        Assertion::greaterOrEqualThan($rotationTime, 0);

        $this->rotationTime = $rotationTime;
    }

    protected function assembleFiltersString(): string
    {
        return implode(' ' . $this->filterOperator . ' ', $this->filters);
    }

    protected function getCommandType(): string
    {
        return 'user-based-recommendations';
    }

    protected function getCommandParameters(): array
    {
        $parameters = [
            'user_id' => $this->userId,
            'count' => $this->count,
            'scenario' => $this->scenario,
            'rotation_rate' => $this->rotationRate,
            'rotation_time' => $this->rotationTime,
            'hard_rotation' => $this->hardRotation,
            'min_relevance' => $this->minimalRelevance,
            'filter' => $this->assembleFiltersString(),
        ];

        if ($this->modelName !== null) {
            $parameters['model_name'] = $this->modelName;
        }

        return $parameters;
    }
}
