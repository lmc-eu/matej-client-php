# PHP API Client for Matej recommendation engine

[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/matej-client.svg?style=flat-square)](https://packagist.org/packages/lmc/matej-client)
![Required PHP version](https://img.shields.io/packagist/dependency-v/lmc/matej-client/php?style=flat-square)
[![GitHub Actions Build Status](https://img.shields.io/github/actions/workflow/status/lmc-eu/matej-client-php/php.yaml?style=flat-square&label=GitHub%20Actions%20build)](https://github.com/lmc-eu/matej-client-php/actions)
[![Coverage Status](https://img.shields.io/coveralls/lmc-eu/matej-client-php/main.svg?style=flat-square)](https://coveralls.io/r/lmc-eu/matej-client-php?branch=main)

## Installation

Matej API client library is installed using [Composer](https://getcomposer.org/).
**Please be aware you must properly set-up [autoloading](https://getcomposer.org/doc/01-basic-usage.md#autoloading) in your project first.**

We use [HTTPlug](https://github.com/php-http/httplug) abstraction so that the library is not hard coupled to
any specific HTTP library, allowing you to use HTTP library which fits your needs (or maybe HTTP library you already use).

This means that besides the `lmc/matej-client` library itself you must install a package which provides
`php-http/client-implementation` - see [list of supported clients and adapters](http://docs.php-http.org/en/latest/clients.html).

If you, for example, want to use Guzzle 6 as the underlying HTTP library, install the package like this:

```sh
$ composer require lmc/matej-client php-http/guzzle6-adapter
```

Or if you want to use Guzzle 5 (note that unlike `guzzle6-adapter`, the one does not come with `guzzlehttp/psr7`, so you must install it as well):

```sh
$ composer require lmc/matej-client php-http/guzzle5-adapter guzzlehttp/psr7
```

Or if you want to use cURL client:

```sh
$ composer require lmc/matej-client php-http/curl-client guzzlehttp/psr7
```

## Usage

To start using Matej you will need your account id (database name) and secret API key - both of them must be obtained from LMC R&D team.

First create an instance of `Matej` object:
```php
use Lmc\Matej\Matej; // in all following examples namespaces and use statements are ommited from the code samples

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
    ->addInteraction(\Lmc\Matej\Model\Command\Interaction::withItem('purchases', 'user-id', 'item-id'))
    ->addUserMerge(...)
    ...
    ->send();
```

See further below for examples of building request for each endpoint.

### Processing the response

Once `$response` is filled with data from Matej (as in example above), you can now work with the response like this:

```php
echo 'Number of commands: ' . $response->getNumberOfCommands() . "\n";
echo 'Number of successful commands: ' . $response->getNumberOfSuccessfulCommands() . "\n";
echo 'Number of failed commands: ' . $response->getNumberOfFailedCommands()() . "\n";

// Use $response->isSuccessful() to check whether all of the commands send in request were successful or not:
if (!$response->isSuccessful()) {
    echo 'At least one command response was not succesful!';
}

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
and [Item Properties](#item-properties-setup-to-setup-your-matejs-database) endpoints have syntax sugar shortcuts,
which makes processing responses easier. See below for detailed examples.

### Item properties setup (to setup your Matej's database)

```php
$matej = new Matej('accountId', 'apikey');

// Create new item property in database:
$response = $matej->request()
    ->setupItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_to'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();

// Get list of item properties that are defined in Matej
$response = $matej->request()
    ->getItemProperties()
    ->send();

$properties = $response->getData(); // this is shortcut for $response->getCommandResponse(0)->getData()

// Delete item property from database:
$response = $matej->request()
    ->deleteItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_from'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();
```

### Reset database

Database of test accounts (those ending with `-test`) could be reset via the API.
Using this you can delete all data including database setup (item properties).

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->resetDatabase()
    ->send();

var_dump($response->isSuccessful()); // true on success
```

### Reset data

Data (users and items) of test accounts (those ending with `-test`) could be reset via the API.
Using this you can delete all data while keeping database setup (item properties, scenarios).

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->resetData()
    ->send();

var_dump($response->isSuccessful()); // true on success
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
    ->addInteraction(Interaction::withItem('purchases', 'user-id', 'item-id'))
    ->addInteractions([/* array of Interaction objects */])
    // Update item data
    ->addItemProperty(ItemProperty::create('item-id', ['valid_from' => time(), 'title' => 'Title']))
    ->addItemProperties([/* array of ItemProperty objects */])
    // Merge user
    ->addUserMerge(UserMerge::mergeInto('target-user-id', 'source-user-id', 1629361884))
    ->addUserMerges([/* array of UserMerge objects */])
    ->send();
```

**This endpoint has rate-limiting implemented.** We constantly monitor workload on backend systems,
and when the number of events in the queue crosses certain threshold, Matej API will start returning `429` errors.
If that happens, you should resend the entire request later, as no commands were processed.

This has been implemented so that we don't lose any pushed data. Simple sleep of 100ms should be enough.

### Merging users

You can merge users using the `UserMerge` command. The first argument is the target user ID and the second argument is the ID
of source user. When you merge two users, Matej will move all interactions and history of the source user and assign them to
the target user. The source user is then removed.

Optionally, you may send a third argument with a timestamp of when the merge happened.

**The timestamp will be required in the future. We reccommend you to send it which will make future upgrades of the Matej client easier for you.**

### Requesting recommendations

You can request 4 types of recommendations from Matej. Each of them is represented by a specific recommendation command class:

- Items to user - UserItemRecommendation
- Items to item - ItemItemRecommendation
- Users to user - UserUserRecommendation
- Users to item - ItemUserRecommendation

For example, you can get recommendations for a single user using `recommendation()` builder.
You can attach most recent interaction and user merge event to the request so that they're taken into account
when providing recommendations.

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->recommendation(UserItemRecommendation::create('user-id', 'test-scenario'))
    ->setInteraction(Interaction::withItem('purchases', 'user-id', 'item-id')) // optional
    ->setUserMerge(UserMerge::mergeInto('user-id', 'source-id')) // optional
    ->send();

$recommendations = $response->getRecommendation()->getData();
```

You can also set more granular options of the recommendation command and overwrite Matej default behavior on per-request basis.

Each type of recommendation command supports different customization options. See table below.


#### Available recommendation attributes

| Attribute      | Methods                                   | UserItemRecommendation | UserUserRecommendation | ItemItemRecommendation | ItemUserRecommendation |
|---------------|-------------------------------------------|------------------------|------------------------|------------------------|------------------------|
| scenario      |              in constructor               |            ✅           |            ✅           |            ✅           |            ✅           |
| count         |                  `setCount`                 |            ✅           |            ✅           |            ✅           |            ✅           |
| rotation_rate |              `setRotationRate`              |            ✅           |            ✅           |            ❌           |            ❌           |
| rotation_time |              `setRotationTime`              |            ✅           |            ✅           |            ❌           |            ❌           |
| hard_rotation |             `enableHardRotation`            |            ✅           |            ✅           |            ❌           |            ❌           |
| allow_seen    |                `setAllowSeen`               |            ✅           |            ❌           |            ❌           |            ✅           |
| min_relevance |            `setMinimalRelevance`            |  `ItemMinimalRelevance`  |            ❌           |            ❌           |  `UserMinimalRelevance`  |
| filter        |            `addFilter` `setFilters`           |            ✅           |            ❌           |            ✅           |            ❌           |
| boost_rules   |             `addBoost` `setBoosts`            |            ✅           |            ❌           |            ✅           |            ❌           |
| model_name    |                `setModelName`               |            ✅           |            ✅           |            ✅           |            ✅           |
| properties    | `addResponseProperty` `setResponseProperties` |            ✅           |            ❌           |            ✅           |            ❌           |


```php
$recommendation = UserItemRecommendation::create('user-id', 'test-scenario')
    ->setCount(5)
    ->setRotationRate(1.0)
    ->setRotationTime(3600)
    ->setFilters(['for_recommendation = 1'])
    ->setMinimalRelevance(ItemMinimalRelevance::HIGH())
    ->enableHardRotation()
    // You can further modify which items will be recommended by providing boosting rules.
    // Priority of items matching the query will be multiplied by the value of multiplier:
    ->addBoost(Boost::create('valid_to >= NOW()', 2));

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

// var_dump($recommendations):
// array(2) {
//     [0] => object(stdClass)#1 (2) {
//         ["item_id"]  => string(9) "item_id_1"
//     }
//     [1] => object(stdClass)#2 (2) {
//         ["item_id"]  => string(9)  "item_id_2"
//     }
// }
```

#### Recommendation response properties

Every item in Matej has its id, and optionally other item properties. These properties can be set up in [item properties setup](#item-properties-setup-to-setup-you-matej-database),
and you can upload item data in the [events](#send-events-data-to-matej) request. This has major benefit because you can request
these properties to be returned as part of your Recommendation Request.

We call them response properties. They can be specified by calling `->addResponseProperty()` method or by calling `->setResponseProperties()` method. Following will request an `item_id`, `item_url`, `item_title`:

```php
$recommendation = UserItemRecommendation::create('user-id', 'test-scenario')
    ->addResponseProperty('item_title')
    ->addResponseProperty('item_url');

$response = $matej->request()
    ->recommendation($recommendation)
    ->send();

$recommendedItems = $response->getRecommendation()->getData();

// $recommendedItems is an array of stdClass instances:
//
// array(2) {
//     [0] => object(stdClass)#1 (2) {
//         ["item_id"]  => string(9) "item_id_1"
//         ["item_url"] => string(5) "url_1"
//         ["item_title"] => string(5) "title_1"
//     }
//     [1] => object(stdClass)#2 (2) {
//         ["item_id"]  => string(9)  "item_id_2"
//         ["item_url"] => string(10) "url_2"
//         ["item_title"] => string(10) "title_2"
//     }
// }
```

If you don't specify any response properties, Matej will return an array of `stdClass` instances, which contain only `item_id` property.
If you do request at least one response property, you don't need to mention `item_id`, as Matej will always return it regardless of the
properties requested.

If you request an unknown property, Matej will return a `BAD REQUEST` with HTTP status code `400`.

This way, when you receive recommendations from Matej, you don't need to loop the `item_id` and retrieve further information
from your local database. It means, however, that you'll have to keep all items up to date within Matej,
which can be done through [events](#send-events-data-to-matej) request.

### Request item sorting for single user

Request item sorting for a single user. You can combine this sorting command with the most recent interaction
and user merge event in one request, to make them taken into account when executing the item sorting.

```php
$matej = new Matej('accountId', 'apikey');

$response =  $matej->request()
    ->sorting(ItemSorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->setInteraction(Interaction::withItem('purchases', 'user-id', 'item-id')) // optional
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
A typical use case for this is generating email campaigns.

```php
$matej = new Matej('accountId', 'apikey');

$response = $matej->request()
    ->campaign()
    // Request item sortings
    ->addSorting(ItemSorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']))
    ->addSortings([/* array of Sorting objects */])
    // Request user-based recommendations
    ->addRecommendation(UserItemRecommendation::create('user-id', 'emailing'))
    ->addRecommendations([/* array of UserRecommendation objects */])
    ->send();
```

### A/B Testing support
`Recommendation` and `ItemSorting` commands support optional A/B testing of various models. This has to be set up in Matej first,
but once available, you can specify which model you want to use when requesting recommendations or sorting.

This is available for `Recommendation`, `ItemSorting` and `Campaign` requests:

```php
$recommendationCommand = UserItemRecommendation::create('user-id', 'test-scenario')
    ->setModelName('alpha');

$sortingCommand = ItemSorting::create('user-id', ['item-id-1', 'item-id-2', 'item-id-3']);
$sortingCommand->setModelName('beta');

$response = $matej->request()
    ->recommendation($recommendationCommand)
    ->send();

$response = $matej->request()
    ->sorting($sortingCommand)
    ->send();

$response = $matej->request()
    ->campaign()
    ->addRecommendation($recommendationCommand->setModelName('gamma'))
    ->addSorting($sortingCommand->setModelName('delta'))
    ->send();
```

If you don't provide any model name, the request will be sent without it, and Matej will use default model for your instance.

Typically, you'd select a random sample of users, to which you'd present recommendations and sorting from second model. This way, implementation
in your code should look similar to this:

```php
$recommendation = UserItemRecommendation::create('user-id', 'test-scenario');

if ($session->isUserInBucketB()) {
    $recommendation->setModelName('alpha');
}

$response = $matej->request()->recommendation($recommendation)->send();
```

Model names will be provided to you by LMC.

### Forgetting user data (GDPR)
Matej can "forget" user data, either by anonymizing or by deleting them. The right to erasure ("right to be forgotten") is part of
[General Data Protection Regulation in the European Union](https://eur-lex.europa.eu/legal-content/EN/TXT/HTML/?uri=CELEX:32016R0679#d1e2606-1-1)
and can be implemented on your end using the `forget()` builder.

There are two ways how to remove user data, but both of them aren't reversible and you will not be able to identify
the user ever again:

* Preferred way is to `anonymize` the user, which will randomly generate unique identifiers for all personal data,
  and change that identifier across all databases and logfiles. This way the users behavior will stay in Matej database,
  and therefore **will continue to contribute to the recommendation model**, but you won't be able to identify the user.
  Thus his profile will be effectively frozen (as no new interactions can come in.) **New user id is generated server-side**,
  so there is no going back after issuing the request.
* An alternate way is to `delete` the user, which will wipe their data from all databases in accordance
  with the Data Protection laws. This may affect the quality of recommendations, as the users behavior will be completely
  removed from all databases, and therefore their profile will not contribute to the recommendation model anymore.

Usually, though, the user will identify whether they want their data anonymized or deleted, and you have to adhere to their request.

To call the endpoint, use the `forget()` builder and append the users:

```php
$matej = new Matej('accountId', 'apikey');

$matej->request()
    ->forget()
    ->addUser(UserForget::anonymize('anonymize-this-user-id'))
    ->addUser(UserForget::anonymize('delete-this-user-id'))
    ->addUsers([
        UserForget::anonymize('anonymize-this-user-id-as-well'),
        UserForget::delete('delete-this-user-id-as-well'),
    ])
    ->send()
;
```

### Exceptions and error handling

Exceptions are thrown only if the whole Request to Matej failed (when sending, decoding, authenticating etc.) or if
the library is used incorrectly. If the request is successfully delivered to Matej, **exception is not thrown** even
if any (or maybe all) of the submitted commands (which were part of the request) were rejected by Matej.

To check whether the whole Response (ie. all contained command responses) is successful, you thus MUST NOT rely on exceptions
(because they won't be thrown - as stated above) but rather use `Response::isSuccessful()` method - see [usage examples](#processing-the-response) above.

If you want to check which individual CommandResponse was successful, you can check its status using `CommandResponse::isSuccessful()` method.

Exceptions occurring inside Matej API client implements `Lmc\Matej\Exception\MatejExceptionInterface`.
The exception tree is:

| Exception                                         | Thrown when                                                   |
|---------------------------------------------------|---------------------------------------------------------------|
| MatejExceptionInterface                           | Common interface of all Matej exceptions                      |
| └ RequestException                                | Request to Matej errored (see below for troubleshooting howto)|
| &nbsp;&nbsp;└ AuthorizationException              | Request errored as unauthorized                               |
| └ ResponseDecodingException                       | Response contains invalid or inconsistent data                |
| └ LogicException                                  | Incorrect library use - no data passed to request etc.        |
| &nbsp;&nbsp;└ DomainException                     | Invalid value was passed to domain model                      |

Please note if you inject custom HTTP client (via `$matej->setHttpClient()`), it may be configured to throw custom
exceptions when HTTP request fails. So please make sure this behavior is disabled (eg. `http_errors` option in Guzzle 6).

#### Troubleshooting `RequestException`

If `RequestException` is thrown when doing request to Matej, you may want to read the response body from the server
to see the full error originating from Matej, so that you can troubleshoot the cause of the exception.

You can do it like this:

```php
$matej = new Matej('accountId', 'apikey');

try {
    $response = $matej
        ->request()
        ->sorting(/* ... */)
        // ...
        ->send();
} catch (\Lmc\Matej\Exception\RequestException $exception) {
    echo $e->getMessage(); // this will output just HTTP reason phrase, like "Bad Request"
    $serverResponseContents = $exception->getResponse()
        ->getBody()
        ->getContents();

    echo $serverResponseContents; // this will output the full response body which Matej server gave
}
```

## Changelog
For latest changes see [CHANGELOG.md](CHANGELOG.md) file. We follow [Semantic Versioning](http://semver.org/).

## Releasing new version
See [RELEASE.md](RELEASE.md) for step-by-step how to release new client version.

## Running Tests

For each pull-request, unit tests, as well as static analysis and codestyle checks, must pass.

To run all those checks execute:

```sh
$ composer all
```

In case of codestyle violation you can run this command which will try to automatically fix the codestyle:


```sh
$ composer fix
```
