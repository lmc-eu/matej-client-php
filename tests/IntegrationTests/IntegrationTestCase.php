<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests;

use Lmc\Matej\Matej;
use Lmc\Matej\TestCase;

class IntegrationTestCase extends TestCase
{
    /** @before */
    protected function checkIfConfigured(): void
    {
        if (!getenv('MATEJ_TEST_ACCOUNTID')) {
            $this->markTestSkipped('Environment variable MATEJ_TEST_ACCOUNTID has to be defined');
        }

        if (!getenv('MATEJ_TEST_APIKEY')) {
            $this->markTestSkipped('Environment variable MATEJ_TEST_APIKEY has to be defined');
        }
    }

    protected function createMatejInstance(): Matej
    {
        $instance = new Matej(getenv('MATEJ_TEST_ACCOUNTID'), getenv('MATEJ_TEST_APIKEY'));

        if ($baseUrl = getenv('MATEJ_TEST_BASE_URL')) { // intentional assignment
            $instance->setBaseUrl($baseUrl);
        }

        return $instance;
    }
}
