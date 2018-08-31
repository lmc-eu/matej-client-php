<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;
use Lmc\Matej\Model\Command\Constants\UserForgetMethod;

/**
 * UserForget any user in Matej, either by anonymizing or by deleting their entries.
 * Anonymization and deletion is done server-side, and is GDPR-compliant. When anonymizing the data, new user-id is
 * generated server-side and client library won't ever know it.
 */
class UserForget extends AbstractCommand implements UserAwareInterface
{
    /** @var string */
    private $userId;
    /** @var UserForgetMethod */
    private $method;

    private function __construct(string $userId, UserForgetMethod $method)
    {
        $this->setUserId($userId);
        $this->method = $method;
    }

    /**
     * Anonymize all user data in Matej.
     */
    public static function anonymize(string $userId): self
    {
        return new static($userId, UserForgetMethod::ANONYMIZE());
    }

    /**
     * Completely wipe all user data from Matej.
     */
    public static function delete(string $userId): self
    {
        return new static($userId, UserForgetMethod::DELETE());
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getForgetMethod(): UserForgetMethod
    {
        return $this->method;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function getCommandType(): string
    {
        return 'user-forget';
    }

    protected function getCommandParameters(): array
    {
        return [
            'user_id' => $this->userId,
            'method' => $this->method->jsonSerialize(),
        ];
    }
}
