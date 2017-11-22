<?php declare(strict_types=1);

namespace Lmc\Matej;

class Matej
{
    public const CLIENT_ID = 'php-client';
    public const VERSION = '0.0.0';

    /** @var string */
    private $clientId;
    /** @var string */
    private $apiKey;

    public function __construct(string $clientId, string $apiKey)
    {
        $this->clientId = $clientId;
        $this->apiKey = $apiKey;
    }
}
