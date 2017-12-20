<?php declare(strict_types=1);

namespace Lmc\Matej;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Http\ResponseDecoderInterface;
use Lmc\Matej\RequestBuilder\RequestBuilderFactory;

class Matej
{
    public const CLIENT_ID = 'php-client';
    public const VERSION = '1.0.0';

    /** @var string */
    private $accountId;
    /** @var string */
    private $apiKey;
    /** @var RequestManager */
    private $requestManager;

    public function __construct(string $accountId, string $apiKey)
    {
        $this->accountId = $accountId;
        $this->apiKey = $apiKey;
        $this->requestManager = new RequestManager($accountId, $apiKey);
    }

    public function request(): RequestBuilderFactory
    {
        return new RequestBuilderFactory($this->getRequestManager());
    }

    /** @return $this */
    public function setHttpClient(HttpClient $client): self
    {
        $this->getRequestManager()->setHttpClient($client);

        return $this;
    }

    /**
     * @internal used mainly in integration tests
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->getRequestManager()->setBaseUrl($baseUrl);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function setHttpMessageFactory(MessageFactory $messageFactory): self
    {
        $this->getRequestManager()->setMessageFactory($messageFactory);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function setHttpResponseDecoder(ResponseDecoderInterface $responseDecoder): self
    {
        $this->getRequestManager()->setResponseDecoder($responseDecoder);

        return $this;
    }

    protected function getRequestManager(): RequestManager
    {
        return $this->requestManager;
    }
}
