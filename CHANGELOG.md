# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.3.0] - 2022-10-28
**Note that this version bumps the minimum WordPress version from 5.6 to 6.0 and adds an integration with the Sophi Override feature.**

### Added
- Override feature to the Sophi Site Automation Block (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333), [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
- [GET] `/wp-json/sophi/v1/site-automation` WP REST endpoint to Get Posts from Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).
- [POST] `/wp-json/sophi/v1/site-automation-override` WP REST endpoint to Override the Posts for Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).
- `api_version` to the block JSON files (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
- Display message when no posts are returned from Sophi Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
- Functionality to remove old error messages when posts are found (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
- Tracker Address option on plugin settings page (props [@faisal-alvi](https://github.com/faisal-alvi), [@YMufleh](https://github.com/YMufleh), [@jeffpaul](https://github.com/jeffpaul) via [#342](https://github.com/globeandmail/sophi-for-wordpress/pull/342)).
- New tutorial for modifying tracking data on documentation site (props [@iamdharmesh](https://github.com/iamdharmesh), [@YMufleh](https://github.com/YMufleh), [@dkotter](https://github.com/dkotter) via [#327](https://github.com/globeandmail/sophi-for-wordpress/pull/327)).

### Changed
- Bump minimum WordPress version from 5.6 to 6.0 (props [@faisal-alvi](https://github.com/faisal-alvi) via [#346](https://github.com/globeandmail/sophi-for-wordpress/pull/346)).
- Sophi Site Automation Block from a server-side-rendered custom block to a nested block (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333), [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
- Replaced hardcoded tracker endpoint value with the new Tracker Address setting (props [@faisal-alvi](https://github.com/faisal-alvi), [@YMufleh](https://github.com/YMufleh), [@jeffpaul](https://github.com/jeffpaul) via [#342](https://github.com/globeandmail/sophi-for-wordpress/pull/342)).
- Developer Documentation updates (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@faisal-alvi](https://github.com/faisal-alvi) via [#332](https://github.com/globeandmail/sophi-for-wordpress/pull/332), [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).

### Fixed
- Prevent duplicate CMS publish/update event tracking (props [@cadic](https://github.com/cadic), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#335](https://github.com/globeandmail/sophi-for-wordpress/pull/335)).

### Security
- Added latest PHPCompatibility checks for PHP 8.0 (props [@Sidsector9](https://github.com/Sidsector9) via [#331](https://github.com/globeandmail/sophi-for-wordpress/pull/331)).
- Update dependency `phpunit/phpunit` from 8.5.29 to 9.5.25 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#329](https://github.com/globeandmail/sophi-for-wordpress/pull/329), [#337](https://github.com/globeandmail/sophi-for-wordpress/pull/337), [#339](https://github.com/globeandmail/sophi-for-wordpress/pull/339)).
- Update dependency `10up-toolkit` from 4.1.2 to 4.3.0 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#330](https://github.com/globeandmail/sophi-for-wordpress/pull/330), [#340](https://github.com/globeandmail/sophi-for-wordpress/pull/340)).
- Update `actions/checkout` action from v2 to v3 and v3.0.2 to v3.1.0 (props [@renovate](https://github.com/apps/renovate) via [#334](https://github.com/globeandmail/sophi-for-wordpress/pull/334)).

## [1.2.1] - 2022-08-30
### Changed
- Bump `content_update` schema from 2-0-3 to 2-0-5 (props [@jeffpaul](https://github.com/jeffpaul), [@vjayaseelan90](https://github.com/vjayaseelan90), [@YMufleh](https://github.com/YMufleh) via [#323](https://github.com/globeandmail/sophi-for-wordpress/pull/323)).

### Security
- Update dependency `jsdoc` from 3.6.10 to 3.6.11 (props [@renovate](https://github.com/apps/renovate) via [#311](https://github.com/globeandmail/sophi-for-wordpress/pull/311)).
- Bump `terser` from 5.13.1 to 5.14.2 (props [@dependabot](https://github.com/apps/dependabot) via [#312](https://github.com/globeandmail/sophi-for-wordpress/pull/312)).
- Update dependency `phpunit/phpunit` from 8.5.27 to 8.5.29 (props [@renovate](https://github.com/apps/renovate) via [#316](https://github.com/globeandmail/sophi-for-wordpress/pull/316)).

## [1.2.0] - 2022-08-15
### Added
- New Curator API (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#310](https://github.com/globeandmail/sophi-for-wordpress/pull/310).
- Documentation on default handling of Sophi API empty array response (props [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez) via [#307](https://github.com/globeandmail/sophi-for-wordpress/pull/307).
- New tutorial sections added to the documentation site (props [@dkotter](https://github.com/dkotter), [@YMufleh](https://github.com/YMufleh) via [#318](https://github.com/globeandmail/sophi-for-wordpress/pull/318).

### Changed
- Instead of storing responses from Sophi in an option, store that in a new CPT, `sophi-response` (props [@oscarssanchez](https://github.com/oscarssanchez), [@cadic](https://github.com/cadic), [@felipeelia](https://github.com/felipeelia) via [#298](https://github.com/globeandmail/sophi-for-wordpress/pull/298).
- Replaces `WP_Query` with `get_posts` in details about the Sophi Automation block (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#300](https://github.com/globeandmail/sophi-for-wordpress/pull/300).

### Removed
- Dependency on Auth0 and replaced with the new Curator API (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#310](https://github.com/globeandmail/sophi-for-wordpress/pull/310).

### Fixed
- Ensure we are properly getting cached responses (props [@oscarssanchez](https://github.com/oscarssanchez), [@Sidsector9](https://github.com/Sidsector9), [@felipeelia](https://github.com/felipeelia) via [#305](https://github.com/globeandmail/sophi-for-wordpress/pull/305).

### Security
- Added PHP compatibility checker (props [@Sidsector9](https://github.com/Sidsector9), [@dkotter](https://github.com/dkotter) via [#317](https://github.com/globeandmail/sophi-for-wordpress/pull/317).
- Update dependency `10up-toolkit` from v4.0.0 to v4.1.2 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9) via [#299](https://github.com/globeandmail/sophi-for-wordpress/pull/299), [#303](https://github.com/globeandmail/sophi-for-wordpress/pull/303).
- Update dependency `phpunit/phpunit` from v8.5.26 to v8.5.27 (props [@renovate](https://github.com/apps/renovate) via [#304](https://github.com/globeandmail/sophi-for-wordpress/pull/304).

## [1.1.3] - 2022-05-27
### Added
- WordPress and Sophi plugin versions to Javascript and AMP Tracking (props [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul), [@YMufleh](https://github.com/YMufleh) via [#287](https://github.com/globeandmail/sophi-for-wordpress/pull/287)).
- Documentation for `sophi_cache_duration` filter (props [@Sidsector9](https://github.com/Sidsector9), [@barryceelen](https://github.com/barryceelen), [@jeffpaul](https://github.com/jeffpaul) via [#285](https://github.com/globeandmail/sophi-for-wordpress/pull/285)).
- Unit tests (props [@cadic](https://github.com/cadic) via [#291](https://github.com/globeandmail/sophi-for-wordpress/pull/291)).

### Changed
- Reverted response saving back to options table from post meta field (props [@oscarssanchez](https://github.com/oscarssanchez) via [#280](https://github.com/globeandmail/sophi-for-wordpress/pull/280)).
- Replace deprecated `block_categories` filter with `block_categories_all` (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#292](https://github.com/globeandmail/sophi-for-wordpress/pull/292)).
- Bump WordPress "tested up to" version 6.0 (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#293](https://github.com/globeandmail/sophi-for-wordpress/pull/293)).

### Fixed
- Fatal error on tracking a post with empty content (props [@cadic](https://github.com/cadic) via [#289](https://github.com/globeandmail/sophi-for-wordpress/pull/289)).
- Warning shown due to access of undefined array (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#292](https://github.com/globeandmail/sophi-for-wordpress/pull/292)).
- Warning on Widget page via check before accessing property (props [@Sidsector9](https://github.com/Sidsector9) via [#295](https://github.com/globeandmail/sophi-for-wordpress/pull/295)).
- PHPCS check and linting issues (props [@cadic](https://github.com/cadic) via [#291](https://github.com/globeandmail/sophi-for-wordpress/pull/291)).

## [1.1.2] - 2022-05-18
### Fixed
- Incorrect posts rendered due to wrong transient name (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic) via [#282](https://github.com/globeandmail/sophi-for-wordpress/pull/282)).

## [1.1.1] - 2022-05-17
### Added
- `sophi_bypass_curated_posts_cache` filter to bypess curated posts cache (props [@cadic](https://github.com/cadic) via [#276](https://github.com/globeandmail/sophi-for-wordpress/pull/276)).
- Send all Categories in new `allSections` field to Sophi (props [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul), [@tott](https://github.com/tott) via [#275](https://github.com/globeandmail/sophi-for-wordpress/pull/275)).

### Changed
- `$result` is used instead of `$request` to store return value of the `sophi_request_result` filter (props [@faisal-alvi](https://github.com/faisal-alvi), [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic) via [#277](https://github.com/globeandmail/sophi-for-wordpress/pull/277)).
- Suppress Emitter debug output and writing logs (props [@cadic](https://github.com/cadic) via [#272](https://github.com/globeandmail/sophi-for-wordpress/pull/272), [#274](https://github.com/globeandmail/sophi-for-wordpress/pull/274)).

### Fixed
- [Hook Docs](https://globeandmail.github.io/sophi-for-wordpress/) deployment GitHub Action and included filter docs (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic) via [#266](https://github.com/globeandmail/sophi-for-wordpress/pull/266), [#268](https://github.com/globeandmail/sophi-for-wordpress/pull/268)).

## [1.1.0] - 2022-05-06
### Added
- Filter `sophi_request_args` filters arguments used in Sophi HTTP request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257)).
- Filter `sophi_request_result` filters a Sophi HTTP request immediately after the response is received (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#261](https://github.com/globeandmail/sophi-for-wordpress/pull/261)).
- Filter `sophi_cms_tracking_request_data` filters the data used in Sophi track event request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#263](https://github.com/globeandmail/sophi-for-wordpress/pull/263)).
- Action `sophi_cms_tracking_result` fires after tracker sends the request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#263](https://github.com/globeandmail/sophi-for-wordpress/pull/263)).
- Filter `sophi_tracker_emitter_debug` allows to enable debug mode in the tracking emitter (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257)).
- Documentation on rational for block vs. custom query integration uses and `WP_Query` caveat on WordPress VIP (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez) via [#242](https://github.com/globeandmail/sophi-for-wordpress/pull/242), [#249](https://github.com/globeandmail/sophi-for-wordpress/pull/249), [#251](https://github.com/globeandmail/sophi-for-wordpress/pull/251)).

### Changed
- Update response saving from options table to post meta field (props [@Rahmon](https://github.com/Rahmon), [@oscarssanchez](https://github.com/oscarssanchez) via [#253](https://github.com/globeandmail/sophi-for-wordpress/pull/253)).
- Bump WordPress "tested up to" version 5.9 (props [@jeffpaul](https://github.com/jeffpaul) via [#248](https://github.com/globeandmail/sophi-for-wordpress/pull/248)).
- Updated hookdocs site (now [Developer Docs](https://globeandmail.github.io/sophi-for-wordpress/)) to properly run on release and migrate in docs from readme (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#256](https://github.com/globeandmail/sophi-for-wordpress/pull/256)).

### Fixed
- Check if `$terms` is an array before using `count()` within `get_the_terms()` (props [@barryceelen](https://github.com/barryceelen), [@Sidsector9](https://github.com/Sidsector9) via [#215](https://github.com/globeandmail/sophi-for-wordpress/pull/215)).
- Update `track_event` arguments list inside `sync` WP-CLI command (props [@cadic](https://github.com/cadic), [@Sidsector9](https://github.com/Sidsector9) via [#250](https://github.com/globeandmail/sophi-for-wordpress/pull/250)).

### Security
- Update `actions/checkout` action to from v2.4.0 to v3.0.2 (props [@renovate](https://github.com/apps/renovate), [@iamdharmesh](https://github.com/iamdharmesh) via [#240](https://github.com/globeandmail/sophi-for-wordpress/pull/240), [#241](https://github.com/globeandmail/sophi-for-wordpress/pull/241), [#243](https://github.com/globeandmail/sophi-for-wordpress/pull/243)).
- Update `actions/setup-node` action from v1 to v3 (props [@renovate](https://github.com/apps/renovate), [@iamdharmesh](https://github.com/iamdharmesh) via [#244](https://github.com/globeandmail/sophi-for-wordpress/pull/244)).
- Update dependency `10up-toolkit` from 3.1.2 to 4.0.0 (props [@renovate](https://github.com/apps/renovate), [@cadic](https://github.com/cadic) via [#264](https://github.com/globeandmail/sophi-for-wordpress/pull/264)).

## [1.0.13] - 2022-04-20
### Changed
- `keywords` data type from string to array (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez), [@YMufleh](https://github.com/YMufleh) via [#233](https://github.com/globeandmail/sophi-for-wordpress/pull/233)).

### Security
- Bump `node-forge` from 1.2.1 to 1.3.0 (props [@dependabot](https://github.com/apps/dependabot) via [#228](https://github.com/globeandmail/sophi-for-wordpress/pull/228)).
- Bump `minimist` from 1.2.5 to 1.2.6 (props [@dependabot](https://github.com/apps/dependabot) via [#229](https://github.com/globeandmail/sophi-for-wordpress/pull/229)).
- Update dependency `10up-toolkit` from 3.0.3 to 3.1.2 (props [@renovate](https://github.com/apps/renovate) via [#230](https://github.com/globeandmail/sophi-for-wordpress/pull/230), [#234](https://github.com/globeandmail/sophi-for-wordpress/pull/234)).
- Update dependency `phpunit/phpunit` from 8.5.25 to 8.5.26 (props [@renovate](https://github.com/apps/renovate) via [#231](https://github.com/globeandmail/sophi-for-wordpress/pull/231)).
- Bump `async` from 2.6.3 to 2.6.4 (props [@dependabot](https://github.com/apps/dependabot) via [#235](https://github.com/globeandmail/sophi-for-wordpress/pull/235)).

## [1.0.12] - 2022-03-23
### Fixed
- PHP fatal error when duplicating posts across sites (props [@YMufleh](https://github.com/YMufleh) via [#218](https://github.com/globeandmail/sophi-for-wordpress/pull/218)).

### Security
- Update dependency `phpunit/phpunit` from 8.5.23 to 8.5.25 (props [@renovate](https://github.com/apps/renovate) via [#214](https://github.com/globeandmail/sophi-for-wordpress/pull/214), [#220](https://github.com/globeandmail/sophi-for-wordpress/pull/220)).
- Update dependency `10up-toolkit` from 3.0.2 to 3.0.3 (props [@renovate](https://github.com/apps/renovate) via [#217](https://github.com/globeandmail/sophi-for-wordpress/pull/217)).

## [1.0.11] - 2022-03-03
### Changed
- Update `package-lock.json` (props [@jeffpaul](https://github.com/jeffpaul) via [#197](https://github.com/globeandmail/sophi-for-wordpress/pull/197)).

### Security
- Update `actions/setup-node` action from v2 to v3 (props [@renovate](https://github.com/apps/renovate) via [#199](https://github.com/globeandmail/sophi-for-wordpress/pull/199)).
- Update `actions/checkout` action from v2 to v3 (props [@renovate](https://github.com/apps/renovate) via [#202](https://github.com/globeandmail/sophi-for-wordpress/pull/202)).
- Update dependency `10up-toolkit` from 2.1.1 to 3.0.2 (props [@renovate](https://github.com/apps/renovate) via [#203](https://github.com/globeandmail/sophi-for-wordpress/pull/203), [#210](https://github.com/globeandmail/sophi-for-wordpress/pull/210)).

## [1.0.10] - 2022-02-28
### Changed
- Sets the default timeout of the first Sophi request and the cron request to 3 seconds (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia), [@tott](https://github.com/tott) via [#198](https://github.com/globeandmail/sophi-for-wordpress/pull/198)).

### Security
- Update dependency `snowplow/snowplow-tracker` from 0.4.0 to 0.5.0 (props [@renovate](https://github.com/apps/renovate) via [#186](https://github.com/globeandmail/sophi-for-wordpress/pull/186)).

## [1.0.9] - 2022-02-18
### Added
- `hostname` and `path` fields to schema (props [@Rahmon](https://github.com/Rahmon), [@dinhtungdu](https://github.com/dinhtungdu) via [#164](https://github.com/globeandmail/sophi-for-wordpress/pull/164)).
- `sophi_curated_post_list` filter to modify the Sophi API response to allow manual posts to be injected previous to returning the filterable value from `posts_pre_query` (e.g., via a fallback method to inject posts that would be a good fit) (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia) via [#183](https://github.com/globeandmail/sophi-for-wordpress/pull/183)).

### Fixed
- Sophi API empty Post ID array response changed from using `WP_Query` default results to a "no results" response (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia) via [#183](https://github.com/globeandmail/sophi-for-wordpress/pull/183)).

### Security
- Update dependency `phpunit/phpunit` from 8.5.21 to 8.5.23 (props [@renovate](https://github.com/apps/renovate) via [#160](https://github.com/globeandmail/sophi-for-wordpress/pull/160)).
- Update dependency `prop-types` from 15.8.0 to 15.8.1 (props [@renovate](https://github.com/apps/renovate) via [#161](https://github.com/globeandmail/sophi-for-wordpress/pull/161)).
- Update dependency `10up-toolkit` from `1.0.13` to `2.1.1` (props [@renovate](https://github.com/apps/renovate), [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul) via [#169](https://github.com/globeandmail/sophi-for-wordpress/pull/169), [#189](https://github.com/globeandmail/sophi-for-wordpress/pull/189)).
- Update dependency `marked` from 2.1.3 to 4.0.10 (props [@renovate](https://github.com/apps/renovate) via [#176](https://github.com/globeandmail/sophi-for-wordpress/pull/176)).
- Update dependency `jsdoc` from 3.6.8 to 3.6.10 (props [@renovate](https://github.com/apps/renovate) via [#177](https://github.com/globeandmail/sophi-for-wordpress/pull/177)).
- Update dependency `dealerdirect/phpcodesniffer-composer-installer` from 0.7.1 to 0.7.2 (props [@renovate](https://github.com/apps/renovate) via [#182](https://github.com/globeandmail/sophi-for-wordpress/pull/182)).

## [1.0.8] - 2021-12-23
### Changed
- Updated `auth_url` and `audience` parameters to get respective Staging and Development environment access tokens (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia) via [#152](https://github.com/globeandmail/sophi-for-wordpress/pull/152)).

### Fixed
- Issue where multiple Sophi blocks on the same page had duplicated content (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@jeffpaul](https://github.com/jeffpaul), [@dinhtungdu](https://github.com/dinhtungdu) via [#157](https://github.com/globeandmail/sophi-for-wordpress/pull/157)).
- Testing environment deploy process (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia) via [#155](https://github.com/globeandmail/sophi-for-wordpress/pull/155)).

### Security
- Update dependency `prop-types` from 15.7.2 to 15.8.0 (props [@renovate](https://github.com/marketplace/renovate) via [#158](https://github.com/globeandmail/sophi-for-wordpress/pull/158)).

## [1.0.7] - 2021-10-29
### Changed
- Sophi Auth URL value used for Production, Staging, and Development (props [@jeffpaul](https://github.com/jeffpaul), [@barryceelen](https://github.com/barryceelen), [@amckie](https://github.com/amckie) via [#148](https://github.com/globeandmail/sophi-for-wordpress/pull/148)).
- Sophi Site Automation API URL structure (props [@Rahmon](https://github.com/Rahmon) via [#149](https://github.com/globeandmail/sophi-for-wordpress/pull/149)).
- Sophi Audience URL used in authorization (props [@Rahmon](https://github.com/Rahmon) via [#150](https://github.com/globeandmail/sophi-for-wordpress/pull/150)).

## [1.0.6] - 2021-10-27
**Note: this was a hotfix release to fix an issue with deploys to WordPress.org.**

## [1.0.5] - 2021-10-27
### Added
- New content fields sent to Sophi `thumbnailImageUri`, `embeddedImagesCount`, and `keywords` (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@dinhtungdu](https://github.com/dinhtungdu) via [#116](https://github.com/globeandmail/sophi-for-wordpress/pull/116)).

### Changed
- Updated name convention for `appId` (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@dinhtungdu](https://github.com/dinhtungdu) via [#117](https://github.com/globeandmail/sophi-for-wordpress/pull/117)).
- Send WordPress Categories to Sophi in `sectionNames` field (props [@Rahmon](https://github.com/Rahmon), [@jeffpaul](https://github.com/jeffpaul), [@felipeelia](https://github.com/felipeelia), [@tott](https://github.com/tott) via [#135](https://github.com/globeandmail/sophi-for-wordpress/pull/135), [#138](https://github.com/globeandmail/sophi-for-wordpress/pull/138), [#140](https://github.com/globeandmail/sophi-for-wordpress/pull/140), [#142](https://github.com/globeandmail/sophi-for-wordpress/pull/142), [#144](https://github.com/globeandmail/sophi-for-wordpress/pull/144)).
- Updated JS tracking of the homepage from `article` to `section` content type (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@jeffpaul](https://github.com/jeffpaul) via [#139](https://github.com/globeandmail/sophi-for-wordpress/pull/139), [#140](https://github.com/globeandmail/sophi-for-wordpress/pull/140), [#142](https://github.com/globeandmail/sophi-for-wordpress/pull/142)).
- Updated `audience` URL (props [@Rahmon](https://github.com/Rahmon), [@jeffpaul](https://github.com/jeffpaul) via [#143](https://github.com/globeandmail/sophi-for-wordpress/pull/143)).
- Bump WordPress version "tested up to" 5.8 (props [@Rahmon](https://github.com/Rahmon), [@jeffpaul](https://github.com/jeffpaul) via [#146](https://github.com/globeandmail/sophi-for-wordpress/pull/146)).
- Documentation (props [@dkotter](https://github.com/dkotter) via [#113](https://github.com/globeandmail/sophi-for-wordpress/pull/113)).

### Fixed
- Stopped sending homepage (aka "page on front" or "page for posts") content updates to Sophi (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@jeffpaul](https://github.com/jeffpaul) via [#139](https://github.com/globeandmail/sophi-for-wordpress/pull/139), [#140](https://github.com/globeandmail/sophi-for-wordpress/pull/140), [#142](https://github.com/globeandmail/sophi-for-wordpress/pull/142)).

### Security
- Update dependency `10up-toolkit` from 1.0.9 to 1.0.13 (props [@renovate](https://github.com/marketplace/renovate) via [#111](https://github.com/globeandmail/sophi-for-wordpress/pull/111), [#115](https://github.com/globeandmail/sophi-for-wordpress/pull/115), [#129](https://github.com/globeandmail/sophi-for-wordpress/pull/129), [#145](https://github.com/globeandmail/sophi-for-wordpress/pull/145)).
- Update dependency `phpunit/phpunit` from 8.5.18 to 8.5.21 (props [@renovate](https://github.com/marketplace/renovate) via [#114](https://github.com/globeandmail/sophi-for-wordpress/pull/114), [#120](https://github.com/globeandmail/sophi-for-wordpress/pull/120), [#132](https://github.com/globeandmail/sophi-for-wordpress/pull/132)).
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
[1.3.0]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.1.3...1.2.0
[1.1.3]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.13...1.1.0
[1.0.13]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.12...1.0.13
[1.0.12]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.11...1.0.12
[1.0.11]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.10...1.0.11
[1.0.10]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.9...1.0.10
[1.0.9]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.8...1.0.9
[1.0.8]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.7...1.0.8
[1.0.7]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/globeandmail/sophi-for-wordpress/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/globeandmail/sophi-for-wordpress/tree/3e714c0149a3b3e4a209c9d71b1510e43c42efcd
