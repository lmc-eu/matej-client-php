# Upgrading from 3.x to 4.0

API client release 4.0 contains backward incompatible changes.

This guide will help you upgrade your codebase.

## Recommending items to users
Class `UserRecommendation` was renamed to `UserItemRecommendation`. All other methods and attributes remained unchanged.

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
    ->setMinimalRelevance(MinimalRelevance::LOW())
    ->setCount(5)
    ->setRotationRate(1.0)
    ->setRotationTime(3600);
```
