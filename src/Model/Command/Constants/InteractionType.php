<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static InteractionType DETAILVIEWS()
 * @method static InteractionType PURCHASES()
 * @method static InteractionType BOOKMARKS()
 * @method static InteractionType RATINGS()
 */
final class InteractionType extends Enum
{
    public const DETAILVIEWS = 'detailviews';
    public const PURCHASES = 'purchases';
    public const BOOKMARKS = 'bookmarks';
    public const RATINGS = 'ratings';
}
