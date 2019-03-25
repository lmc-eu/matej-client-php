<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserRecommendation;

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

    public function getItemProperties(): ItemPropertiesGetRequestBuilder
    {
        $requestBuilder = new ItemPropertiesGetRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    /**
     * Define new properties into the database. Those properties will be created and subsequently accepted by Matej.
     */
    public function setupItemProperties(): ItemPropertiesSetupRequestBuilder
    {
        $requestBuilder = new ItemPropertiesSetupRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    /**
     * Added item properties will be IRREVERSIBLY removed from all items in the database and the item property will
     * from now be rejected by Matej.
     */
    public function deleteItemProperties(): ItemPropertiesSetupRequestBuilder
    {
        $requestBuilder = new ItemPropertiesSetupRequestBuilder($shouldDelete = true);
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function events(): EventsRequestBuilder
    {
        $requestBuilder = new EventsRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function campaign(): CampaignRequestBuilder
    {
        $requestBuilder = new CampaignRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function sorting(Sorting $sorting): SortingRequestBuilder
    {
        $requestBuilder = new SortingRequestBuilder($sorting);
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function recommendation(UserRecommendation $recommendation): RecommendationRequestBuilder
    {
        $requestBuilder = new RecommendationRequestBuilder($recommendation);
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function forget(): ForgetRequestBuilder
    {
        $requestBuilder = new ForgetRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    public function resetDatabase(): ResetDatabaseRequestBuilder
    {
        $requestBuilder = new ResetDatabaseRequestBuilder();
        $this->setupBuilder($requestBuilder);

        return $requestBuilder;
    }

    private function setupBuilder(AbstractRequestBuilder $requestBuilder): void
    {
        $requestBuilder->setRequestManager($this->requestManager);
    }
}
