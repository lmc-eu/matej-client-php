<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\ItemPropertiesGetRequestBuilder
 */
class ItemPropertiesGetRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldGetListOfPropertiesFromMatej(): void
    {
        $response = $this->createMatejInstance()
            ->request()
            ->getItemProperties()
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
    }
}
