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

    private function __construct(string $userId, array $itemIds)
    {
        $this->setUserId($userId);
        $this->setItemIds($itemIds);
    }

    /**
     * Sort given item ids for user-based recommendations.
     *
     * @return static
     */
    public static function create(string $userId, array $itemIds): self
    {
        return new static($userId, $itemIds);
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
        return [
            'user_id' => $this->userId,
            'item_ids' => $this->itemIds,
        ];
    }
}
