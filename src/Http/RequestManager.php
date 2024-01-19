<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Lmc\Matej\Http\Plugin\ExceptionPlugin;
use Lmc\Matej\Matej;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Encapsulates HTTP layer, ie. request/response handling.
 * This class should not be typically used directly - its supposed to be called internally from `Matej` class.
 */
class RequestManager
{
    public const CLIENT_VERSION_HEADER = 'Matej-Client-Version';
    public const REQUEST_ID_HEADER = 'Matej-Request-Id';
    public const RESPONSE_ID_HEADER = 'Matej-Response-Id';

    /** @var string */
    private $baseUrl = 'https://%s.matej.lmc.cz';
    /** @var string */
    protected $accountId;
    /** @var string */
    protected $apiKey;
    /** @var ClientInterface */
    protected $httpClient;
    /** @var RequestFactoryInterface */
    protected $messageFactory;
    /** @var ResponseDecoderInterface */
    protected $responseDecoder;
    /** @var StreamFactoryInterface */
    protected $streamFactory;

    public function __construct(string $accountId, string $apiKey)
    {
        $this->accountId = $accountId;
        $this->apiKey = $apiKey;
    }

    public function sendRequest(Request $request): Response
    {
        $httpRequest = $this->createHttpRequestFromMatejRequest($request);

        $client = $this->createConfiguredHttpClient();

        $httpResponse = $client->sendRequest($httpRequest);

        return $this->getResponseDecoder()->decode($httpResponse, $request->getResponseClass());
    }

    /** @codeCoverageIgnore */
    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /** @codeCoverageIgnore */
    public function setMessageFactory(RequestFactoryInterface $messageFactory): void
    {
        $this->messageFactory = $messageFactory;
    }

    /** @codeCoverageIgnore */
    public function setResponseDecoder(ResponseDecoderInterface $responseDecoder): void
    {
        $this->responseDecoder = $responseDecoder;
    }

    /** @codeCoverageIgnore */
    public function setStreamFactory(StreamFactoryInterface $streamFactory): void
    {
        $this->streamFactory = $streamFactory;
    }

    /** @codeCoverageIgnore */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    protected function getHttpClient(): ClientInterface
    {
        if ($this->httpClient === null) {
            // @codeCoverageIgnoreStart
            $this->httpClient = Psr18ClientDiscovery::find();
            // @codeCoverageIgnoreEnd
        }

        return $this->httpClient;
    }

    protected function getMessageFactory(): RequestFactoryInterface
    {
        if ($this->messageFactory === null) {
            $this->messageFactory = Psr17FactoryDiscovery::findRequestFactory();
        }

        return $this->messageFactory;
    }

    protected function getResponseDecoder(): ResponseDecoderInterface
    {
        if ($this->responseDecoder === null) {
            $this->responseDecoder = new ResponseDecoder();
        }

        return $this->responseDecoder;
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        if ($this->streamFactory === null) {
            $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        }

        return $this->streamFactory;
    }

    protected function createConfiguredHttpClient(): ClientInterface
    {
        return new PluginClient(
            $this->getHttpClient(),
            [
                new HeaderSetPlugin($this->getDefaultHeaders()),
                new AuthenticationPlugin(new HmacAuthentication($this->apiKey)),
                new ExceptionPlugin(),
            ]
        );
    }

    protected function createHttpRequestFromMatejRequest(Request $request): RequestInterface
    {
        $requestBody = $this->getStreamFactory()->createStream(
            json_encode($request->getData(), JSON_THROW_ON_ERROR)
        ); // TODO: use \Safe\json_encode
        $uri = $this->buildBaseUrl() . $request->getPath();

        return $this->getMessageFactory()
            ->createRequest(
                $request->getMethod(),
                $uri
            )
            ->withHeader('Content-Type', 'application/json')
            ->withHeader(static::REQUEST_ID_HEADER, $request->getRequestId())
            ->withBody($requestBody);
    }

    protected function buildBaseUrl(): string
    {
        return sprintf($this->baseUrl, $this->accountId);
    }

    private function getDefaultHeaders(): array
    {
        return [
            static::CLIENT_VERSION_HEADER => Matej::CLIENT_ID . '/' . Matej::VERSION,
        ];
    }
}
