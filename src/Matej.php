<?php declare(strict_types=1);

namespace Lmc\Matej;

use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Http\ResponseDecoderInterface;
use Lmc\Matej\RequestBuilder\RequestBuilderFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Matej
{
    public const CLIENT_ID = 'php-client';
    public const VERSION = '4.1.0';

    /** @var RequestManager */
    private $requestManager;

    public function __construct(string $accountId, string $apiKey)
    {
        $this->requestManager = new RequestManager($accountId, $apiKey);
    }

    public function request(): RequestBuilderFactory
    {
        return new RequestBuilderFactory($this->getRequestManager());
    }

    /** @return $this */
    public function setHttpClient(ClientInterface $client): self
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
    public function setHttpMessageFactory(RequestFactoryInterface $messageFactory): self
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

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function setStreamFactory(StreamFactoryInterface $streamFactory): self
    {
        $this->getRequestManager()->setStreamFactory($streamFactory);

        return $this;
    }

    protected function getRequestManager(): RequestManager
    {
        return $this->requestManager;
    }
}
