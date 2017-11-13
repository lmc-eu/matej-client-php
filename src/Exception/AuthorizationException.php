<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown when request authorization fails.
 */
class AuthorizationException extends RequestException
{
    public static function createFromRequestAndResponse(
        RequestInterface $request,
        ResponseInterface $response,
        \Throwable $previous = null
    ): self {
        $responseData = json_decode($response->getBody()->getContents());

        $message = sprintf(
            'Matej API authorization error for url "%s"%s',
            $request->getRequestTarget(),
            isset($responseData->message) ? ' (' . $responseData->message . ')' : ''
        );

        return new static($message, $request, $response, $previous);
    }
}
