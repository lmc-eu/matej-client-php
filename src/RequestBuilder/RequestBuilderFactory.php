<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Lmc\Matej\Http\RequestManager;

/**
 * Factory to create concrete RequestBuilder which helps you to create request for each Matej API
 */
class RequestBuilderFactory
{
    /** @var RequestManager */
    private $requestManager;

    public function __construct(RequestManager $requestManager)
    {
        $this->requestManager = $requestManager;
    }

    /**
     * Define new properties into the database. Those properties will be created and subsequently accepted by Matej.
     */
    public function setupItemProperties(): ItemPropertiesSetupRequestBuilder
    {
        return $this->createConfiguredBuilder(ItemPropertiesSetupRequestBuilder::class);
    }

    /**
     * Added item properties will be IRREVERSIBLY removed from all items in the database and the item property will
     * from now be rejected by Matej.
     */
    public function deleteItemProperties(): ItemPropertiesSetupRequestBuilder
    {
        return $this->createConfiguredBuilder(ItemPropertiesSetupRequestBuilder::class, $shouldDelete = true);
    }

    public function events(): EventsRequestBuilder
    {
        return $this->createConfiguredBuilder(EventsRequestBuilder::class);
    }

    // TODO: builders for other endpoints

    /**
     * @param string $builderClass
     * @param array ...$args
     * @return mixed
     */
    private function createConfiguredBuilder(string $builderClass, ...$args)
    {
        /** @var AbstractRequestBuilder $requestBuilder */
        $requestBuilder = new $builderClass(...$args);

        $requestBuilder->setRequestManager($this->requestManager);

        return $requestBuilder;
    }
}
