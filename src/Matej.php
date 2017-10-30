<?php declare(strict_types=1);

namespace Lmc\Matej;

class Matej
{
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
