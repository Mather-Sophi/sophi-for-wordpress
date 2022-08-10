# Welcome to the Sophi for WordPress Plugin Developer Documentation

This resource is generated documentation on actions and filters found in the Sophi for WordPress plugin, as well as some helpful tutorials on how to utilize those hooks. Use the sidebar to browse and navigate.

If you're looking to contribute to or extend the Sophi for WordPress plugin, then the following sub-sections are things to be aware of in terms of how the plugin is architected.

### Dependencies

1. [Node >= 8.11 & NPM](https://www.npmjs.com/get-npm) - Build packages and 3rd party dependencies are managed through NPM, so you will need that installed globally.
2. [Webpack](https://webpack.js.org/) - Webpack is used to process the JavaScript, CSS, and other assets.
3. [Composer](https://getcomposer.org/) - Composer is used to manage PHP.

### NPM Commands

- `npm run test` (runs phpunit)
- `npm run start` (install dependencies)
- `npm run watch` (watch)
- `npm run build` (build all files)
- `npm run build-release` (build all files for release)
- `npm run dev` (build all files for development)
- `npm run lint-release` (install dependencies and run linting)
- `npm run lint-css` (lint CSS)
- `npm run lint-js` (lint JS)
- `npm run lint-php` (lint PHP)
- `npm run lint` (run all lints)
- `npm run format-js` (format JS using eslint)
- `npm run format` (alias for `npm run format-js`)
- `npm run test-a11y` (run accessibility tests)

### Composer Commands

- `composer lint` (lint PHP files)
- `composer lint-fix` (lint PHP files and automatically correct coding standard violations)

### WP-CLI Commands

### Sync content to Sophi Collector

`$ wp sophi sync [--post_types=<string>] [--limit=<number>] [--per_page=<number>] [--include=<number>]`

Sync all supported content to Sophi Collector, firing off update events for all of them.  The expected use case with the Sophi for WordPress plugin is that someone will install it on an existing site and instead of having to manually update each piece of content to ensure that it makes it to the Collector, they can run this script to handle that all at once.

#### Options

**`--post_types=<string>`**

Post types to be processed. Comma separated for passing multiple post types.

default: `false`
options:
- any post type name
- `false`

**`--limit=<number>`**

Limit the amount of posts to be synced.

default: `false`
options:
- `false`, no limit
- `N`, max number of posts to sync

**`--per_page=<number>`**

Number of posts to process each batch.

default: `false`
options:
- `false`, no limit
- `N`, max number of posts to sync each batch

**`--include=<number>`**

Post IDs to process. Comma separated for passing multiple item.

default: `false`
options:
- `false`, no limit
- `N`, Post IDs to sync

For more information about using Sophi for WordPress, please see the [Sophi website](https://sophi.io/).

To report an issue with Sophi for WordPress or contribute back to the project, please visit the [GitHub repository](https://github.com/globeandmail/sophi-for-wordpress/).

<a href="https://www.sophi.io/contact/" class="banner"><img src="https://raw.githubusercontent.com/globeandmail/sophi-for-wordpress/develop/.wordpress-org/banner-1544x500.png?token=AAVQAVOFCCNY7MWUHRZLYNTAOR6JI" width="850"></a>
