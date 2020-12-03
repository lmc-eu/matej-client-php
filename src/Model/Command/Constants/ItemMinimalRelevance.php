<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static ItemMinimalRelevance LOW()
 * @method static ItemMinimalRelevance MEDIUM()
 * @method static ItemMinimalRelevance HIGH()
 */
final class ItemMinimalRelevance extends Enum
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
}
