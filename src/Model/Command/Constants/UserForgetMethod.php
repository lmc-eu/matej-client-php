<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static UserForgetMethod ANONYMIZE()
 * @method static UserForgetMethod DELETE()
 */
final class UserForgetMethod extends Enum
{
    public const ANONYMIZE = 'anonymize';
    public const DELETE = 'delete';
}
