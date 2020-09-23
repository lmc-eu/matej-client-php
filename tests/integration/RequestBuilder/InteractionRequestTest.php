<?php declare(strict_types=1);

namespace Lmc\Matej\IntegrationTests\RequestBuilder;

use Lmc\Matej\IntegrationTests\IntegrationTestCase;
use Lmc\Matej\Model\Command\Interaction;

/**
 * @covers \Lmc\Matej\RequestBuilder\EventsRequestBuilder
 */
class InteractionRequestTest extends IntegrationTestCase
{
    /** @test */
    public function shouldFailWithInvalidInteractionType(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(Interaction::withItem('invalid-type', 'user-a', 'item-a'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'INVALID');
    }

    /** @test */
    public function shouldSendInteractionWithCustomAttribute(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(
                Interaction::withItem('purchase', 'user-a', 'item-a')
                    ->setAttribute('quantity', 2)
            )
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
    }

    /** @test */
    public function shouldSendInteractionWithCustomAttributes(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(
                Interaction::withAliasedItem('search', 'user-a', 'search_id', 'search-id')
                    ->setAttributes([
                        'query' => 'query value',
                        'keywords' => 'key, words',
                        'serp' => ['serp1', 'serp2'],
                        'categories' => ['category1', 'category2'],
                        'location' => [['lat' => 41.2395, 'long' => 3.40592]],
                        'is_logged_in' => true,
                    ])
            )
            ->send();
        $this->assertResponseCommandStatuses($response, 'OK');
    }

    /** @test */
    public function shouldSendInteractionWithItemAlias(): void
    {
        $response = static::createMatejInstance()
            ->request()
            ->events()
            ->addInteraction(Interaction::withAliasedItem('search', 'user-a', 'search_id', 'search-id'))
            ->send();

        $this->assertResponseCommandStatuses($response, 'OK');
    }
}
