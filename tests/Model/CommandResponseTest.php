<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Lmc\Matej\Exception\InvalidDomainModelArgumentException;
use Lmc\Matej\TestCase;

class CommandResponseTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideObjectResponses
     */
    public function shouldBeInstantiableFromRawObject(
        \stdClass $objectResponse,
        string $expectedStatus,
        string $expectedMessage,
        array $expectedData
    ): void {
        $commandResponse = CommandResponse::createFromRawCommandResponseObject($objectResponse);

        $this->assertInstanceOf(CommandResponse::class, $commandResponse);
        $this->assertSame($expectedStatus, $commandResponse->getStatus());
        $this->assertSame($expectedMessage, $commandResponse->getMessage());
        $this->assertSame($expectedData, $commandResponse->getData());
    }

    /**
     * @return array[]
     */
    public function provideObjectResponses(): array
    {
        return [
            'OK response with only status' => [
                (object) ['status' => 'OK'],
                'OK',
                '',
                [],
            ],
            'OK response with status and empty message and data' => [
                (object) ['status' => 'OK', 'message' => '', 'data' => []],
                'OK',
                '',
                [],
            ],
            'OK response with all fields' => [
                (object) ['status' => 'OK', 'message' => 'Nice!', 'data' => [['foo' => 'bar'], ['baz' => 'bak']]],
                'OK',
                'Nice!',
                [['foo' => 'bar'], ['baz' => 'bak']],
            ],
            'Error response with status and message' => [
                (object) ['status' => 'ERROR', 'message' => 'DuplicateKeyError(Duplicate key error collection)'],
                'ERROR',
                'DuplicateKeyError(Duplicate key error collection)',
                [],
            ],
        ];
    }

    /** @test */
    public function shouldThrowExceptionIfStatusIsMissing(): void
    {
        $this->expectException(InvalidDomainModelArgumentException::class);
        $this->expectExceptionMessage('Status field is missing in command response object');
        CommandResponse::createFromRawCommandResponseObject((object) ['message' => 'Foo', 'data' => [['bar']]]);
    }
}
