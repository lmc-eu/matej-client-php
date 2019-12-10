<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Lmc\Matej\Model\Command\UserAwareInterface;

/**
 * Exception represents error in the program logic.
 */
class LogicException extends \LogicException implements MatejExceptionInterface
{
    public static function forInconsistentUserId(
        UserAwareInterface $mainCommand,
        UserAwareInterface $additionalCommand
    ): self {
        $message = sprintf(
            'User in %s command ("%s") must be the same as user in %s command ("%s")',
            (new \ReflectionClass($additionalCommand))->getShortName(),
            $additionalCommand->getUserId(),
            (new \ReflectionClass($mainCommand))->getShortName(),
            $mainCommand->getUserId()
        );

        return new self($message);
    }

    public static function forInconsistentUserMergeAndInteractionCommand(
        string $userMergeId,
        string $interactionUserId
    ): self {
        $message = sprintf(
            'Source user in UserMerge command ("%s") must be the same as user in Interaction command ("%s")',
            $userMergeId,
            $interactionUserId
        );

        return new self($message);
    }

    public static function forClassNotExtendingOtherClass(string $class, string $wantedClass): self
    {
        return new self(sprintf('Class %s has to be instance or subclass of %s.', $class, $wantedClass));
    }
}
