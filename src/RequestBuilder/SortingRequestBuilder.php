<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Request;

class SortingRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/sorting';

    /** @var Interaction|null */
    private $interactionCommand;
    /** @var UserMerge|null */
    private $userMergeCommand;
    /** @var Sorting */
    private $sortingCommand;

    public function __construct(Sorting $sortingCommand)
    {
        $this->sortingCommand = $sortingCommand;
    }

    public function addUserMerge(UserMerge $merge): self
    {
        $this->userMergeCommand = $merge;

        return $this;
    }

    public function addInteraction(Interaction $interaction): self
    {
        $this->interactionCommand = $interaction;

        return $this;
    }

    public function build(): Request
    {
        return new Request(
            self::ENDPOINT_PATH,
            RequestMethodInterface::METHOD_POST,
            [$this->interactionCommand, $this->userMergeCommand, $this->sortingCommand]
        );
    }
}
