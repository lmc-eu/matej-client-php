<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

/**
 * Sorting items is a way how to use Matej to deliver personalized experience to users.
 * It allows to sort given list of items according to the user preference.
 */
class Sorting extends AbstractCommand
{
    /** @var string */
    private $userId;
    /** @var string[] */
    private $itemIds = [];

    private function __construct(string $userId, array $itemIds)
    {
        $this->userId = $userId;
        $this->itemIds = $itemIds;
    }

    /**
     * Sort given item ids for user-based recommendations.
     */
    public static function create(string $userId, array $itemIds): self
    {
        return new static($userId, $itemIds);
    }

    public function getCommandType(): string
    {
        return 'sorting';
    }

    public function getCommandParameters(): array
    {
        return [
            'user_id' => $this->userId,
            'item_ids' => $this->itemIds,
        ];
    }
}
