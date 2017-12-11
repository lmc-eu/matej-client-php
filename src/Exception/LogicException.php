<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Lmc\Matej\Model\Command\UserAwareInterface;

/**
 * Exception represents error in the program logic.
 */
class LogicException extends \LogicException implements MatejExceptionInterface
{
    public static function forInconsistentUserId(UserAwareInterface $mainCommand, UserAwareInterface $additionalCommand)
    {
        $message = sprintf(
            'User in %s command ("%s") must be the same as user in %s command ("%s")',
            (new \ReflectionClass($additionalCommand))->getShortName(),
            $additionalCommand->getUserId(),
            (new \ReflectionClass($mainCommand))->getShortName(),
            $mainCommand->getUserId()
        );

        return new self($message);
    }

    public static function forClassNotExtendingOtherClass($class, $wantedClass)
    {
        return new self(sprintf('Class %s has to be instance or subclass of %s.', $class, $wantedClass));
    }
}
