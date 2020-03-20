<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Command;

use Lmc\Matej\Exception\DomainException;
use PHPUnit\Framework\TestCase;

class BoostTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeJsonSerializable(): void
    {
        $boost = Boost::create('valid_to >= NOW()', 2.1);
        $this->assertSame(
            ['query' => 'valid_to >= NOW()', 'multiplier' => 2.1],
            $boost->jsonSerialize()
        );
    }

    /**
     * @test
     */
    public function multiplierHasToBeGreaterThan0(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Provided "-1" is not greater than "0".');
        $boost = Boost::create('valid_to >= NOW()', -1);
    }
}
