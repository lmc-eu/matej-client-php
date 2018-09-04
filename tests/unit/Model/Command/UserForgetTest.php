<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Command\Constants\UserForgetMethod;
use Lmc\Matej\UnitTestCase;

class UserForgetTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiableViaNamedConstructor(): void
    {
        $userId = 'user-id';

        $command = UserForget::anonymize($userId);
        $this->assertForgetCommand($command, $userId, UserForgetMethod::ANONYMIZE());

        $command = UserForget::delete($userId);
        $this->assertForgetCommand($command, $userId, UserForgetMethod::DELETE());
    }

    /**
     * Execute asserts against UserForget command
     *
     * @param UserForget $command
     */
    private function assertForgetCommand($command, string $userId, UserForgetMethod $method): void
    {
        $this->assertInstanceOf(UserForget::class, $command);
        $this->assertSame(
            [
                'type' => 'user-forget',
                'parameters' => [
                    'user_id' => $userId,
                    'method' => $method->jsonSerialize(),
                ],
            ],
            $command->jsonSerialize()
        );
        $this->assertSame($userId, $command->getUserId());
        $this->assertEquals($method, $command->getForgetMethod());
    }
}
