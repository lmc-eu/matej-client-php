<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Model\Command\ItemSorting;

class MatejTest extends IntegrationTestCase
{
    /** @test */
    public function shouldReceiveRequestIdInResponse(): void
    {
        $requestId = uniqid('integration-test-php-client-request-id');

        $response = static::createMatejInstance()
            ->request()
            ->sorting(ItemSorting::create('integration-test-php-client-user-id-A', ['itemA', 'itemB']))
            ->setRequestId($requestId)
            ->send();

        $this->assertSame($requestId, $response->getResponseId());
    }
}
