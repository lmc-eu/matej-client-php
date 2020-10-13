<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response\PlainResponse;

/**
 * @method PlainResponse send()
 */
class ResetDataRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/data';

    public function build(): Request
    {
        return new Request(
            self::ENDPOINT_PATH,
            RequestMethodInterface::METHOD_DELETE,
            [],
            $this->requestId,
            PlainResponse::class
        );
    }
}
