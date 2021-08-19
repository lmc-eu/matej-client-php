<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Assert\InvalidArgumentException;
use Lmc\Matej\UnitTestCase;

class UserMergeTest extends UnitTestCase
{
    /** @test */
    public function shouldGenerateCorrectSignature(): void
    {
        $sourceUserId = 'source-user';
        $targetUserId = 'target-user';
        $timestamp = 1629362296;

        $command = UserMerge::mergeInto($targetUserId, $sourceUserId, $timestamp);
        $this->assertUserMergeCommand($command, $sourceUserId, $targetUserId, $timestamp);

        $command = UserMerge::mergeFromSourceToTargetUser($sourceUserId, $targetUserId, $timestamp);
        $this->assertUserMergeCommand($command, $sourceUserId, $targetUserId, $timestamp);
    }

    public function shouldThrowExceptionWhenMergingSameUsers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You have to provide different source and target user id in UserMerge ("test-user" set for both)');

        UserMerge::mergeInto('test-user', 'test-user');
    }

    /**
     * Execute asserts against user merge command
     *
     * @param UserMerge $command
     */
    private function assertUserMergeCommand($command, string $sourceUserId, string $targetUserId, int $timestamp): void
    {
        $this->assertInstanceOf(UserMerge::class, $command);
        $this->assertSame(
            [
                'type' => 'user-merge',
                'parameters' => [
                    'target_user_id' => $targetUserId,
                    'source_user_id' => $sourceUserId,
                    'timestamp' => $timestamp,
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($targetUserId, $command->getUserId());
        $this->assertSame($sourceUserId, $command->getSourceUserId());
    }
}
