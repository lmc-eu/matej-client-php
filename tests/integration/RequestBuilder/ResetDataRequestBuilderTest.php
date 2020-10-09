<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;

/**
 * @covers \Lmc\Matej\Model\Response\PlainResponse
 * @covers \Lmc\Matej\RequestBuilder\ResetDataRequestBuilder
 */
class ResetDataRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldResetMatejData(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->resetData()
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('', $response->getMessage());
        $this->assertSame('OK', $response->getStatus());
        $this->assertIsArray($response->getData());
    }
}
