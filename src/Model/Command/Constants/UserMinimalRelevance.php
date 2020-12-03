<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static UserMinimalRelevance MEDIUM()
 * @method static UserMinimalRelevance HIGH()
 */
final class UserMinimalRelevance extends Enum
{
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
}
