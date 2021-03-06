# Releasing new Matej PHP Client version

1. Check [open pull-requests](https://github.com/lmc-eu/matej-client-php/pulls), if there are any, you may coordinate releasing of a new version with authors of these pull requests.
1. If there are some backward incompatible changes, the specific message in CHANGELOG.md must be prefixed with `**BC BREAK**`.
1. Now new version number needs to be determined. The format is `MAJOR.MINOR.PATCH` and you MUST follow [SemVer rules](http://semver.org/):
    - Increase MAJOR version number, if there are any API backward incompatible changes, eg. renaming/removing of any public interface etc.
    - Increase MINOR version number when you add functionality in a backward-compatible manner.
    - Increase PATCH version if the release contains *only* backward-compatible bug fixes.
1. Update [CHANGELOG.md](CHANGELOG.md):
    - Add header in form `## X.Y.Z - YYYY-MM-DD` above the list of changes that were already merged.
    - You should keep the `## Unreleased` header (but with no content below it) on the top of the document.
    - Also, make sure the CHANGELOG.md contains an entry for each notable change done in the repository since the last release.
1. Change client version constant `VERSION` in `src/Matej.php` to match the new version number.
1. Commit the changes in `CHANGELOG.md` and `Matej.php` directly to the main branch:
    - `git commit -m "Release version X.Y.Z"`
1. Tag the release:
    - `git tag X.Y.Z`
1. Push the changes in main branch and tags to the repository:
    - `git push origin main`
    - `git push origin X.Y.Z`
