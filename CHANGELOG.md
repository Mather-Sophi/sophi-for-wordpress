# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.0.5] - 2021-10-28

### Changed
- Added new fields to be sent to the API: thumbnailImageUri, embeddedImagesCount, and keywords (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@dinhtungdu](https://github.com/dinhtungdu) via [#116](https://github.com/globeandmail/sophi-for-wordpress/pull/116)).
- Updated name convention for appId (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@dinhtungdu](https://github.com/dinhtungdu) via [#117](https://github.com/globeandmail/sophi-for-wordpress/pull/117)).
- Updated to send WordPress categories in sectionNames field (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia) via [#135](https://github.com/globeandmail/sophi-for-wordpress/pull/135)).
- Changed the tracking of the homepage on the Dashboard (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia) via [#139](https://github.com/globeandmail/sophi-for-wordpress/pull/139)).
- Updated the audience URL (props [@Rahmon](https://github.com/Rahmon), [@jeffpaul](https://github.com/jeffpaul) via [#143](https://github.com/globeandmail/sophi-for-wordpress/pull/143)).

### Fixed
- Issue with the tracking of the homepage on the frontend (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia) via [#139](https://github.com/globeandmail/sophi-for-wordpress/pull/139)).

### Security
- Update dependency `10up-toolkit` from 1.0.10 to 1.0.13 (props [@renovate](https://github.com/marketplace/renovate) via [#111](https://github.com/globeandmail/sophi-for-wordpress/pull/111), [#129](https://github.com/globeandmail/sophi-for-wordpress/pull/129), [#145](https://github.com/globeandmail/sophi-for-wordpress/pull/145)).
- Update dependency `phpunit/phpunit` from 8.5.18 to 8.5.21 (props [@renovate](https://github.com/marketplace/renovate) via [#114](https://github.com/globeandmail/sophi-for-wordpress/pull/114), [#132](https://github.com/globeandmail/sophi-for-wordpress/pull/132)).
- Update dependency `automattic/vipwpcs` from 2.3.2 to 2.3.3 (props [@renovate](https://github.com/marketplace/renovate) via [#133](https://github.com/globeandmail/sophi-for-wordpress/pull/133)).

## [1.0.4] - 2021-07-29
### Added
- Support for Yoast canonical URL (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter) via [#70](https://github.com/globeandmail/sophi-for-wordpress/pull/70), [#71](https://github.com/globeandmail/sophi-for-wordpress/pull/71), [#72](https://github.com/globeandmail/sophi-for-wordpress/pull/72)).
- Configure WhiteSource Bolt and Renovate integrations (props [@whitesource-bolt](https://github.com/marketplace/whitesource-bolt), [@renovate](https://github.com/marketplace/renovate) via [#72](https://github.com/globeandmail/sophi-for-wordpress/pull/72), [#97](https://github.com/globeandmail/sophi-for-wordpress/pull/97)).

### Changed
- Updated AMP and JS tracking data, changed post data type to post content type (props [@dinhtungdu](https://github.com/dinhtungdu) via [#71](https://github.com/globeandmail/sophi-for-wordpress/pull/71), [#72](https://github.com/globeandmail/sophi-for-wordpress/pull/72)).
- Update documentation, dependencies, and pin dependencies (props [@jeffpaul](https://github.com/jeffpaul), [@dinhtungdu](https://github.com/dinhtungdu), [@renovate](https://github.com/marketplace/renovate) via [#96](https://github.com/globeandmail/sophi-for-wordpress/pull/96), [#98](https://github.com/globeandmail/sophi-for-wordpress/pull/98), [#104](https://github.com/globeandmail/sophi-for-wordpress/pull/104)).

### Fixed
- Issue with empty `sectionNames` (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter) via [#69](https://github.com/globeandmail/sophi-for-wordpress/pull/69)).
- Set `jsonschema` to 2.0.0 (props [@dinhtungdu](https://github.com/dinhtungdu), [@dkotter](https://github.com/dkotter) via [#69](https://github.com/globeandmail/sophi-for-wordpress/pull/69)).
- Corrected variable name from `contentSize` to `size` (props [@dinhtungdu](https://github.com/dinhtungdu) via [#71](https://github.com/globeandmail/sophi-for-wordpress/pull/71)).
- Sending publish events when Yoast SEO is activated (props [@dinhtungdu](https://github.com/dinhtungdu) via [#107](https://github.com/globeandmail/sophi-for-wordpress/pull/107)).
- Only use public taxonomies to build post breadcrumb (props [@dinhtungdu](https://github.com/dinhtungdu) via [#109](https://github.com/globeandmail/sophi-for-wordpress/pull/109)).
- Update events via Quick Edit are now sent to Sophi when Yoast SEO is activated (props [@dinhtungdu](https://github.com/dinhtungdu) via [#110](https://github.com/globeandmail/sophi-for-wordpress/pull/110)).

### Security
- Update dependency `@10up/scripts` from 1.3.1 to 1.3.4 (props [@renovate](https://github.com/marketplace/renovate) via [#99](https://github.com/globeandmail/sophi-for-wordpress/pull/99)).
- Update dependency `automattic/vipwpcs` from 2.3.0 to 2.3.2 (props [@renovate](https://github.com/marketplace/renovate) via [#100](https://github.com/globeandmail/sophi-for-wordpress/pull/100)).
- Update dependency `phpunit/phpunit` from 8.5.15 to 8.5.18 (props [@renovate](https://github.com/marketplace/renovate) via [#101](https://github.com/globeandmail/sophi-for-wordpress/pull/101), [#108](https://github.com/globeandmail/sophi-for-wordpress/pull/108)).
- Update `actions/setup-node` action from v1 to v2 (props [@renovate](https://github.com/marketplace/renovate) via [#102](https://github.com/globeandmail/sophi-for-wordpress/pull/102)).
- Update dependency `10up-toolkit` from 1.0.9 to 1.0.10 (props [@renovate](https://github.com/marketplace/renovate) via [#111](https://github.com/globeandmail/sophi-for-wordpress/pull/111)).

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
[1.0.5]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/globeandmail/sophi-for-wordpress/tree/3e714c0149a3b3e4a209c9d71b1510e43c42efcd
