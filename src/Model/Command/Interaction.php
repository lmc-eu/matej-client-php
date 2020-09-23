<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use ArrayObject;
use Lmc\Matej\Model\Assertion;

/**
 * Interaction command allows to send one interaction between a user and item.
 * When given user or item identifier is unknown, Matej will create such user or item respectively.
 */
class Interaction extends AbstractCommand implements UserAwareInterface
{
    private const DEFAULT_ITEM_ID_ALIAS = 'item_id';

    /** @var string */
    private $interactionType;
    /** @var string */
    private $userId;
    /** @var string */
    private $itemId;
    /** @var string */
    private $itemIdAlias;
    /** @var int */
    private $timestamp;
    /** @var ArrayObject */
    private $attributes;

    private function __construct(
        string $interactionType,
        string $userId,
        string $itemIdAlias,
        string $itemId,
        int $timestamp = null
    ) {
        $this->attributes = new ArrayObject();
        $this->setInteractionType($interactionType);
        $this->setUserId($userId);
        $this->setItemIdAlias($itemIdAlias);
        $this->setItemId($itemId);
        $this->setTimestamp($timestamp ?? time());
    }

    /**
     * Construct Interaction between user and item identified by ID.
     */
    public static function withItem(
        string $interactionType,
        string $userId,
        string $itemId,
        int $timestamp = null
    ): self {
        $interaction = new static(
            $interactionType,
            $userId,
            self::DEFAULT_ITEM_ID_ALIAS,
            $itemId,
            $timestamp
        );

        return $interaction;
    }

    /**
     * Construct Interaction between user and item identified by aliased ID.
     */
    public static function withAliasedItem(
        string $interactionType,
        string $userId,
        string $itemIdAlias,
        string $itemId,
        int $timestamp = null
    ): self {
        return new static(
            $interactionType,
            $userId,
            $itemIdAlias,
            $itemId,
            $timestamp
        );
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getCommandType(): string
    {
        return 'interaction';
    }

    /**
     * Set all Interaction attributes. All previously set attributes are removed.
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = new ArrayObject();
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Set Interaction attribute and its value. If attribute with the same name
     * already exists, it's replaced.
     *
     * @param mixed $value
     */
    public function setAttribute(string $name, $value): self
    {
        Assertion::typeIdentifier($name);

        $this->attributes[$name] = $value;

        return $this;
    }

    public function getCommandParameters(): array
    {
        return [
            'interaction_type' => $this->interactionType,
            'user_id' => $this->userId,
            'timestamp' => $this->timestamp,
            'attributes' => $this->attributes,
            $this->itemIdAlias => $this->itemId,
        ];
    }

    protected function setInteractionType(string $interactionType): void
    {
        Assertion::typeIdentifier($interactionType);

        $this->interactionType = $interactionType;
    }

    protected function setUserId(string $userId): void
    {
        Assertion::typeIdentifier($userId);

        $this->userId = $userId;
    }

    protected function setItemId(string $itemId): void
    {
        Assertion::typeIdentifier($itemId);

        $this->itemId = $itemId;
    }

    protected function setItemIdAlias(string $itemIdAlias): void
    {
        Assertion::typeIdentifier($itemIdAlias);

        $this->itemIdAlias = $itemIdAlias;
    }

    protected function setTimestamp(int $timestamp): void
    {
        Assertion::greaterThan($timestamp, 0);

        $this->timestamp = $timestamp;
    }
}
