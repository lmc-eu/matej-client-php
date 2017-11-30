# Changelog

<!-- We follow Semantic Versioning (http://semver.org/) and Keep a Changelog principles (http://keepachangelog.com/) --> 

<!-- There is always Unreleased section on the top. Subsections (Added, Changed, Fixed, Removed) should be added as needed. -->

## Unreleased

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
