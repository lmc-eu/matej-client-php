<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Fixtures\DummyResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\Model\Assertion
 */
class AssertionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideValidTypeIdentifiers
     */
    public function shouldAssertValidTypeIdentifier(string $typeIdentifier): void
    {
        $this->assertTrue(Assertion::typeIdentifier($typeIdentifier));
    }

    /**
     * @return array[]
     */
    public function provideValidTypeIdentifiers(): array
    {
        return [
            'single character' => ['a'],
            'lower/upper case combination' => ['FOObar'],
            'numbers, dashes' => ['foo-123'],
            'cases, numbers, uderscore, dash' => ['fOoO_13-37'],
            'starts with number' => ['123-foo'],
            'number as string' => ['666333666333'],
            'max length (100 characters)' => [str_repeat('a', 100)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidTypeIdentifiers
     * @param mixed $typeIdentifier
     * @param string $expectedExceptionMessage
     */
    public function shouldAssertInvalidTypeIdentifier($typeIdentifier, string $expectedExceptionMessage): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        Assertion::typeIdentifier($typeIdentifier);
    }

    /**
     * @return array[]
     */
    public function provideInvalidTypeIdentifiers(): array
    {
        $formatExceptionMessage = 'does not match type identifier format requirement';
        $lengthExceptionMessage = 'is too long, it should have no more than 100 characters';

        return [
            'empty' => ['', $formatExceptionMessage],
            'special national characters' => ['föbär', $formatExceptionMessage],
            'at character' => ['user@email', $formatExceptionMessage],
            'integer' => [333666, $formatExceptionMessage],
            'over max length (>100 characters)' => [str_repeat('a', 101), $lengthExceptionMessage],
        ];
    }

    /**
     * @test
     * @dataProvider provideValidBatchSize
     */
    public function shouldAssertValidBatchSize(int $batchSize): void
    {
        $commands = [];

        for ($i = 0; $i < $batchSize; $i++) {
            $commands[] = ItemPropertySetup::string('name');
        }

        $this->assertTrue(Assertion::batchSize($commands));
    }

    /**
     * @return array[]
     */
    public function provideValidBatchSize(): array
    {
        return [
            [0],
            [1],
            [1000],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidBatchSize
     */
    public function shouldAssertInvalidBatchSize(int $batchSize): void
    {
        $commands = [];

        for ($i = 0; $i < $batchSize; $i++) {
            $commands[] = ItemPropertySetup::string('name');
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Request contains ' . $batchSize . ' commands, but at most 1000 is allowed in one request.'
        );

        Assertion::batchSize($commands);
    }

    /**
     * @return array[]
     */
    public function provideInvalidBatchSize(): array
    {
        return [
            [1001],
            [2000],
        ];
    }

    /** @test */
    public function shouldAssertValidResponseClass(): void
    {
        $inheritedObject = new DummyResponse(0, 0, 0, 0);

        $this->assertTrue(Assertion::isResponseClass(Response::class));
        $this->assertTrue(Assertion::isResponseClass(get_class($inheritedObject)));
    }

    /** @test */
    public function shouldAssertInvalidResponseClass(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s has to be instance or subclass of %s.',
            \stdClass::class,
            Response::class
        ));

        Assertion::isResponseClass(\stdClass::class);
    }
}
