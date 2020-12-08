<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Deliver recommendations.
 */
abstract class AbstractRecommendation extends AbstractCommand
{
    /** @var int */
    private $count;
    /** @var string */
    private $scenario;
    /** @var string|null */
    private $modelName;

    protected function __construct(string $scenario)
    {
        $this->setScenario($scenario);
    }

    /**
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

    /**
     * Set number of requested recommendations. The real number of recommended items could be lower or even zero when
     * there are no items relevant for the user.
     *
     * @return $this
     */
    public function setCount(int $count): self
    {
        Assertion::greaterThan($count, 0);

        $this->count = $count;

        return $this;
    }

    /**
     * Scenario name.
     */
    protected function setScenario(string $scenario): void
    {
        Assertion::typeIdentifier($scenario);

        $this->scenario = $scenario;
    }

    abstract protected function getCommandType(): string;

    protected function getCommandParameters(): array
    {
        $parameters = [
            'scenario' => $this->scenario,
        ];

        if ($this->count !== null) {
            $parameters['count'] = $this->count;
        }

        if ($this->modelName !== null) {
            $parameters['model_name'] = $this->modelName;
        }

        return $parameters;
    }
}
