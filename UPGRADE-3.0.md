# Upgrading from 2.x to 3.0

API client release 3.0 contains few backward incompatible changes.

This guide will help you upgrade your codebase.

## `UserRecommendation::create()` now accepts only `$userId` and `$scenario` 
`UserRecommendation::create()` accepts only two arguments: `$user_id` and `$scenario`.

Both arguments are required. 

All other arguments can be now set using new setters and are optional:
- `setCount(int $count)`
- `setRotationRate(float $rotationRate)`
- `setRotationTime(int $rotationTime)`

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

## `Interaction` now accepts interaction type as parameter
Matej now allows configuration of custom interaction types and interaction attributes.

At the same time, it allows specifying interaction item using item alias instead
of item ID. For that reason we removed static constructor methods for creating specific interaction types - `Interaction::detailView`, `Interaction::purchase`, `Interaction::bookmark` and `Interaction::rating`.

We replaced them with constructors for creating Interaction based on `$itemId` or `$itemIdAlias`:

```php
Interaction::withItem(
    string $interactionType,
    string $userId,
    string $itemId,
    int $timestamp = null
);
```

```php
Interaction::withAliasedItem(
    string $interactionType,
    string $userId,
    array $itemIdAlias,
    int $timestamp = null
);
```

The first argument is always a string representing interaction type. Consult the table bellow to find out the correct value to fill in:

| Before: constructor method   | After: argument $interactionType |
|------------------------------|----------------------------------|
| `Interaction::detailView`    | `"detailviews"`                  |
| `Interaction::purchase`      | `"purchases"`                    |
| `Interaction::bookmark`      | `"bookmarks"`                    |
| `Interaction::rating`        | `"ratings"`                      |

> To request new interaction types, please contact Matej support.

#### Before
```php
$interaction = Interaction::bookmark('user-id', 'item_id', time());
```

#### After
```php
$interaction = Interaction::withItem('bookmarks', 'user-id', 'item_id', time());
```
> Argument `$timestamp` remains optional.

## `Interaction` command supports custom attributes
Interactions now support custom attributes. These can be added using fluent API
methods `setAttribute()` and `setAttributes()`.

Argument `value` was removed from constructor methods and has to be set using new attribute methods.
Its real name might have changed as well. For example, for interaction type `ratings`, it was renamed to `stars`.

**Attribute `context` is no longer supported and was removed.**

#### Before
```php
$interaction = Interaction::rating('user-id', 'item_id', 0.5);
```

#### After
```php
$interaction = Interaction::create('ratings', 'user-id', 'item_id')
    ->setAttribute('stars', 0.5)
```

which is equivalent to

```php
$interaction = Interaction::create('ratings', 'user-id', 'item_id')
$interaction->setAttribute('stars', 0.5)
```
