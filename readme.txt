=== Sophi for WordPress ===
Contributors:      10up
Tags:              Sophi, Site Automation, Collector, AI, Artifical Intelligence, ML, Machine Learning, Content Curation
Requires at least: 5.6
Tested up to:      5.6
Requires PHP:      7.4
Stable tag:        1.0.0
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
You can upload and install the [archived (zip) plugin](https://github.com/globeandmail/sophi-for-wordpress/archive/stable.zip) via the WordPress dashboard (`Plugins` > `Add New` -> `Upload Plugin`) or manually inside of the `wp-content/plugins` directory, and activate on the Plugins dashboard.

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

You can utilize the Site Automation Block, configure the Site Automation page and widget name, and adjust block content and meta settings to display Site Automation content on your site.

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

= Query integration =

The Site Automation block uses query integration under the hood. You can query for Sophi curated articles by passing `sophi_curated_page` and `sophi_curated_widget` to your WP_Query queries.

`
$the_query = new WP_Query( [
	'sophi_curated_page'   => 'page-name',
	'sophi_curated_widget' => 'widget-name',
] );
`

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

= 1.0.0 =
* Initial public release! 🎉
