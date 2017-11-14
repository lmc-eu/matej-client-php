<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

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

    public function __construct(string $path, string $method, array $data)
    {
        $this->path = $path;
        $this->method = $method;
        $this->data = $data;
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
}
