<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Assert\AssertionFailedException;

/**
 * Exception thrown when invalid value is passed while creating domain model.
 *
 * @codeCoverageIgnore
 */
class DomainException extends LogicException implements AssertionFailedException
{
    /** @var string|null */
    private $propertyPath;
    /** @var mixed */
    private $value;
    /** @var array */
    private $constraints;

    /**
     * @param mixed $value
     */
    public function __construct(string $message, int $code, ?string $propertyPath, $value, array $constraints = [])
    {
        parent::__construct($message, $code);

        $this->propertyPath = $propertyPath;
        $this->value = $value;
        $this->constraints = $constraints;
    }

    public function getPropertyPath(): ?string
    {
        return $this->propertyPath;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
