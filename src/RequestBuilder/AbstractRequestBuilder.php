<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;

/**
 * Request builders provides methods for simple and type-safe assembly of request to specific Matej endpoint.
 *
 * If `RequestManager` is injected to the builder via `setRequestManager()`, the request could be executed right from
 * the builder using `send()` method.
 */
abstract class AbstractRequestBuilder
{
    /** @var RequestManager */
    protected $requestManager;
    /** @var string */
    protected $requestId;

    /**
     * Use Commands and other settings which were passed to this builder object to build instance of
     * Lmc\Matej\Model\Request.
     */
    abstract public function build(): Request;

    /**
     * If instance of RequestManager is injected to this builder object, you can build and send the request directly
     * via send() method of the builder itself.
     *
     * @return $this
     */
    public function setRequestManager(RequestManager $requestManager): self
    {
        $this->requestManager = $requestManager;

        return $this;
    }

    /**
     * Set custom identifier of the request to make it traceable in case of debugging.
     * The same ID will then also be available in the response.
     *
     * @see Response::getResponseId()
     * @return $this
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * @return Response
     */
    public function send(): Response
    {
        $this->assertRequestManagerIsAvailable();

        return $this->requestManager->sendRequest($this->build());
    }

    private function assertRequestManagerIsAvailable(): void
    {
        if ($this->requestManager === null) {
            throw new LogicException(
                'Instance of RequestManager must be set to request builder via setRequestManager() before'
                . ' calling send()'
            );
        }
    }
}
