<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Assertion;
use Lmc\Matej\Model\Command\AbstractCommand;
use Lmc\Matej\Model\Command\AbstractRecommendation;
use Lmc\Matej\Model\Command\ItemSorting;
use Lmc\Matej\Model\Request;

class CampaignRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/campaign';

    /** @var AbstractCommand[] */
    protected $commands = [];

    public function addRecommendation(AbstractRecommendation $recommendation): self
    {
        $this->commands[] = $recommendation;

        return $this;
    }

    /**
     * @param AbstractRecommendation[] $recommendations
     */
    public function addRecommendations(array $recommendations): self
    {
        foreach ($recommendations as $recommendation) {
            $this->addRecommendation($recommendation);
        }

        return $this;
    }

    /** @return $this */
    public function addSorting(ItemSorting $sorting): self
    {
        $this->commands[] = $sorting;

        return $this;
    }

    /**
     * @param ItemSorting[] $sortings
     * @return $this
     */
    public function addSortings(array $sortings): self
    {
        foreach ($sortings as $sorting) {
            $this->addSorting($sorting);
        }

        return $this;
    }

    public function build(): Request
    {
        if (empty($this->commands)) {
            throw new LogicException('At least one command must be added to the builder before sending the request');
        }
        Assertion::batchSize($this->commands);

        return new Request(
            static::ENDPOINT_PATH,
            RequestMethodInterface::METHOD_POST,
            $this->commands,
            $this->requestId
        );
    }
}
