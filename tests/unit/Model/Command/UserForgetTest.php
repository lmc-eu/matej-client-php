<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\UnitTestCase;

class UserForgetTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructor(): void
    {
        $userId = 'user-id';

        $command = UserForget::anonymize($userId);
        $this->assertForgetCommand($command, $userId, UserForget::ANONYMIZE);

        $command = UserForget::delete($userId);
        $this->assertForgetCommand($command, $userId, UserForget::DELETE);
    }

    /**
     * Execute asserts against UserForget command
     *
     * @param UserForget $command
     */
    private function assertForgetCommand($command, string $userId, string $method): void
    {
        $this->assertInstanceOf(UserForget::class, $command);
        $this->assertSame(
            [
                'type' => 'user-forget',
                'parameters' => [
                    'user_id' => $userId,
                    'method' => $method,
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($userId, $command->getUserId());
        $this->assertSame($method, $command->getForgetMethod());
    }
}
