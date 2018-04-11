<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Sorting items is a way how to use Matej to deliver personalized experience to users.
 * It allows to sort given list of items according to the user preference.
 */
class Sorting extends AbstractCommand implements UserAwareInterface
{
    /** @var string */
    private $userId;
    /** @var string[] */
    private $itemIds = [];
    /** @var string|null */
    private $modelName = null;

    private function __construct(string $userId, array $itemIds, string $modelName = null)
    {
        $this->setUserId($userId);
        $this->setItemIds($itemIds);

        if ($modelName !== null) {
            $this->setModelName($modelName);
        }
    }

    /**
     * Sort given item ids for user-based recommendations.
     *
     * @return static
     */
    public static function create(string $userId, array $itemIds, string $modelName = null): self
    {
        return new static($userId, $itemIds, $modelName);
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

    public function getUserId(): string
    {
        return $this->userId;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function setItemIds(array $itemIds): void
    {
        Assertion::allTypeIdentifier($itemIds);

        $this->itemIds = $itemIds;
    }

    protected function getCommandType(): string
    {
        return 'sorting';
    }

    protected function getCommandParameters(): array
    {
        $parameters = [
            'user_id' => $this->userId,
            'item_ids' => $this->itemIds,
        ];

        if ($this->modelName !== null) {
            $parameters['model_name'] = $this->modelName;
        }

        return $parameters;
    }
}
