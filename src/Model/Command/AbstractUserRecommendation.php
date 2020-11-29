<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Deliver personalized recommendations for the given user.
 */
abstract class AbstractUserRecommendation extends AbstractRecommendation implements UserAwareInterface
{
    /** @var string */
    private $userId;
    /** @var float */
    private $rotationRate;
    /** @var int */
    private $rotationTime;
    /** @var bool */
    private $hardRotation;
    /** @var bool */
    private $allowSeen = false;

    protected function __construct(string $userId, string $scenario)
    {
        parent::__construct($scenario);
        $this->setUserId($userId);
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

    /**
     * Get id of user requesting recommendation.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Set how much should the item be penalized for being recommended again in the near future.
     */
    public function setRotationRate(float $rotationRate): self
    {
        Assertion::between($rotationRate, 0, 1);

        $this->rotationRate = $rotationRate;

        return $this;
    }

    /**
     * Specify for how long will the item's rotationRate be taken in account and so the item is penalized for
     * recommendations.
     */
    public function setRotationTime(int $rotationTime): self
    {
        Assertion::greaterOrEqualThan($rotationTime, 0);

        $this->rotationTime = $rotationTime;

        return $this;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function getCommandParameters(): array
    {
        $parameters = parent::getCommandParameters();

        $parameters['user_id'] = $this->userId;

        if ($this->rotationRate !== null) {
            $parameters['rotation_rate'] = $this->rotationRate;
        }

        if ($this->rotationRate !== null) {
            $parameters['rotation_time'] = $this->rotationTime;
        }

        if ($this->allowSeen !== false) {
            $parameters['allow_seen'] = $this->allowSeen;
        }

        if ($this->hardRotation !== null) {
            $parameters['hard_rotation'] = $this->hardRotation;
        }

        return $parameters;
    }
}
