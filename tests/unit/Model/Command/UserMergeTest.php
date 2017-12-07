<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\UnitTestCase;

class UserMergeTest extends UnitTestCase
{
    /** @test */
    public function shouldGenerateCorrectSignature(): void
    {
        $sourceUserId = 'source-user';
        $targetUserId = 'target-user';

        $command = UserMerge::mergeInto($targetUserId, $sourceUserId);
        $this->assertUserMergeCommand($command, $sourceUserId, $targetUserId);

        $command = UserMerge::mergeFromSourceToTargetUser($sourceUserId, $targetUserId);
        $this->assertUserMergeCommand($command, $sourceUserId, $targetUserId);
    }

    /**
     * Execute asserts against user merge command
     * @param UserMerge $command
     */
    private function assertUserMergeCommand($command, string $sourceUserId, string $targetUserId): void
    {
        $this->assertInstanceOf(UserMerge::class, $command);
        $this->assertSame(
            [
                'type' => 'user-merge',
                'parameters' => [
                    'target_user_id' => $targetUserId,
                    'source_user_id' => $sourceUserId,
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($targetUserId, $command->getUserId());
    }
}
