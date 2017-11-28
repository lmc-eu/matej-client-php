<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Take all interactions from the source user and merge them to the target user.
 * Source user will be DELETED and unknown to Matej from this action.
 */
class UserMerge extends AbstractCommand
{
    /** @var string */
    private $sourceUserId;
    /** @var string */
    private $targetUserId;

    private function __construct(string $targetUserId, string $sourceUserId)
    {
        $this->setTargetUserId($targetUserId);
        $this->setSourceUserId($sourceUserId);
    }

    /**
     * Merge source user into target user AND DELETE SOURCE USER.
     */
    public static function mergeInto(string $targetUserId, string $sourceUserIdToBeDeleted): self
    {
        return new static($targetUserId, $sourceUserIdToBeDeleted);
    }

    /**
     * Merge source user into target user AND DELETE SOURCE USER.
     */
    public static function mergeFromSourceToTargetUser(string $sourceUserIdToBeDeleted, string $targetUserId): self
    {
        return new static($targetUserId, $sourceUserIdToBeDeleted);
    }

    protected function setSourceUserId(string $sourceUserId): void
    {
        Assertion::typeIdentifier($sourceUserId);

        $this->sourceUserId = $sourceUserId;
    }

    protected function setTargetUserId(string $targetUserId): void
    {
        Assertion::typeIdentifier($targetUserId);

        $this->targetUserId = $targetUserId;
    }

    protected function getCommandType(): string
    {
        return 'user-merge';
    }

    protected function getCommandParameters(): array
    {
        return [
            'target_user_id' => $this->targetUserId,
            'source_user_id' => $this->sourceUserId,
        ];
    }
}
