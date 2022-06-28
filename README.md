<img alt="Sophi.io icon" src="https://github.com/globeandmail/sophi-for-wordpress/blob/trunk/.wordpress-org/icon.svg" height="45" width="45" align="left">

# Sophi for WordPress

> WordPress VIP-compatible plugin for the Sophi.io Site Automation service.

[![Support Level](https://img.shields.io/badge/support-active-green.svg)](#support-level) [![Release Version](https://img.shields.io/github/release/globeandmail/sophi-for-wordpress.svg)](https://github.com/globeandmail/sophi-for-wordpress/releases/latest) ![WordPress tested up to version](https://img.shields.io/wordpress/plugin/tested/sophi?color=blue&label=WordPress&logo=WordPress) [![GPL-2.0-or-later License](https://img.shields.io/github/license/globeandmail/sophi-for-wordpress.svg)](https://github.com/globeandmail/sophi-for-wordpress/blob/trunk/LICENSE.md)

[![Developer Docs](https://github.com/globeandmail/sophi-for-wordpress/actions/workflows/build-docs.yml/badge.svg?branch=trunk)](https://globeandmail.github.io/sophi-for-wordpress/) [![PHPUnit Testing](https://github.com/globeandmail/sophi-for-wordpress/actions/workflows/test.yml/badge.svg)](https://github.com/globeandmail/sophi-for-wordpress/actions/workflows/test.yml) [![PHPCS Linting](https://github.com/globeandmail/sophi-for-wordpress/actions/workflows/lint.yml/badge.svg)](https://github.com/globeandmail/sophi-for-wordpress/actions/workflows/lint.yml)

## Table of Contents
* [Overview](#overview)
* [Highlights](#highlights)
* [Requirements](#requirements)
* [Installation](#installation)
* [Using the plugin](#usage)
  * [Site Automation block](#site-automation-block)
  * [Query integration](#query-integration)
* [Documentation](#documentation)
* [FAQs](#frequently-asked-questions)
* [Changelog](#changelog)
* [Contributing](#contributing)

## Overview

[Sophi.io](https://www.sophi.io) is a suite of artificial intelligence (AI) tools developed by [The Globe and Mail](https://www.theglobeandmail.com/) to help content publishers make important strategic and tactical decisions. The tools range from automated content curation engines to predictive analytics and paywall solutions.

Sophi-driven pages outperform human curated pages by a significant margin and free up publisher resources so that they can focus on finding the next story that matters. Invisible, automated curation of your home and section pages to streamline your team’s workflow. Continuously optimize the performance characteristics of the site based on demand and your brand, so the content is always the right fit.

## Highlights of Sophi.io service

* An automation platform capable of transforming your business.
* State of the art natural language processing to enable prediction and optimization.
* Tomorrow’s publishers rely on Sophi to better understand their audience and make sure every pixel is used to drive value efficiently and effectively.
* **Sophi Automation:** Automates your digital content curation to display your most valuable content in the most valuable places on your website, on social media and in newsletters.
* **Sophi Dive:** Uses your historical data to help content publishers and editors determine what content to produce more or less of and what to stop doing all together — which enables intelligent decision-making around resource allocation.
* **Sophi Now:** Equips you with analytics that provide real-time, tactical decision support that can tell you what content to promote more prominently, or less prominently; find out what’s over-performing and underperforming on a particular page; and find out why a particular article is doing well or not (for example, is it driving subscriptions, helping retain subscribers, getting traction on social media, search or newsletters, etc.) and where readers are coming from and going next on your website.
* **Sophi Next:** Flags a short list of articles that have just been published (but not promoted anywhere) that are likely to be your most valuable content going forward. This helps editors decide which headlines to place on a particular page — helping them find the hidden gems.
* **Sophi for Paywalls:** A fully dynamic, real-time paywall engine that understands both content and users and can optimize for several outcomes and offers simultaneously.

## Requirements

* [WordPress](http://wordpress.org) 5.6+
* [PHP](https://php.net/) 7.4+
* [Sophi.io account](https://www.sophi.io/contact/) and related credentials to authorize and authenticate access within the plugin settings

## Installation

*Note that if you are on the [WordPress VIP](https://wpvip.com/) platform that you will not be able to upload a plugin in the WordPress Admin so you will need to follow steps in 1b.*

### 1a. Download plugin ZIP file and upload to your site.
You can upload and install the [plugin ZIP file](https://downloads.wordpress.org/plugin/sophi.zip) via the WordPress dashboard (`Plugins` > `Add New` -> `Upload Plugin`) or manually inside of the `wp-content/plugins` directory, and activate on the Plugins dashboard.

### 1b. Download or Clone this repo, install dependencies, and build.
- `git clone https://github.com/globeandmail/sophi-for-wordpress.git && cd sophi-for-wordpress`
- `composer install && npm install && npm run build`

### 2. Activate Plugin
Click the `Activate` link after installing the plugin.

### 3. Configure plugin settings
Go to `Settings` > `Sophi` in the WordPress Admin and enter your Sophi Collector and Site Automation credentials supplied by the [Sophi.io](https://www.sophi.io/contact/) team.  Click `Save Changes` button to validate and save your credentials.

<img src="/.wordpress-org/screenshot-1.png" alt="Sophi.io Collector and Site Automation settings page." width="600">

Once your credentials are validated and saved, your site is officially supercharged by the Sophi.io Site Automation service.  You also have access to Sophi for WordPress' powerful WP_Query parameters and custom Sophi Site Automation block, which enables you to utilize Site Automation to power the content curation on your site.

## Usage

There are two potential ways to integrate Sophi Site Automation results with your WordPress site. The default approach includes a Sophi Site Automation block that integrates with `get_posts` by injecting Posts IDs via the `posts_pre_query` filter that gets fetched later to return the actual Posts. In the same fashion, you can integrate Sophi results with your `WP_Query` object by setting the `sophi_curated_page` and `sophi_curated_widget` query parameters.

More details on each of these two options are described below.  If you are not certain on the best integration approach, considering the following:

Reasons to use the Site Automation block:
- No additional development effort needed for initial Sophi integration
- Immediate integration with Sophi Site Automation API and page/widget settings
- Basic block display settings allow for basic configurations (show/hide post excerpt, author name, post date, featured image)

Reasons to use the Query integration:
- Can implement more custom caching and content fallback options
- Can implement support into non-block editor setups
- Likely more flexible for headless setups
- Block editor is not in-use within your WordPress environment
- You need an integration with Category/Taxonomy Pages and cannot integrate using a sidebar widget

### Site Automation block

You can utilize the Site Automation block by configuring the Site Automation page and widget names and adjusting block content and post meta settings to display Site Automation content on your site.

<img src="/.wordpress-org/screenshot-2.png" alt="Sophi Site Automation block." width="600">

The Site Automation block comes with a barebone HTML output and styling. It's made to be filtered using `sophi_site_automation_block_output`.

```php
add_filter(
	'sophi_site_automation_block_output',
	function( $output, $curated_posts, $attributes, $content, $block ) {
		// ...
		return $new_output;
	},
	10,
	5
);
```

The Site Automation block will automatically add a `data-sophi-feature=<widget_name>` attribute for the rendered HTML content so Sophi can understand what content section is rendered on your site.

### Query integration

The Site Automation block uses query integration under the hood. You can query for Sophi curated articles by passing `sophi_curated_page` and `sophi_curated_widget` to your WP_Query queries.

```php
$the_query = new WP_Query( [
	'sophi_curated_page'   => 'page-name',
	'sophi_curated_widget' => 'widget-name',
] );
```

Note that you need to add `data-sophi-feature=<widget_name>` to the wrapper div of your post for Sophi to capture your widget better.

```html
<div class="posts-wrapper" data-sophi-feature="trending">
	<!-- Post lists -->
</div>
```

#### Caveats

##### WordPress VIP and `WP_Query`

While the above query integration works just fine, it has been observed on [WordPress VIP](https://wpvip.com/) infrastructure that `WP_Query` may return latest posts instead of the posts curated by Sophi due to the [Advanced Post Cache](https://github.com/Automattic/advanced-post-cache) plugin used by the VIP platform. A workaround for this is to use [`get_posts()`](https://developer.wordpress.org/reference/functions/get_posts/) instead and as good practice to add a comment explaining the usage of it so that developers new to it don't swap it for `WP_Query`. Also remember to whitelist `get_posts` by adding the following inline comment so that PHPCS doesn't throw a warning:

`phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts`

### Post content type

By default, Sophi for WordPress uses post format as the content type. This plugin uses `content_type` internally to distinguish between WordPress post type and Sophi type.

Sophi accepts 4 types: article, video, audio, and image. The default type is `article`. Any other types that are not included above will be treated as `article`.

If you're not using post format for content type, you can utilize `sophi_post_content_type` to set the content type.

```php
add_filter(
	'sophi_post_content_type',
	function( $type, $post ) {
		// You logic here.

		return $new_type;
	},
	10,
	2
);
```

### Canonical URL

Sophi for WordPress uses `wp_get_canonical_url` function introduced in WordPress 4.6 to get the canonical URL for given post. The plugin compares this canonical URL to post permalink to set the `isCanonical` attribute.

WordPress SEO (Yoast) canonical is supported out of the box. For other SEO plugins and custom implementations, [`get_canonical_url`](https://developer.wordpress.org/reference/functions/wp_get_canonical_url/) filter can be used to change the canonical URL.

### Object caching

Object caching is encouraged, as the plugin saves Sophi data as a transient.  If you do not have object caching, then the data will be saved as a transient in the options table but note that these will eventually expire.

The default caching period is five minutes. This can be modified with the `sophi_cache_duration` hook.

### Sophi API empty response

If the Sophi API returns an empty Post ID array, the plugin will result in a "no results" response.  The `sophi_curated_post_list` filter can be used to modify the Sophi API response to allow manual posts to be injected into the final array previous to returning the filterable value from `posts_pre_query` (e.g., via a fallback method to inject posts that would be a good fit).

## Documentation

Sophi for WordPress has an in-depth documentation site that details the available actions and filters found within the plugin. [Visit the developer docs ☞](https://globeandmail.github.io/sophi-for-wordpress/)

## Debugging

If you are having difficulties with your Sophi integration, then we recommend utilizing the [Debug Bar for Sophi](https://wordpress.org/plugins/debug-bar-for-sophi/) plugin to help triage your Sophi Authentication, Sophi API integration, and CMS publish/update events.

## Frequently Asked Questions

### How was Sophi created?

The award-winning Sophi suite of solutions was developed by The Globe and Mail to help content publishers make important strategic and tactical decisions that transform their business. It is a suite of ML and NLP-powered tools that includes [Sophi Automation](https://www.sophi.io/automation) for content curation, Sophi for Paywalls, and Sophi Analytics – a decision-support system for publishers.

### How well does Sophi Automation perform?

The Globe and Mail saw a dramatic improvement in the business metrics that matter:
* Click-through rates were up 17% from the homepage.
* The subscriber acquisition rate increased by more than 10%.
* Not a single reader has complained or asked if a computer is curating the website.

You can read more details in [The Globe and Mail case study](https://www.sophi.io/automation/insights/case-studies/case-study-the-globe-and-mail-and-sophi-automation/).

### What Privacy and Terms govern Sophi.io?

The same [Privacy & Terms that govern The Globe and Mail](https://www.theglobeandmail.com/privacy-terms/) cover the Sophi.io service.

## Support Level

**Active:** The Globe and Mail is actively working on this, and we expect to continue work for the foreseeable future including keeping tested up to the most recent version of WordPress.  Bug reports, feature requests, questions, and pull requests are welcome.

## Changelog

A complete listing of all notable changes to Sophi for WordPress are documented in [CHANGELOG.md](https://github.com/globeandmail/sophi-for-wordpress/blob/develop/CHANGELOG.md).

## Contributing

Please read [CODE_OF_CONDUCT.md](https://github.com/globeandmail/sophi-for-wordpress/blob/develop/CODE_OF_CONDUCT.md) for details on our code of conduct, [CONTRIBUTING.md](https://github.com/globeandmail/sophi-for-wordpress/blob/develop/CONTRIBUTING.md) for details on the process for submitting pull requests to us, and [CREDITS.md](https://github.com/globeandmail/sophi-for-wordpress/blob/develop/CREDITS.md) for a listing of maintainers, contributors, and libraries for Sophi for WordPress.

## Like what you see?

<a href="http://10up.com/contact/"><img src="https://10up.com/uploads/2016/10/10up-Github-Banner.png" alt="Work with 10up, we create amazing websites and tools that make content management simple and fun using open source tools and platforms"></a>
