=== Sophi ===
Contributors:      10up, sophidev
Tags:              Sophi, Site Automation, Collector, AI, Artifical Intelligence, ML, Machine Learning, Content Curation
Tested up to:      5.8
Stable tag:        1.0.9
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

WordPress VIP-compatible plugin for the Sophi.io Site Automation service.

== Description ==

[Sophi.io](https://www.sophi.io) is a suite of artificial intelligence (AI) tools developed by [The Globe and Mail](https://www.theglobeandmail.com/) to help content publishers make important strategic and tactical decisions. The tools range from automated content curation engines to predictive analytics and paywall solutions.

Sophi-driven pages outperform human curated pages by a significant margin and free up publisher resources so that they can focus on finding the next story that matters. Invisible, automated curation of your home and section pages to streamline your team’s workflow. Continuously optimize the performance characteristics of the site based on demand and your brand, so the content is always the right fit.

**Note that a [Sophi.io account](https://www.sophi.io/contact/) and related credentials are required in order to authorize and authenticate access within the plugin settings.**

== Highlights of Sophi.io service ==

* An automation platform capable of transforming your business.
* State of the art natural language processing to enable prediction and optimization.
* Tomorrow’s publishers rely on Sophi to better understand their audience and make sure every pixel is used to drive value efficiently and effectively.
* **Sophi Automation:** Automates your digital content curation to display your most valuable content in the most valuable places on your website, on social media and in newsletters.
* **Sophi Dive:** Uses your historical data to help content publishers and editors determine what content to produce more or less of and what to stop doing all together — which enables intelligent decision-making around resource allocation.
* **Sophi Now:** Equips you with analytics that provide real-time, tactical decision support that can tell you what content to promote more prominently, or less prominently; find out what’s over-performing and underperforming on a particular page; and find out why a particular article is doing well or not (for example, is it driving subscriptions, helping retain subscribers, getting traction on social media, search or newsletters, etc.) and where readers are coming from and going next on your website.
* **Sophi Next:** Flags a short list of articles that have just been published (but not promoted anywhere) that are likely to be your most valuable content going forward. This helps editors decide which headlines to place on a particular page — helping them find the hidden gems.
* **Sophi for Paywalls:** A fully dynamic, real-time paywall engine that understands both content and users and can optimize for several outcomes and offers simultaneously.

== Installation ==

*Note that if you are on the [WordPress VIP](https://wpvip.com/) platform that you will not be able to upload a plugin in the WordPress Admin so you will need to follow steps in 1b.*

1a Download plugin ZIP file and upload to your site.
You can upload and install the [plugin ZIP file](https://downloads.wordpress.org/plugin/sophi.zip) via the WordPress dashboard (`Plugins` > `Add New` -> `Upload Plugin`) or manually inside of the `wp-content/plugins` directory, and activate on the Plugins dashboard.

1b Download or Clone this repo, install dependencies, and build.
- `git clone https://github.com/globeandmail/sophi-for-wordpress.git && cd sophi-for-wordpress`
- `composer install && npm install && npm run build`

2 Activate Plugin
Click the `Activate` link after installing the plugin.

3 Configure plugin settings
Go to `Settings` > `Sophi` in the WordPress Admin and enter your Sophi Collector and Site Automation credentials supplied by the [Sophi.io](https://www.sophi.io/contact/) team.  Click `Save Changes` button to validate and save your credentials.

![Sophi.io Collector and Site Automation settings page.](https://github.com/globeandmail/sophi-for-wordpress/.wordpress-org/screenshot-1.png)

Once your credentials are validated and saved, your site is officially supercharged by the Sophi.io Site Automation service.  You also have access to Sophi for WordPress' powerful WP_Query parameters and custom Sophi Site Automation block, which enables you to utilize Site Automation to power the content curation on your site.

== Usage ==

There are two ways that Sophi Site Automation results can be included in a WordPress site, via a Site Automation block and a direct integration with WP_Query.  More details on each of these options are described below.

= Site Automation block =

You can utilize the Site Automation block by configuring the Site Automation page and widget names and adjusting block content and post meta settings to display Site Automation content on your site.

![Sophi Site Automation block.](https://github.com/globeandmail/sophi-for-wordpress/.wordpress-org/screenshot-2.png)

The Site Automation block comes with a barebone HTML output and styling. It's made to be filtered using `sophi_site_automation_block_output`.

`
add_filter(
	'sophi_site_automation_block_output',
	function( $output, $curated_posts, $attributes, $content, $block ) {
		// ...
		return $new_output;
	},
	10,
	5
);
`

The Site Automation block will automatically add a `data-sophi-feature=<widget_name>` attribute for the rendered HTML content so Sophi can understand what content section is rendered on your site.

= Query integration =

The Site Automation block uses query integration under the hood. You can query for Sophi curated articles by passing `sophi_curated_page` and `sophi_curated_widget` to your WP_Query queries.

`
$the_query = new WP_Query( [
	'sophi_curated_page'   => 'page-name',
	'sophi_curated_widget' => 'widget-name',
] );
`

Note that you need to add `data-sophi-feature=<widget_name>` to the wrapper div of your post for Sophi to capture your widget better.

`
<div class="posts-wrapper" data-sophi-feature="trending">
	<!-- Post lists -->
</div>
`

= Post content type =

By default, Sophi for WordPress uses post format as the content type. This plugin uses `content_type` internally to distinguish between WordPress post type and Sophi type.

Sophi accepts 4 types: article, video, audio, and image. The default type is `article`. Any other types that are not included above will be treated as `article`.

If you're not using post format for content type, you can utilize `sophi_post_content_type` to set the content type.

`
add_filter(
	'sophi_post_content_type',
	function( $type, $post ) {
		// You logic here.

		return $new_type;
	},
	10,
	2
);
`

= Canonical URL =

Sophi for WordPress uses `wp_get_canonical_url` function introduced in WordPress 4.6 to get the canonical URL for given post. The plugin compares this canonical URL to post permalink to set the `isCanonical` attribute.

WordPress SEO (Yoast) canonical is supported out of the box. For other SEO plugins and custom implementations, [`get_canonical_url`](https://developer.wordpress.org/reference/functions/wp_get_canonical_url/) filter can be used to change the canonical URL.

== Documentation ==

Sophi for WordPress has an in-depth documentation site that details the available actions and filters found within the plugin. [Visit the hook docs ☞](https://globeandmail.github.io/sophi-for-wordpress/)

== Developers ==

If you're looking to contribute to or extend the Sophi for WordPress plugin, then the following sub-sections are things to be aware of in terms of how the plugin is architected.

= Dependencies =

1. [Node >= 8.11 & NPM](https://www.npmjs.com/get-npm) - Build packages and 3rd party dependencies are managed through NPM, so you will need that installed globally.
2. [Webpack](https://webpack.js.org/) - Webpack is used to process the JavaScript, CSS, and other assets.
3. [Composer](https://getcomposer.org/) - Composer is used to manage PHP.

= NPM Commands =

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

= Composer Commands =

- `composer lint` (lint PHP files)
- `composer lint-fix` (lint PHP files and automatically correct coding standard violations)

= WP-CLI Commands =

**Sync content to Sophi Collector**

`$ wp sophi sync [--post_types=<string>] [--limit=<number>] [--per_page=<number>] [--include=<number>]`

Sync all supported content to Sophi Collector, firing off update events for all of them.  The expected use case with the Sophi for WordPress plugin is that someone will install it on an existing site and instead of having to manually update each piece of content to ensure that it makes it to the Collector, they can run this script to handle that all at once.

*Options*

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

== Frequently Asked Questions ==

= How was Sophi created? =

The award-winning Sophi suite of solutions was developed by The Globe and Mail to help content publishers make important strategic and tactical decisions that transform their business. It is a suite of ML and NLP-powered tools that includes [Sophi Automation](https://www.sophi.io/automation) for content curation, Sophi for Paywalls, and Sophi Analytics – a decision-support system for publishers.

= How well does Sophi Automation perform? =

The Globe and Mail saw a dramatic improvement in the business metrics that matter:
* Click-through rates were up 17% from the homepage.
* The subscriber acquisition rate increased by more than 10%.
* Not a single reader has complained or asked if a computer is curating the website.

You can read more details in [The Globe and Mail case study](https://www.sophi.io/automation/insights/case-studies/case-study-the-globe-and-mail-and-sophi-automation/).

= What Privacy and Terms govern Sophi.io? =

The same [Privacy & Terms that govern The Globe and Mail](https://www.theglobeandmail.com/privacy-terms/) cover the Sophi.io service.

== Screenshots ==

1. Sophi.io Collector and Site Automation settings page.
2. Sophi Site Automation block.

== Changelog ==

= 1.0.8 =
* **Changed:** Updated `auth_url` and `audience` parameters to get respective Staging and Development environment access tokens (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia)).
* **Fixed:** Issue where multiple Sophi blocks on the same page had duplicated content (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@jeffpaul](https://github.com/jeffpaul), [@dinhtungdu](https://github.com/dinhtungdu)).
* **Fixed:** Testing environment deploy process (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia)).
* **Security:** update dependency `prop-types` from 15.7.2 to 15.8.0 (props [@renovate](https://github.com/marketplace/renovate)).

= 1.0.7 =
* **Changed:** Sophi Auth URL value used for Production, Staging, and Development (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@barryceelen](https://github.com/barryceelen), [@amckie](https://github.com/amckie)).
* **Changed:** Sophi Site Automation API URL structure (props [@rahmohn](https://profiles.wordpress.org/rahmohn/)).
* **Changed:** Sophi Audience URL used in authorization (props [@rahmohn](https://profiles.wordpress.org/rahmohn/)).

= 1.0.6 =
Note: this was a hotfix release to fix an issue with deploys to WordPress.org.

= 1.0.5 =
* **Added:** New content fields sent to Sophi `thumbnailImageUri`, `embeddedImagesCount`, and `keywords` (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@felipeelia](https://profiles.wordpress.org/felipeelia), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Changed:** Updated name convention for `appId` (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@felipeelia](https://profiles.wordpress.org/felipeelia), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Changed:** Send WordPress Categories to Sophi in `sectionNames` field (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul), [@felipeelia](https://profiles.wordpress.org/felipeelia), [@tott](https://profiles.wordpress.org/tott)).
* **Changed:** Updated JS tracking of the homepage from `article` to `section` content type (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@felipeelia](https://profiles.wordpress.org/felipeelia), [@jeffpaul](https://profiles.wordpress.org/jeffpaul)).
* **Changed:** Updated `audience` URL (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Changed:** Bump WordPress version "tested up to" 5.8 (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Changed:** Documentation (props [@dkotter](https://profiles.wordpress.org/dkotter)).
* **Fixed:** Stopped sending homepage (aka "page on front" or "page for posts") content updates to Sophi (props [@rahmohn](https://profiles.wordpress.org/rahmohn/), [@felipeelia](https://profiles.wordpress.org/felipeelia), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Security:** Update dependency `10up-toolkit` from 1.0.9 to 1.0.13 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.18 to 8.5.21 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update dependency `automattic/vipwpcs` from 2.3.2 to 2.3.3 (props [@renovate](https://github.com/marketplace/renovate)).

= 1.0.4 =
* **Added:** Support for Yoast canonical URL (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Added:** Configure WhiteSource Bolt and Renovate integrations (props [@whitesource-bolt](https://github.com/marketplace/whitesource-bolt), [@renovate](https://github.com/marketplace/renovate)).
* **Changed:** Updated AMP and JS tracking data, changed post data type to post content type (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Changed:** Update documentation, dependencies, and pin dependencies (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@renovate](https://github.com/marketplace/renovate)).
* **Fixed:** Issue with empty `sectionNames` (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Fixed:** Set `jsonschema` to 2.0.0 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Fixed:** Corrected variable name from `contentSize` to `size` (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Fixed:** Sending publish events when Yoast SEO is activated (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Fixed:** Only use public taxonomies to build post breadcrumb (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Fixed:** Update events via Quick Edit are now sent to Sophi when Yoast SEO is activated (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Security:** Update dependency `@10up/scripts` from 1.3.1 to 1.3.4 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update dependency `automattic/vipwpcs` from 2.3.0 to 2.3.2 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.15 to 8.5.18 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update `actions/setup-node` action from v1 to v2 (props [@renovate](https://github.com/marketplace/renovate)).
* **Security:** Update dependency `10up-toolkit` from 1.0.9 to 1.0.10 (props [@renovate](https://github.com/marketplace/renovate)).

= 1.0.3 =
* **Added:** Data attributes to Site Automation widgets/blocks (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Changed:** Revisions to tracking data collected to improve Site Automation results (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Changed:** Update default Collector URL helper text (props [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Changed:** Set default environment according to `wp_get_environment_type()` (props [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Fixed:** Only load Snowplow analytics once Sophi plugin is configured (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/), [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Fixed:** VIP CLI dry-run mode defaults to `false`, corrects sync limiting, adds progress bar (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@barryceelen](https://profiles.wordpress.org/barryceelen/)).

= 1.0.2 =
* **Changed:** Bump `vipwpcs` from 2.2.0 to 2.3.0 and `php_codesniffer` from 3.5.8 to 3.6.0 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Fixed:** WP-CLI command now supports both WordPress VIP and non-VIP environments (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).

= 1.0.1 =
* **Added:** `noConfigFile` setting (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Added:** GitHub Actions to deploy releases and update readme/asset changes for WordPress.org (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Changed:** Renamed plugin from `Sophi for WordPress` to `Sophi` (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Fixed:** Minor fixes from WordPress VIP code scan (props [@dkotter](https://profiles.wordpress.org/dkotter/)).

= 1.0.0 =
* Initial public release! 🎉
