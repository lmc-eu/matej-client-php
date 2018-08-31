<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static PropertyType INT()
 * @method static PropertyType DOUBLE()
 * @method static PropertyType STRING()
 * @method static PropertyType BOOLEAN()
 * @method static PropertyType TIMESTAMP()
 * @method static PropertyType SET()
 */
final class PropertyType extends Enum
{
    public const INT = 'int';
    public const DOUBLE = 'double';
    public const STRING = 'string';
    public const BOOLEAN = 'boolean';
    public const TIMESTAMP = 'timestamp';
    public const SET = 'set';
}
