# Changelog

<!-- We follow Semantic Versioning (http://semver.org/) and Keep a Changelog principles (http://keepachangelog.com/) --> 

<!-- There is always Unreleased section on the top. Subsections (Added, Changed, Fixed, Removed) should be added as needed. -->

## Unreleased
### Added
- Added support for new recommendation types - `UserUserRecommendation`, `ItemUserRecommendation`, `ItemItemRecommendation`.

### Changed
- **BC BREAK** | Removed deprecated constant `FILTER_TYPE_MQL` from `UserRecommendation`.
- **BC BREAK** | Class `UserRecommendation` renamed to `UserItemRecommendation`.
- **BC BREAK** | Class `MinimalRelavance` renamed to `ItemMinimalRelevances`.

## 3.1.0 - 2020-10-13
### Fixed
- Attribute `item-id` renamed to `item_id` in recommendation request response.

### Added
- Added support for Matej command `resetData()` to remove data from instance.

## 3.0.2 - 2020-09-24
- Mark `UserRecommendation::FILTER_TYPE_MQL` deprecated.

### Fixed
- Response to UserRecommendation request without specified item properties now works as documented.
Each recommendation is an instance of `StdClass` containing `item_id` property.

## 3.0.1 - 2020-08-18
### Changed
- Client doesn't send no longer used parameter `filter_type`.

## 3.0.0 - 2020-05-14
### Added
- Add support for recommendation boosting rules (via `addBoost()`).
- Custom attributes which can be send as part of `Interaction` using `setAttribute()`/`setAttributes()` methods. See [UPGRADE-3.0.md](UPGRADE-3.0.md).

### Changed
- **BC BREAK** | `UserRecommendation::create()` now accepts only `$user_id` and `$scenario` parameters, all other arguments are optional and are set using setter methods. See [UPGRADE-3.0.md](UPGRADE-3.0.md) for details.
- **BC BREAK** | Interaction types are now defined via string parameter (and not named constructors) and can be created using new `withItem()` or `withAliasedItem()` methods. See [UPGRADE-3.0.md](UPGRADE-3.0.md).

## 2.4.0 - 2020-01-21
- No changes in the library, new version released because of adjustments of PHP 5.6 compatible fork.

## 2.3.2 - 2019-12-10
### Changed
- Internal codestyle improvements.

## 2.3.1 - 2019-08-24
### Changed
- Allow httplug 2.0 dependency.
- Mark some internal classes `@internal`.

## 2.3.0 - 2019-01-21
### Added
- Method `isSuccessfull()` to `Response` providing simple check whether the whole response (all of contained command responses) are successful (ie. none of them is invalid).
This is also recommended way how to validate response successfulness.

### Changed
- Invalid command inside recommendation/sorting request does not cause whole request to fail, rather the specific command response is marked as invalid.
- Deprecated `CommandResponse::STATUS_ERROR`, because Matej no longer uses it (`CommandResponse::STATUS_INVALID` is instead used everywhere).
- Method `CommandResponse::isSuccessfull()` now returns true even for not only OK but also SKIPPED (ie. not invalid) statuses.
This also means `Response::getNumberOfSuccessfulCommands()` now won't necessarily return the same number of command responses that return true on `CommandResponse::isSuccessful()`.

## 2.2.0 - 2018-10-02
### Added
- New item property type `geolocation`.

## 2.1.1 - 2018-09-21
### Fixed
- Client version information sent in request header.

## 2.1.0 - 2018-09-20
### Added
- `UserRecommendation` has new method `setAllowSeen(true|false)`, which allows recommending of already visited items.

## 2.0.0 - 2018-09-04
See [UPGRADE-2.0.md](UPGRADE-2.0.md) for upgrade instructions.

### Changed
- **BC BREAK** | `UserRecommendation` now returns new format of response in `->getData()`, which is a list of `stdClass` instances.
- **BC BREAK** | `UserRecommendation` does not have default filter (was previously set to: `valid_to >= NOW`).
- **BC BREAK** | `UserRecommendation` now uses MQL query language by default for filtering.
- **BC BREAK** | Minimal relevance passed to `setMinimalRelevance()` method of `UserRecommendation` command must be of enum type `MinimalRelevance`.
- **BC BREAK** | `getForgetMethod()` method of `UserForget` command now returns instance of `UserForgetMethod` enum instead of string.

### Fixed
- Exceptions occurring during async request now generate rejected promise (as they should) and are no longer thrown directly.

### Added
- Ability to request item properties in `UserRecommendation` command, which are then returned by Matej alongside with `item_id`.

## 1.6.0 - 2018-06-01
### Added
- `UserForget` command to anonymize or completely delete all data belonging to specific user(s) (`$matej->request()->forget()`).

### Changed
- Declare missing direct dependencies (to `php-http/message`, `php-http/promise` and `psr/http-message`) in composer.json.

## 1.5.0 - 2018-04-17
### Added
- A/B testing support for recommendation and sorting requests.

### Changed
- Allow users of commands passed to `$matej->request()->recommendation()` to be: (B [interaction], A -> B [user merge], B [recommendation]) in accordance with new Matej API behavior.
- Type hints and type assertions relevant only for PHP 5 version removed from codebase and are now added only to PHP 5 version of the library (in `RequestBuilderFactory`, `Command\Interaction`, `Command\UserRecommendation`).

## 1.4.0 - 2018-02-23
### Added
- Endpoint to reset Matej test-databases (`$matej->request()->resetDatabase()`).

### Fixed
- Check for equality of user ids in `UserMerge` - so that we don't "merge" user into the same user id

## 1.3.0 - 2018-01-19
### Changed
- Allow setting rotation time = 0 in UserRecommendation, because it is now also accepted by Matej API.

## 1.2.0 - 2018-01-11
### Changed
- Unify `self`/`static`/`$this` return typehints.

### Fixed
- Checking of consistent user ids on `recommendation()` and `sorting()` requests was not working in accordance with API behavior.

## 1.1.0 - 2017-12-20
### Added
- Endpoint to get all defined item properties in the Matej database (`$matej->request()->getItemProperties()`).
- `Response` now contains `getCommandResponse(int $index)` to provide direct access to individual command responses.
- Syntaxt sugar shortcuts to provide semantic access to data of the responses:
  - `sorting()->send()` now returns `SortingResponse` instance (so you can call eg. `$response->getSorting()`).
  - `recommendation()->send()` now returns `RecommendationResponse` instance (so you can call eg. `$response->getRecommendation()`).
  - `getItemProperties()->send()` now returns `ItemPropertiesListResponse` instance (so you can call eg. `getItemProperties()->send()->getData()`).

### Changed
- Validate max. 1000 commands are added to `campaign()`, `events()`, `setupItemProperties()` and `deleteItemProperties()` requests (in accordance with Matej batch API limit).

## 1.0.0 - 2017-12-05
### Added
- Commands which include user now implements `UserAwareInterface` and `getUserId()` method (ie. Interaction, Sorting, UserMerge, UserRecommendation).
- Custom request ID could be passed to a request (via `setRequestId()` method of request builders). If none is set, random request ID is generated.
- Response ID could be read from the response via `getRequestId()` method of the Response object.

### Changed
- Validate all commands of `recommendation()` and `sorting()` request involve the same user.
- Validate Item properties to not contain `$property['item_id']` as that would redefine the primary key in Matej database.
- Validate Item property setup to not set up property named `item_id` as that would conflict with the primary key in Matej database.

### Fixed
- URL assembling was not working on systems with non-standard setting of `arg_separator.output` PHP directive.

## 0.10.0 - 2017-11-30
### Changed
- All exceptions now implement `MatejExceptionInterface` instead of subclassing `AbstractMatejException`.
- Exceptions raised during response processing are now all of `ResponseDecodingException` type.
- Values passed to Command objects are now validated on construction and throws `Lmc\Matej\Exception\DomainException` when invalid.

## 0.9.0 - 2017-11-27
### Added
- `UserMerge` command and `addUserMerge` & `addUserMerges` method for `EventsRequestBuilder`. (Accessible via `$matej->request()->events()->...`).
- `Interaction` command and `addInteraction` & `addInteractions` method for `EventsRequestBuilder`. (Accessible via `$matej->request()->events()->...`).
- `CampaignRequestBuilder` to request batch of recommendations and item sortings for multiple users. (Accessible via `$matej->request()->campaign()->...`).
- `SortingRequestBuilder` to request item sortings. (Accessible via `$matej->request()->sorting()->...`).
- `RecommendationRequestBuilder` to request recommendations for single user. (Accessible via `$matej->request()->recommendation()->...`).
- Method `isSuccessfull()` of `CommandResponse` for easy and encapsulated detection of successful command responses.

### Fixed
- Return types of methods in `RequestBuilderFactory` were not defined (and were not guessable by an IDE) for PHP 5 version.

## 0.0.1 - 2017-11-23
- Initial version. Allows to build (and execute) requests of `ItemProperty` and `ItemPropertySetup` commands to `item-properties` and `events` endpoints.
