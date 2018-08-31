# Upgrading from 1.x to 2.0

API client release 2.0 contains few backward incompatible changes.

This guide will help you upgrade your codebase.

## `UserRecommendation` now returns new format of response in `->getData()`

#### Before

```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);

$response = $matej->request()
    ->recommendation($recommendation)
    ->send();

$recommendations = $response->getRecommendation()->getData();

// var_dump($recommendations):
// array(10) {
//    [0] =>
//        string(9) "item_id_1"
//    [1] =>
//        string(9) "item_id_2"
// }

foreach ($recommendations as $recommendedId) {
    echo $recommendedId; // outputs id of recommended item
}
```

#### After

```php
// Additional properties could now be passed to the UserRecommendation command using constructor or setResponseProperties() method
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600, ['item_title']);

$response = $matej->request()
    ->recommendation($recommendation)
    ->send();

$recommendations = $response->getRecommendation()->getData();

// $recommendations now contains array of stdClasses with item_id and other requested properties (item_title in this example)

// var_dump($recommendations):
// array(2) {
//     [0] => object(stdClass)#1 (2) {
//         ["item_id"]  => string(9) "item_id_1"
//         ["item_title"] => string(5) "title_1"
//     }
//     [1] => object(stdClass)#2 (2) {
//         ["item_id"]  => string(9)  "item_id_2"
//         ["item_title"] => string(10) "title_2"
//     }
// }

foreach ($recommendations as $recommendation) {
    echo $recommendation->item_id; // outputs id of recommended item
    echo $recommendation->item_title; // outputs custom item_title property of recommended item
}
```

## `UserRecommendation` does not have default filter

If you relied on default filter of UserRecommendation command (`['valid_to >= NOW']`) you must now define it explicitly.

#### Before

```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);
```

#### After

```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);
$recommendation->addFilter('valid_to >= NOW()');
```

## `UserRecommendation` now uses MQL query language by default for filtering

Filters specified using `addFilter()` or `setFilters()` method of `UserRecommendation` command now uses different format.

#### Before

```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);
$recommendation->addFilter('foo = bar')
    ->addFilter('item_id not in {"a", "b"}');
```

#### After

```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);
$recommendation->addFilter("foo = 'bar'")
    ->addFilter("item_id NOT IN ('a', 'b')");

```

#### Filter conversion table

Below you can find examples of previous filters, adapted for MQL:

| Original filter                                  | New filter                                         |
|--------------------------------------------------|----------------------------------------------------|
| `for_recommendation`                             | `for_recommendation = 1`                           |
| `for_recommendation and itemId in {"a", "b"}`    | `for_recommendation = 1 AND item_id IN ('a', 'b')` |
| `valid_from <= NOW <= valid_to`                  | `valid_from <= NOW() AND valid_to >= NOW()`        |
| `NOW <= valid_to`                                | `valid_to >= NOW()`                                |
| `item_id in {"a", "b"}`                          | `item_id IN ('a', 'b')`                            |
| `item_id not in {"a", "b"}`                      | `item_id NOT IN ('a', 'b')`                        |
| `valid_to >= 123456789`                          | `valid_to >= 123456789`                            |
| `valid_to >= NOW AND item_id NOT IN ["a", "b"]`  | `valid_to >= NOW() AND item_id NOT IN ('a', 'b')`  |
| `valid_to >= NOW AND for_recommendation == True` | `valid_to >= NOW() AND for_recommendation = 1`     |

## Minimal relevance of `UserRecommendation` command must enum of type `MinimalRelevance`

Minimal relevance passed to `setMinimalRelevance()` method is now defined using enum instead of constants with strings.

#### Before

```php
$recommendation = UserRecommendation::create('user-id', 1, 'scenario', 1.0, 3600);

$recommendation->setMinimalRelevance(UserRecommendation::MINIMAL_RELEVANCE_HIGH);
```

#### After

```php
use Lmc\Matej\Model\Command\Constants\MinimalRelevance;

...

$recommendation = UserRecommendation::create('user-id', 1, 'scenario', 1.0, 3600);

$recommendation->setMinimalRelevance(MinimalRelevance::HIGH());
```
