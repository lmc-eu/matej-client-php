# Changelog

<!-- We follow Semantic Versioning (http://semver.org/) and Keep a Changelog principles (http://keepachangelog.com/) --> 

<!-- There is always Unreleased section on the top. Subsections (Added, Changed, Fixed, Removed) should be added as needed. -->

## Unreleased
### Added
- A/B testing support for recommendation and sorting requests.
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
- `UserMerge` command and `addUserMerge` & `addUserMerges` method for `EventsRequestBuilder`. (Accessible via `$matej->events()->...`).
- `Interaction` command and `addInteraction` & `addInteractions` method for `EventsRequestBuilder`. (Accessible via `$matej->events()->...`).
- `CampaignRequestBuilder` to request batch of recommendations and item sortings for multiple users. (Accessible via `$matej->campaign()->...`).
- `SortingRequestBuilder` to request item sortings. (Accessible via `$matej->sorting()->...`).
- `RecommendationRequestBuilder` to request recommendations for single user. (Accessible via `$matej->recommendation()->...`).
- Method `isSuccessfull()` of `CommandResponse` for easy and encapsulated detection of successful command responses.

### Fixed
- Return types of methods in `RequestBuilderFactory` were not defined (and were not guessable by an IDE) for PHP 5 version.

## 0.0.1 - 2017-11-23
- Initial version. Allows to build (and execute) requests of `ItemProperty` and `ItemPropertySetup` commands to `item-properties` and `events` endpoints.
