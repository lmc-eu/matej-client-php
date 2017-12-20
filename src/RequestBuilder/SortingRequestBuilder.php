<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\Sorting;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response\SortingResponse;

/**
 * @method SortingResponse send()
 */
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

    /** @return $this */
    public function setUserMerge(UserMerge $merge): self
    {
        $this->userMergeCommand = $merge;

        return $this;
    }

    /** @return $this */
    public function setInteraction(Interaction $interaction): self
    {
        $this->interactionCommand = $interaction;

        return $this;
    }

    public function build(): Request
    {
        $this->assertInteractionUserId();
        $this->assertUserMergeUserId();

        // Build request
        return new Request(
            static::ENDPOINT_PATH,
            RequestMethodInterface::METHOD_POST,
            [$this->interactionCommand, $this->userMergeCommand, $this->sortingCommand],
            $this->requestId,
            SortingResponse::class
        );
    }

    /**
     * Assert that interaction user ids are ok:
     * - (A,  null,  A)
     * - (A, A -> ?, ?)
     */
    private function assertInteractionUserId(): void
    {
        if ($this->interactionCommand === null) {
            return;
        }

        $interactionUserId = $this->interactionCommand->getUserId();

        // (A, null, A)
        if ($this->userMergeCommand === null && $interactionUserId !== $this->sortingCommand->getUserId()) {
            throw LogicException::forInconsistentUserId($this->sortingCommand, $this->interactionCommand);
        }

        // (A, A -> ?, ?)
        if ($this->userMergeCommand !== null && $interactionUserId !== $this->userMergeCommand->getSourceUserId()) {
            throw LogicException::forInconsistentUserMergeAndInteractionCommand(
                $this->userMergeCommand->getSourceUserId(),
                $interactionUserId
            );
        }
    }

    /**
     * Assert user merge id is ok:
     * (?, ? -> A, A)
     */
    private function assertUserMergeUserId(): void
    {
        if ($this->userMergeCommand !== null
            && $this->userMergeCommand->getUserId() !== $this->sortingCommand->getUserId()) {
            throw LogicException::forInconsistentUserId($this->sortingCommand, $this->userMergeCommand);
        }
    }
}
