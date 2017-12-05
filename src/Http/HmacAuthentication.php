<?php declare(strict_types=1);

namespace Lmc\Matej\Http;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

/**
 * Use api key to sign request with hmac_timestamp and hmac_sign query parameters.
 */
class HmacAuthentication implements Authentication
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $params = $this->addAuthenticationToParamsFromRequest($request);

        return $this->createRequestWithParams($request, $params);
    }

    private function addAuthenticationToParamsFromRequest(RequestInterface $request): array
    {
        $params = $this->parseQueryParamsFromRequest($request);

        $params['hmac_timestamp'] = time();

        $unsignedRequest = $this->createRequestWithParams($request, $params);

        $params['hmac_sign'] = hash_hmac('sha1', $unsignedRequest->getRequestTarget(), $this->apiKey);

        return $params;
    }

    private function parseQueryParamsFromRequest(RequestInterface $request): array
    {
        $uri = $request->getUri();
        $query = $uri->getQuery();

        $params = [];
        parse_str($query, $params);

        return $params;
    }

    private function createRequestWithParams(RequestInterface $request, array $params): RequestInterface
    {
        $uri = $request->getUri();

        $query = http_build_query($params, '', '&');
        $uri = $uri->withQuery($query);

        return $request->withUri($uri);
    }
}
