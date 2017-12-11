<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Lmc\Matej\Model\Response;
use Psr\Http\Message\ResponseInterface;

interface ResponseDecoderInterface
{
    public function decode(ResponseInterface $httpResponse, string $responseClass = Response::class): Response;
}
