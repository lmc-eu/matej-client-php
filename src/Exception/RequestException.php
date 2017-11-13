<?php declare(strict_types=1);

namespace Lmc\Matej\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Generic exception thrown when error occurs during request execution.
 * Some more specific child exception could be thrown instead.
 */
class RequestException extends AbstractMatejException
{
    /** @var RequestInterface */
    private $request;
    /** @var ResponseInterface */
    private $response;

    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response,
        \Throwable $previous = null
    ) {
        $code = $response->getStatusCode();
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
