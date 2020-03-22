# Upgrading from 2.x to 3.0

API client release 2.0 contains few backward incompatible changes.

This guide will help you upgrade your codebase.

## `UserRecommendation::create()` now accepts only `$user_id` and `$scenario` 
`UserReccomentation::create()` accepts only two argumens: `$user_id` and `$scenario`.
Both arguments are required. All other arguments are optional their default values are
configured using admin 

Recommendation command can be further parametrized using fluent API.

#### Before
```php
$recommendation = UserRecommendation::create('user-id', 5, 'scenario', 1.0, 3600);
```

#### After
```php
$recommendation = UserRecommendation::create('user-id', 'scenario')
	->setCount(5)
	->setRotationRate(1.0)
	->setRotationTime(3600);
```

which is equivalent to

```php
$recommendation = UserRecommendation::create('user-id', 'scenario');
$recommendation->setCount(5);
$recommendation->setRotationRate(1.0);
$recommendation->setRotationTime(3600);
```