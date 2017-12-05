<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Model\Request;

class ItemPropertiesGetRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/item-properties';

    public function build(): Request
    {
        return new Request(self::ENDPOINT_PATH, RequestMethodInterface::METHOD_GET, [], $this->requestId);
    }
}
