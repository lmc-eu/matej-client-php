
# Upgrading from 3.x to 4.0

API client release 4.0 contains backward incompatible changes.

This guide will help you upgrade your codebase.

## Sorting renamed to ItemSorting
Class `Sorting` was renamed to `ItemSorting`. All its methods and behaviour remained the same.

#### Before
```php
$response =  $matej->request()
    ->sorting(Sorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->send();
```

#### After
```php
$response =  $matej->request()
    ->sorting(ItemSorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->send();
```


## New types of recommendations
API client now supports new types of recommendation commands:
- Recommend items to user (`UserItemRecommendation`, originally `UserRecommendation`)
- Recommend users to user (`UserUserRecommendation`)
- Recommend items to item (`ItemItemRecommendation`)
- Recommend users to item (`ItemUserRecommendation`)

Each recommendation type supports different parameter options. Awailable parameters are
described in table bellow:

| Atribute      | Methods                                   | UserItemRecommendation | UserUserRecommendation | ItemItemRecommendation | ItemUserRecommendation |
|---------------|-------------------------------------------|------------------------|------------------------|------------------------|------------------------|
| scenario      |              `static::create`               |            ✅           |            ✅           |            ✅           |            ✅           |
| count         |                  `setCount`                 |            ✅           |            ✅           |            ✅           |            ✅           |
| rotation_rate |              `setRotationRate`              |            ✅           |            ✅           |            ❌           |            ❌           |
| rotation_time |              `setRotationTime`              |            ✅           |            ✅           |            ❌           |            ❌           |
| hard_rotation |             `enableHardRotation`            |            ✅           |            ✅           |            ❌           |            ❌           |
| allow_seen    |                `setAllowSeen`               |            ✅           |            ❌           |            ❌           |            ✅           |
| min_relevance |            `setMinimalRelevance`            |  `ItemMinimalRelevance`  |            ❌           |            ❌           |  `UserMinimalRelevance`*  |
| filter        |            `addFilter` `setFilters`           |            ✅           |            ❌           |            ✅           |            ❌           |
| boost_rules   |             `addBoost` `setBoosts`            |            ✅           |            ❌           |            ✅           |            ❌           |
| model_name    |                `setModelName`               |            ✅           |            ✅           |            ✅           |            ✅           |
| properties    | `addResponseProperty` `setResponseProperties` |            ✅           |            ❌           |            ✅           |            ❌           |

\* `UserMinimalRelevance` supports only `MEDIUM` and `HIGH` relevancies.

Each recommendation class provides a static constructor method `create` that accepts `userId` (recommendations for users) or `itemId` (recommendations for items).

## Recommending items to users
Class `UserRecommendation` was renamed to `UserItemRecommendation`. Class `MinimalRelevance` was
renamed to `ItemMinimalRelevance`. All other methods and attributes remained unchanged.

#### Before
```php
$recommendation = UserRecommendation::create('user-id', 'scenario')
    ->setMinimalRelevance(MinimalRelevance::LOW())
    ->setCount(5)
    ->setRotationRate(1.0)
    ->setRotationTime(3600);
```

#### After
```php
$recommendation = UserItemRecommendation::create('user-id', 'scenario')
    ->setMinimalRelevance(ItemMinimalRelevance::LOW())
    ->setCount(5)
    ->setRotationRate(1.0)
    ->setRotationTime(3600);
```

## New recommendation types
New types of recommendations can be created is similar way to `UserItemRecommendation`. For example:

### Users that might be interested in an item
```php
$recommendation = ItemUserRecommendation::create('item-id', 'scenario')
    ->setCount(5)
    ->setAllowSeen(false);
```

### Users that are similar to a user
```php
$recommendation = UserUserRecommendation::create('user-id', 'scenario')
    ->setCount(5)
    ->setRotationRate(1.0)
    ->setRotationTime(3600);
```

### Items that are similar to an item
```php
$recommendation = ItemItemRecommendation::create('item-id', 'scenario')
    ->setCount(5);
```