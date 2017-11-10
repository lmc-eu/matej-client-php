<?php declare(strict_types=1);

namespace Lmc\Matej\Http\Plugin;

use Fig\Http\Message\StatusCodeInterface;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Lmc\Matej\Exception\AuthorizationException;
use Lmc\Matej\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExceptionPlugin implements Plugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        /** @var Promise $promise */
        $promise = $next($request);

        return $promise->then(function (ResponseInterface $response) use ($request) {
            return $this->transformResponseToException($request, $response);
        });
    }

    private function transformResponseToException(RequestInterface $request, ResponseInterface $response)
    {
        $responseCode = $response->getStatusCode();

        if ($responseCode === StatusCodeInterface::STATUS_UNAUTHORIZED) {
            throw AuthorizationException::createFromRequestAndResponse($request, $response);
        }

        if ($responseCode >= 400 && $responseCode < 600) {
            // TODO: use more specific exceptions
            throw new RequestException($response->getReasonPhrase(), $request, $response);
        }

        return $response;
    }
}
