<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Response\PlainResponse;

/**
 * @covers \Lmc\Matej\Model\Response\PlainResponse
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesGetRequestBuilder
 */
class ItemPropertiesGetRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldGetListOfPropertiesFromMatej(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->getItemProperties()
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
        $this->assertInstanceOf(PlainResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('', $response->getMessage());
        $this->assertSame('OK', $response->getStatus());
        $this->assertInternalType('array', $response->getData());
    }
}
