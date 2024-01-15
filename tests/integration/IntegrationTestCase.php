<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Matej;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    protected static function markAsSkippedIfMatejIsNotAvailable(): void
    {
        if (!getenv('MATEJ_TEST_ACCOUNTID')) {
            self::markTestSkipped('Environment variable MATEJ_TEST_ACCOUNTID has to be defined');
        }

        if (!getenv('MATEJ_TEST_APIKEY')) {
            self::markTestSkipped('Environment variable MATEJ_TEST_APIKEY has to be defined');
        }
    }

    protected static function createMatejInstance(): Matej
    {
        self::markAsSkippedIfMatejIsNotAvailable();

        $instance = new Matej(getenv('MATEJ_TEST_ACCOUNTID'), getenv('MATEJ_TEST_APIKEY'));

        if ($baseUrl = getenv('MATEJ_TEST_BASE_URL')) { // intentional assignment
            $instance->setBaseUrl($baseUrl);
        }

        return $instance;
    }

    protected function assertResponseCommandStatuses(Response $response, string ...$expectedCommandStatuses): void
    {
        $this->assertSame(count($expectedCommandStatuses), $response->getNumberOfCommands());
        $this->assertSame(
            count(array_intersect($expectedCommandStatuses, ['OK'])),
            $response->getNumberOfSuccessfulCommands()
        );
        $this->assertSame(
            count(array_intersect($expectedCommandStatuses, ['INVALID'])),
            $response->getNumberOfFailedCommands()
        );
        $this->assertSame(
            count(array_intersect($expectedCommandStatuses, ['SKIPPED'])),
            $response->getNumberOfSkippedCommands()
        );

        $commandResponses = $response->getCommandResponses();
        foreach ($expectedCommandStatuses as $key => $expectedStatus) {
            $this->assertSame($expectedStatus, $commandResponses[$key]->getStatus());
        }
    }

    /** @return string[] */
    protected static function generateOkStatuses(int $amount): array
    {
        $data = explode(',', str_repeat('OK,', $amount));
        array_pop($data);

        return $data;
    }
}
