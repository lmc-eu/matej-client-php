<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response\RecommendationsResponse;

/**
 * @method RecommendationsResponse send()
 */
class RecommendationRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/recommendations';

    /** @var Interaction|null */
    private $interactionCommand;
    /** @var UserMerge|null */
    private $userMergeCommand;
    /** @var UserRecommendation */
    private $userRecommendationCommand;

    public function __construct(UserRecommendation $userRecommendationCommand)
    {
        $this->userRecommendationCommand = $userRecommendationCommand;
    }

    public function setUserMerge(UserMerge $merge): self
    {
        $this->userMergeCommand = $merge;

        return $this;
    }

    public function setInteraction(Interaction $interaction): self
    {
        $this->interactionCommand = $interaction;

        return $this;
    }

    public function build(): Request
    {
        $this->assertConsistentUsersInCommands();

        return new Request(
            self::ENDPOINT_PATH,
            RequestMethodInterface::METHOD_POST,
            [$this->interactionCommand, $this->userMergeCommand, $this->userRecommendationCommand],
            $this->requestId,
            RecommendationsResponse::class
        );
    }

    private function assertConsistentUsersInCommands(): void
    {
        $mainCommandUser = $this->userRecommendationCommand->getUserId();
        if ($this->interactionCommand !== null && $mainCommandUser !== $this->interactionCommand->getUserId()) {
            throw LogicException::forInconsistentUserId($this->userRecommendationCommand, $this->interactionCommand);
        }

        if ($this->userMergeCommand !== null && $mainCommandUser !== $this->userMergeCommand->getUserId()) {
            throw LogicException::forInconsistentUserId($this->userRecommendationCommand, $this->userMergeCommand);
        }
    }
}
