# Changelog

<!-- We follow Semantic Versioning (http://semver.org/) and Keep a Changelog principles (http://keepachangelog.com/) --> 

<!-- There is always Unreleased section on the top. Subsections (Added, Changed, Fixed, Removed) should be added as needed. -->

## Unreleased
### Added
- Endpoint to get all defined item properties in the Matej database (`$matej->request()->getItemProperties()`).

### Changed
- Validate max. 1000 commands are added to `campaign()`, `events()`, `setupItemProperties()` and `deleteItemProperties()` requests (in accordance with Matej batch API limit).
- `Response` now contains `getCommandResponse(int $index)` to access individual command responses.
- `sorting()->send()` request now returns `SortingResponse` instance, which provides semantic access to data of the response (ie `->getSorting()`).
- `recommendation()->send()` request now returns `RecommendationResponse` instance, which provides semantic access to data of the response (ie. `->getRecommendation()`).
- `getItemProperties()->send()` request now returns `ItemPropertiesListResponse` instance, which provides semantic access to data of the response (ie. `->getData()`).

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
