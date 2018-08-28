<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\Exception\RequestException;
use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;
use Lmc\Matej\Model\Command\UserMerge;
use Lmc\Matej\Model\Command\UserRecommendation;
use Lmc\Matej\Model\CommandResponse;
use Lmc\Matej\Model\Response\RecommendationsResponse;

/**
 * @covers \Lmc\Matej\Model\Response\RecommendationsResponse
 * @covers \Lmc\Matej\RequestBuilder\RecommendationRequestBuilder
 */
class RecommendationRequestBuilderTest extends IntegrationTestCase
{
    /** @test */
    public function shouldExecuteRecommendationRequestOnly(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand('user-a'))
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'SKIPPED', 'SKIPPED', 'OK');
        $this->assertShorthandResponse($response, 'SKIPPED', 'SKIPPED', 'OK');
    }

    /** @test */
    public function shouldExecuteRecommendationRequestWithUserMergeAndInteraction(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->recommendation($this->createRecommendationCommand('user-b'))
            ->setUserMerge(UserMerge::mergeInto('user-b', 'user-a'))
            ->setInteraction(Interaction::bookmark('user-a', 'item-a'))
            ->send();

        $this->assertInstanceOf(RecommendationsResponse::class, $response);
        $this->assertResponseCommandStatuses($response, 'OK', 'OK', 'OK');
        $this->assertShorthandResponse($response, 'OK', 'OK', 'OK');
    }

    /** @test */
    public function shouldFailOnInvalidModelName(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('BAD REQUEST');

        $recommendation = $this->createRecommendationCommand('user-a')
            ->setModelName('invalid-model-name');

        $this->createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();
    }

    /** @test */
    public function shouldFailOnInvalidPropertyName(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('BAD REQUEST');

        $recommendation = $this->createRecommendationCommand('user-a')
            ->addResponseProperty('unknown-property');

        $this->createMatejInstance()
            ->request()
            ->recommendation($recommendation)
            ->send();
    }

    private function createRecommendationCommand(string $username): UserRecommendation
    {
        return UserRecommendation::create(
            $username,
            5,
            'integration-test-scenario',
            0.50,
            3600
        );
    }

    private function assertShorthandResponse(RecommendationsResponse $response, $interactionStatus, $userMergeStatus, $recommendationStatus): void
    {
        $this->assertInstanceOf(CommandResponse::class, $response->getInteraction());
        $this->assertInstanceOf(CommandResponse::class, $response->getUserMerge());
        $this->assertInstanceOf(CommandResponse::class, $response->getRecommendation());
        $this->assertSame($interactionStatus, $response->getInteraction()->getStatus());
        $this->assertSame($userMergeStatus, $response->getUserMerge()->getStatus());
        $this->assertSame($recommendationStatus, $response->getRecommendation()->getStatus());
    }
}
