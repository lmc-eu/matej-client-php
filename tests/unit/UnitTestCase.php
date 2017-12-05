<?php declare(strict_types=1);

namespace Lmc\Matej;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class UnitTestCase extends \PHPUnit\Framework\TestCase
{
    protected function createJsonResponseFromFile(string $fileName): ResponseInterface
    {
        $jsonData = file_get_contents($fileName);
        $response = new Response(StatusCodeInterface::STATUS_OK, ['Content-Type' => 'application/json'], $jsonData);

        return $response;
    }
}
