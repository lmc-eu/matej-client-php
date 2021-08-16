<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * Take all interactions from the source user and merge them to the target user.
 * Source user will be DELETED and unknown to Matej from this action.
 */
class UserMerge extends AbstractCommand implements UserAwareInterface
{
    /** @var string */
    private $sourceUserId;
    /** @var string */
    private $targetUserId;
    /** @var int */
    private $timestamp;

    private function __construct(string $targetUserId, string $sourceUserId, int $timestamp = null)
    {
        $this->setTargetUserId($targetUserId);
        $this->setSourceUserId($sourceUserId);
        $this->setTimestamp($timestamp ?? time());

        $this->assertUserIdsNotEqual();
    }

    /**
     * Merge source user into target user AND DELETE SOURCE USER.
     *
     * @return static
     */
    public static function mergeInto(string $targetUserId, string $sourceUserIdToBeDeleted, int $timestamp = null): self
    {
        return new static($targetUserId, $sourceUserIdToBeDeleted, $timestamp);
    }

    /**
     * Merge source user into target user AND DELETE SOURCE USER.
     *
     * @return static
     */
    public static function mergeFromSourceToTargetUser(string $sourceUserIdToBeDeleted, string $targetUserId, int $timestamp = null): self
    {
        return new static($targetUserId, $sourceUserIdToBeDeleted, $timestamp);
    }

    public function getUserId(): string
    {
        return $this->targetUserId;
    }

    public function getSourceUserId(): string
    {
        return $this->sourceUserId;
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

    protected function setTimestamp(int $timestamp): void
    {
        Assertion::greaterThan($timestamp, 0);

        $this->timestamp = $timestamp;
    }

    private function assertUserIdsNotEqual(): void
    {
        Assertion::notEq(
            $this->sourceUserId,
            $this->targetUserId,
            'You have to provide different source and target user id in UserMerge ("%s" set for both)'
        );
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
            'timestamp' => $this->timestamp,
        ];
    }
}
