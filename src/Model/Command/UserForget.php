<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Model\Assertion;

/**
 * UserForget any user in Matej, either by anonymizing or by deleting their entries.
 * Anonymization and deletion is done server-side, and is GDPR-compliant. When anonymizing the data, new user-id is
 * generated server-side and client library won't ever know it.
 */
class UserForget extends AbstractCommand implements UserAwareInterface
{
    public const ANONYMIZE = 'anonymize';
    public const DELETE = 'delete';

    /** @var string */
    private $userId;
    /** @var string */
    private $method;

    private function __construct(string $userId, string $method)
    {
        $this->setUserId($userId);
        $this->setForgetMethod($method);
    }

    /**
     * Anonymize all user data in Matej.
     */
    public static function anonymize(string $userId): self
    {
        return new static($userId, self::ANONYMIZE);
    }

    /**
     * Completely wipe all user data from Matej.
     */
    public static function delete(string $userId): self
    {
        return new static($userId, self::DELETE);
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getForgetMethod(): string
    {
        return $this->method;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function setForgetMethod(string $method): void
    {
        Assertion::choice($method, [self::ANONYMIZE, self::DELETE]);

        $this->method = $method;
    }

    protected function getCommandType(): string
    {
        return 'user-forget';
    }

    protected function getCommandParameters(): array
    {
        return [
            'user_id' => $this->userId,
            'method' => $this->method,
        ];
    }
}
