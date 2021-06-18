# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.0.3] - 2021-06-15
### Added
- Data attributes to Site Automation widgets/blocks (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter) via [#64](https://github.com/globeandmail/sophi-for-wordpress/pull/64)).

### Changed
- Revisions to tracking data collected to improve Site Automation results (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter) via [#65](https://github.com/globeandmail/sophi-for-wordpress/pull/65)).
- Update default Collector URL helper text (props [@barryceelen](https://github.com/barryceelen) via [#56](https://github.com/globeandmail/sophi-for-wordpress/pull/56)).
- Set default environment according to `wp_get_environment_type()` (props [@barryceelen](https://github.com/barryceelen) via [#57](https://github.com/globeandmail/sophi-for-wordpress/pull/57)).

### Fixed
- Only load Snowplow analytics once Sophi plugin is configured (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter), [@barryceelen](https://github.com/barryceelen) via [#61](https://github.com/globeandmail/sophi-for-wordpress/pull/61)).
- VIP CLI dry-run mode defaults to `false`, corrects sync limiting, adds progress bar (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul), [@barryceelen](https://github.com/barryceelen) via [#66](https://github.com/globeandmail/sophi-for-wordpress/pull/66)).

## [1.0.2] - 2021-04-26
### Changed
- Bump `vipwpcs` from 2.2.0 to 2.3.0 and `php_codesniffer` from 3.5.8 to 3.6.0 (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul) via [#47](https://github.com/globeandmail/sophi-for-wordpress/pull/47)).

### Fixed
- WP-CLI command now supports both WordPress VIP and non-VIP environments (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#45](https://github.com/globeandmail/sophi-for-wordpress/pull/45)).

## [1.0.1] - 2021-04-23
### Added
- `noConfigFile` setting (props [@dinhtungdu](https://github.com/dinhtungdu) via [#48](https://github.com/globeandmail/sophi-for-wordpress/pull/48)).
- GitHub Actions to deploy releases and update readme/asset changes for WordPress.org (props [@jeffpaul](https://github.com/jeffpaul), [@dinhtungdu](https://github.com/dinhtungdu) via [#44](https://github.com/globeandmail/sophi-for-wordpress/pull/44)).

### Changed
- Renamed plugin from `Sophi for WordPress` to `Sophi` (props [@jeffpaul](https://github.com/jeffpaul) via [#49](https://github.com/globeandmail/sophi-for-wordpress/pull/49)).

### Fixed
- Minor fixes from WordPress VIP code scan (props [@dkotter](https://github.com/dkotter) via [#50](https://github.com/globeandmail/sophi-for-wordpress/pull/50)).

## [1.0.0] - 2021-04-14
- Initial public release! 🎉

[Unreleased]: https://github.com/globeandmail/sophi-for-wordpress/compare/trunk...develop
[1.0.3]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/globeandmail/sophi-for-wordpress/tree/3e714c0149a3b3e4a209c9d71b1510e43c42efcd