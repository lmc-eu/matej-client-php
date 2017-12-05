<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Model\Command\Sorting;

class MatejTest extends IntegrationTestCase
{
    /** @test */
    public function shouldReceiveRequestIdInResponse(): void
    {
        $requestId = uniqid('integration-test-php-client-request-id');

        $response = $this->createMatejInstance()
            ->request()
            ->sorting(Sorting::create('integration-test-php-client-user-id-A', ['itemA', 'itemB']))
            ->setRequestId($requestId)
            ->send();

        $this->assertSame($requestId, $response->getResponseId());
    }
}
