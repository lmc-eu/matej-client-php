<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Lmc\Matej\Http\Plugin\ExceptionPlugin;
use Lmc\Matej\Matej;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use Psr\Http\Message\RequestInterface;

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
    /** @var HttpClient */
    protected $httpClient;
    /** @var MessageFactory */
    protected $messageFactory;
    /** @var ResponseDecoderInterface */
    protected $responseDecoder;

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
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /** @codeCoverageIgnore */
    public function setMessageFactory(MessageFactory $messageFactory): void
    {
        $this->messageFactory = $messageFactory;
    }

    /** @codeCoverageIgnore */
    public function setResponseDecoder(ResponseDecoderInterface $responseDecoder): void
    {
        $this->responseDecoder = $responseDecoder;
    }

    /** @codeCoverageIgnore */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    protected function getHttpClient(): HttpClient
    {
        if ($this->httpClient === null) {
            // @codeCoverageIgnoreStart
            $this->httpClient = HttpClientDiscovery::find();
            // @codeCoverageIgnoreEnd
        }

        return $this->httpClient;
    }

    protected function getMessageFactory(): MessageFactory
    {
        if ($this->messageFactory === null) {
            $this->messageFactory = MessageFactoryDiscovery::find();
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

    protected function createConfiguredHttpClient(): HttpClient
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
        $requestBody = json_encode($request->getData());
        $uri = $this->buildBaseUrl() . $request->getPath();

        return $this->getMessageFactory()
            ->createRequest(
                $request->getMethod(),
                $uri,
                [
                    'Content-Type' => 'application/json',
                    self::REQUEST_ID_HEADER => $request->getRequestId(),
                ],
                $requestBody
            );
    }

    protected function buildBaseUrl(): string
    {
        return sprintf($this->baseUrl, $this->accountId);
    }

    private function getDefaultHeaders(): array
    {
        return [
            self::CLIENT_VERSION_HEADER => Matej::CLIENT_ID . '/' . Matej::VERSION,
        ];
    }
}
