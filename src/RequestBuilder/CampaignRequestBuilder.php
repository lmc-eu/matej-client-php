<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Assertion;
use Lmc\Matej\Model\Command\AbstractCommand;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\Request;

class CampaignRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/campaign';

    /** @var AbstractCommand[] */
    protected $commands = [];

    /** @return static */
    public function addRecommendation(UserRecommendation $recommendation): self
    {
        $this->commands[] = $recommendation;

        return $this;
    }

    /**
     * @param UserRecommendation[] $recommendations
     * @return static
     */
    public function addRecommendations(array $recommendations): self
    {
        foreach ($recommendations as $recommendation) {
            $this->addRecommendation($recommendation);
        }

        return $this;
    }

    /** @return static */
    public function addSorting(Sorting $sorting): self
    {
        $this->commands[] = $sorting;

        return $this;
    }

    /**
     * @param Sorting[] $sortings
     * @return static
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

        return new Request(static::ENDPOINT_PATH, RequestMethodInterface::METHOD_POST, $this->commands, $this->requestId);
    }
}
