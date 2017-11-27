# Changelog

<!-- We follow Semantic Versioning (http://semver.org/) and Keep a Changelog principles (http://keepachangelog.com/) --> 

<!-- There is always Unreleased section on the top. Subsections (Added, Changed, Fixed, Removed) should be added as needed. -->

## Unreleased
### Added
- `UserMerge` command and `addUserMerge` & `addUserMerges` method for `EventsRequestBuilder`. (Accessible via `$matej->events()->...`).
- `Interaction` command and `addInteraction` & `addInteractions` method for `EventsRequestBuilder`. (Accessible via `$matej->events()->...`).

### Fixed
- Return types of methods in `RequestBuilderFactory` were not defined (and were not guessable by an IDE) for PHP 5 version.

## 0.0.1 - 2017-11-23
- Initial version. Allows to build (and execute) requests of `ItemProperty` and `ItemPropertySetup` commands to `item-properties` and `events` endpoints.
