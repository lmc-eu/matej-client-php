<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

use Ramsey\Uuid\Uuid;

/**
 * Represents request to Matej prepared to be executed by `RequestManager`.
 */
class Request
{
    /** @var string */
    private $path;
    /** @var string */
    private $method;
    /** @var array */
    private $data;
    /** @var string */
    private $requestId;

    public function __construct(string $path, string $method, array $data = [], string $requestId = null)
    {
        $this->path = $path;
        $this->method = $method;
        $this->data = $data;
        $this->requestId = $requestId ?? Uuid::uuid4()->toString();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
