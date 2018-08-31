<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static MinimalRelevance LOW()
 * @method static MinimalRelevance MEDIUM()
 * @method static MinimalRelevance HIGH()
 */
final class MinimalRelevance extends Enum
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
}
