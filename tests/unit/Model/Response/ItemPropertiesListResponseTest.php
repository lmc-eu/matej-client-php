<?php declare(strict_types=1);

namespace Lmc\Matej\Model\Response;

use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\UnitTestCase;

class ItemPropertiesListResponseTest extends UnitTestCase
{
    /** @test */
    public function shouldBeInstantiable(): void
    {
        $commandResponse = (object) [
            'status' => CommandResponse::STATUS_OK,
            'message' => 'MOCK_MESSAGE',
            'data' => ['MOCK' => 'DATA'],
        ];

        $response = new ItemPropertiesListResponse(1, 1, 0, 0, [$commandResponse]);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(CommandResponse::STATUS_OK, $response->getStatus());
        $this->assertSame('MOCK_MESSAGE', $response->getMessage());
        $this->assertSame(['MOCK' => 'DATA'], $response->getData());
    }
}
