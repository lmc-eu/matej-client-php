# PHP API Client for Matej recommendation engine

[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/matej-client.svg?style=flat-square)](https://packagist.org/packages/lmc/matej-client)
[![Travis Build Status](https://img.shields.io/travis/lmc-eu/matej-client-php/master.svg?style=flat-square)](https://travis-ci.org/lmc-eu/matej-client-php)
[![Coverage Status](https://img.shields.io/coveralls/lmc-eu/matej-client-php/master.svg?style=flat-square)](https://coveralls.io/r/lmc-eu/matej-client-php?branch=master)

## Still using PHP 5.6?

This library requires PHP 7.1+. However, we provide also PHP 5.6-compatible version [`matej-client-php5`](https://github.com/lmc-eu/matej-client-php5).

Please note the PHP 5.6 version is just transpiled copy of this library - examples, pull requests, issues, changelog etc. are placed in this repository.

## Installation

Matej API client library is installed using [Composer](https://getcomposer.org/).

We use [HTTPlug](https://github.com/php-http/httplug) abstraction so that the library is not hard coupled to
any specific HTTP library, allowing you to use HTTP library which fits your needs (or maybe HTTP library you already use).

This means that besides the `lmc/matej-client` library itself you must install a package which provides
`php-http/client-implementation` - see [list of supported clients and adapters](http://docs.php-http.org/en/latest/clients.html).

If you, for example, want to use Guzzle 6 as the underlying HTTP library, install the package like this:

```sh
$ composer require lmc/matej-client php-http/guzzle6-adapter # use lmc/matej-client-php5 instead for PHP 5.6 version
```

Or if you want to use cURL client:

```sh
$ composer require lmc/matej-client php-http/curl-client guzzlehttp/psr7 # use lmc/matej-client-php5 instead for PHP 5.6 version
```

## Usage

To start using Matej you will need your account id (database name) and secret API key - both of them must be obtained
from LMC R&D team.

First create an instance of `Matej` object:
```php
$matej = new Matej('accountId', 'apikey');
```

Now you can use `request()` method to use *builders*, which are available for each Matej endpoint and which will
help you to assemble the request. Each request builder accepts via its methods instances of various Command(s)
objects (see `Lmc\Matej\Model\Command` namespace). Refer to Matej documentation, code-completion in your IDE or examples
below for more information.

Once finished with building the request, use `send()` method to execute it and retrieve the response:

```php
$response = $matej->request()
    ->events()
    ->addInteraction(\Lmc\Matej\Model\Command\Interaction::purchase('user-id', 'item-id'))
    ->addUserMerge(...)
    ...
    ->send();
    ...
```

See below for examples of building request for each endpoint.

To process the response:

```php
echo 'Number of commands: ' . $response->getNumberOfCommands() . "\n";
echo 'Number of successful commands: ' . $response->getNumberOfSuccessfulCommands() . "\n";
echo 'Number of failed commands: ' . $response->NumberOfFailedCommands()() . "\n";

// Iterate over getCommandResponses() to get response for each command passed to the builder.
// Commands in the response are present in the same order as they were added to the requets builder.
foreach ($response->getCommandResponses() as $commandResponse) {
    if ($commandResponse->isSuccessful()) {
        // Methods $commandResponse->getData(), ->getMessage() and ->getStatus() are available
    } else {
        // Log error etc.
    }
}
```

[Recommendation](#recommendations-for-single-user), [Sorting](#request-item-sorting-for-single-user)
and [Item Properties](#item-properties-setup-to-setup-you-matej-database) endpoints have syntax sugar,
which makes processing responses easier. See below for detailed examples.

### Item properties setup (to setup you Matej database)

```php
$matej = new Matej('accountId', 'apikey');

// Create new item property in database:
$response = $matej->request()
    ->setupItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_to'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();

// Get list of item properties that are defined in matej
$response = $matej->request()
    ->getItemProperties()
    ->send();

$properties = $response->getData();

// Delete item property from database:
$response = $matej->request()
    ->deleteItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_from'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();
```

### Send Events data to Matej

You can use `events()` builder for sending batch of following commands to Matej:
- `Interaction` via `addInteraction()` - to send information about interaction between user and item
- `ItemProperty` via `addItemProperty()` - to update item data stored in Matej database
- `UserMerge` via `addUserMerge()` - to merge interactions of two users and delete the source user

You can mix different command types in the same request. You can send up to 1 000 commands in a single request.

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->events()
    // Add interaction between user and item
    ->addInteraction(Interaction::purchase('user-id', 'item-id'))
    ->addInteractions([/* array of Interaction objects */])
    // Update item data
    ->addItemProperty(ItemProperty::create('item-id', ['valid_from' => time(), 'title' => 'Title']))
    ->addItemProperties([/* array of ItemProperty objects */])
    // Merge user
    ->addUserMerge(UserMerge::mergeInto('target-user-id', 'source-user-id'))
    ->addUserMerges([/* array of UserMerge objects */])
    ->send();
```

### Recommendations for single user

You can get recommendations for single user using `recommendation()` builder.
You can attach most recent interaction and user merge event to the request, so that they're taken into account
when providing recommendations.

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->recommendation(UserRecommendation::create('user-id', 5, 'test-scenario', 1.0, 3600))
    ->setInteraction(Interaction::purchase('user-id', 'item-id')) // optional
    ->setUserMerge(UserMerge::mergeInto('user-id', 'source-id')) // optional
    ->send();

$recommendations = $response->getRecommendation()->getData();
```

You can also set more granular options of the recommendation command:

```php
$recommendation = UserRecommendation::create('user-id', 5, 'test-scenario', 1.0, 3600);
$recommendation->setFilters(['valid_to >= NOW']) // Note this filter is present by default
    ->setMinimalRelevance(UserRecommendation::MINIMAL_RELEVANCE_HIGH)
    ->enableHardRotation();

$response = $matej->request()
    ->recommendation($recommendation)
    ->send();
```

From `$response`, you can also access rest of the data:

```php
$response = $matej->request()
    ->recommendation($recommendation)
    ->send();

echo $response->getInteraction()->getStatus();    // SKIPPED
echo $response->getUserMerge()->getStatus();      // SKIPPED
echo $response->getRecommendation()->getStatus(); // OK

$recommendations = $response->getRecommendation()->getData();
```

### Request item sorting for single user

Request item sorting for single user. You can combine this sorting command with the most recent interaction
and user merge event in one request, to make them taken into account when executing the item sorting.

```php
$matej = new Matej('accountId', 'apikey');

$response =  $matej->request()
    ->sorting(Sorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->setInteraction(Interaction::purchase('user-id', 'item-id')) // optional
    ->setUserMerge(UserMerge::mergeInto('user-id', 'source-id')) // optional
    ->send();

$sortedItems = $response->getSorting()->getData();
```

From `$response`, you can also access rest of the data:

```php
$response = $matej->request()
    ->sorting($sorting)
    ->send();

echo $response->getInteraction()->getStatus(); // SKIPPED
echo $response->getUserMerge()->getStatus();   // SKIPPED
echo $response->getSorting()->getStatus();     // OK

$sortedData = $response->getSorting()->getData();
```

### Request batch of recommendations/item sortings

Use `campaign()` builder to request batch of recommendations and/or item sorting for multiple users.
Typical use case for this is generating email campaigns.

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->campaign()
    // Request item sortings
    ->addSorting(Sorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->addSortings([/* array of Sorting objects */])
    // Request user-based recommendations
    ->addRecommendation(UserRecommendation::create('user-id', 10, 'emailing', 1.0, 3600))
    ->addRecommendations([/* array of UserRecommendation objects */])
    ->send();
```

### Exceptions and error handling

Exceptions are thrown only if the whole Request to Matej failed (when sending, decoding, authenticating etc.) or if
the library is used incorrectly. If the request is successfully delivered to Matej, **exception is not thrown** even
if any (or maybe all) of the submitted commands (which were part of the request) were rejected by Matej.
This means to make sure any individual CommandResponse was successful, you need to check its status
(eg. using `isSuccessful()` method) or compare value of `getNumberOfSuccessfulCommands()` - see usage examples above.

Exceptions occurring inside Matej API client implements `Lmc\Matej\Exception̈́\MatejExceptionInterface`.
The exception tree is:

| Exception                                         | Thrown when                                                   |
|---------------------------------------------------|---------------------------------------------------------------|
| MatejExceptionInterface                           | Common interface of all Matej exceptions                      |
| └ RequestException                                | Request to Matej errored                                      |
| &nbsp;&nbsp;└ AuthorizationException              | Request errored as unauthorized                               |
| └ ResponseDecodingException                       | Response contains invalid or inconsistent data                |
| └ LogicException                                  | Incorrect library use - no data passed to request etc.        |
| &nbsp;&nbsp;└ DomainException                     | Invalid value was passed to domain model                      |

Please note if you inject custom HTTP client (via `$matej->setHttpClient()`), it may be configured to throw custom
exceptions when HTTP request fails. So please make sure this behavior is disabled (eg. `http_errors` option in Guzzle 6).

## Changelog
For latest changes see [CHANGELOG.md](CHANGELOG.md) file. We follow [Semantic Versioning](http://semver.org/).

## Running Tests

For each pull-request, unit tests, as well as static analysis and codestyle checks, must pass.

To run all those checks execute:

```sh
$ composer all
```
