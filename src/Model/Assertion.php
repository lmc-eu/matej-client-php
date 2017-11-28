<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\DomainException;

/**
 * @method static bool allTypeIdentifier(mixed $value) Assert value is valid Matej type identifier for all values
 */
class Assertion extends \Assert\Assertion
{
    protected static $exceptionClass = DomainException::class;

    /**
     * Assert value is valid Matej type identifier
     * @param mixed $value
     */
    public static function typeIdentifier($value): bool
    {
        static::regex(
            $value,
            '/^[0-9A-Za-z-_]+$/',
            'Value "%s" does not match type identifier format requirement (must contain only of alphanumeric chars,'
            . ' dash or underscore)'
        );
        static::maxLength($value, 100);

        return true;
    }
}
