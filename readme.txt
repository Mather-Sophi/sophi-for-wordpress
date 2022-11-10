=== Sophi ===
Contributors:      10up, sophidev
Tags:              Sophi, Site Automation, Curator, Collector, AI, Artifical Intelligence, ML, Machine Learning, Content Curation
Requires at least: 6.0
Tested up to:      6.0
Stable tag:        1.3.1
Requires PHP:      7.4
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

== Caveats ==

=== WordPress VIP and `WP_Query` ===

While the above query integration works just fine, it has been observed on [WordPress VIP](https://wpvip.com/) infrastructure that `WP_Query` may return latest posts instead of the posts curated by Sophi due to the [Advanced Post Cache](https://github.com/Automattic/advanced-post-cache) plugin used by the VIP platform. A workaround for this is to use [`get_posts()`](https://developer.wordpress.org/reference/functions/get_posts/) instead and as good practice to add a comment explaining the usage of it so that developers new to it don't swap it for `WP_Query`. Also remember to whitelist `get_posts` by adding the following inline comment so that PHPCS doesn't throw a warning:

`phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts`

== Developer Documentation ==

Sophi for WordPress has an in-depth documentation site that details the available actions and filters found within the plugin. [Visit the developer docs ☞](https://globeandmail.github.io/sophi-for-wordpress/)

== Debugging ==

If you are having difficulties with your Sophi integration, then we recommend utilizing the [Debug Bar for Sophi](https://wordpress.org/plugins/debug-bar-for-sophi/) plugin to help triage your Sophi Authentication, Sophi API integration, and CMS publish/update events.

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

= 1.3.1 - 2022-11-10 =
* **Changed:** Mapping for `sectionNames` field to reflect Primary Category or first Category (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@vjayaseelan90](https://github.com/vjayaseelan90), [@dkotter](https://github.com/dkotter) via [#350](https://github.com/globeandmail/sophi-for-wordpress/pull/350), [#363](https://github.com/globeandmail/sophi-for-wordpress/pull/363)).
* **Changed:** Update our configured check to be smarter (props [@dkotter](https://github.com/dkotter), [@vjayaseelan90](https://github.com/vjayaseelan90), [@jeffpaul](https://github.com/jeffpaul) via [#361](https://github.com/globeandmail/sophi-for-wordpress/pull/361)).
* **Removed:** Comment out the override settings _for now_ (props [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#362](https://github.com/globeandmail/sophi-for-wordpress/pull/362)).
* **Fixed:** Ensure we have an array of items before iterating over them (props [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#362](https://github.com/globeandmail/sophi-for-wordpress/pull/362)).

= 1.3.0 - 2022-10-28 =
**Note that this version bumps the minimum WordPress version from 5.6 to 6.0 and adds an integration with the Sophi Override feature.**

* **Added:** Override feature to the Sophi Site Automation Block (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333), [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
* **Added:** [GET] `/wp-json/sophi/v1/site-automation` WP REST endpoint to Get Posts from Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).
* **Added:** [POST] `/wp-json/sophi/v1/site-automation-override` WP REST endpoint to Override the Posts for Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).
* **Added:** `api_version` to the block JSON files (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
* **Added:** Display message when no posts are returned from Sophi Site Automation (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
* **Added:** Functionality to remove old error messages when posts are found (props [@faisal-alvi](https://github.com/faisal-alvi) via [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
* **Added:** Tracker Address option on plugin settings page (props [@faisal-alvi](https://github.com/faisal-alvi), [@YMufleh](https://github.com/YMufleh), [@jeffpaul](https://github.com/jeffpaul) via [#342](https://github.com/globeandmail/sophi-for-wordpress/pull/342)).
* **Added:** New tutorial for modifying tracking data on documentation site (props [@iamdharmesh](https://github.com/iamdharmesh), [@YMufleh](https://github.com/YMufleh), [@dkotter](https://github.com/dkotter) via [#327](https://github.com/globeandmail/sophi-for-wordpress/pull/327)).
* **Changed:** Bump minimum WordPress version from 5.6 to 6.0 (props [@faisal-alvi](https://github.com/faisal-alvi) via [#346](https://github.com/globeandmail/sophi-for-wordpress/pull/346)).
* **Changed:** Sophi Site Automation Block from a server-side-rendered custom block to a nested block (props [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@fabiankaegy](https://github.com/fabiankaegy), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333), [#345](https://github.com/globeandmail/sophi-for-wordpress/pull/345)).
* **Changed:** Replaced hardcoded tracker endpoint value with the new Tracker Address setting (props [@faisal-alvi](https://github.com/faisal-alvi), [@YMufleh](https://github.com/YMufleh), [@jeffpaul](https://github.com/jeffpaul) via [#342](https://github.com/globeandmail/sophi-for-wordpress/pull/342)).
* **Changed:** Developer Documentation updates (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@faisal-alvi](https://github.com/faisal-alvi) via [#332](https://github.com/globeandmail/sophi-for-wordpress/pull/332), [#333](https://github.com/globeandmail/sophi-for-wordpress/pull/333)).
* **Fixed:** Prevent duplicate CMS publish/update event tracking (props [@cadic](https://github.com/cadic), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#335](https://github.com/globeandmail/sophi-for-wordpress/pull/335)).
* **Security:** Added latest PHPCompatibility checks for PHP 8.0 (props [@Sidsector9](https://github.com/Sidsector9) via [#331](https://github.com/globeandmail/sophi-for-wordpress/pull/331)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.29 to 9.5.25 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#329](https://github.com/globeandmail/sophi-for-wordpress/pull/329), [#337](https://github.com/globeandmail/sophi-for-wordpress/pull/337), [#339](https://github.com/globeandmail/sophi-for-wordpress/pull/339)).
* **Security:** Update dependency `10up-toolkit` from 4.1.2 to 4.3.0 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#330](https://github.com/globeandmail/sophi-for-wordpress/pull/330), [#340](https://github.com/globeandmail/sophi-for-wordpress/pull/340)).
* **Security:** Update `actions/checkout` action from v2 to v3 and v3.0.2 to v3.1.0 (props [@renovate](https://github.com/apps/renovate) via [#334](https://github.com/globeandmail/sophi-for-wordpress/pull/334)).

= 1.2.1 - 2022-08-30 =
* **Changed:** Bump `content_update` schema from 2-0-3 to 2-0-5 (props [@jeffpaul](https://github.com/jeffpaul), [@vjayaseelan90](https://github.com/vjayaseelan90), [@YMufleh](https://github.com/YMufleh) via [#323](https://github.com/globeandmail/sophi-for-wordpress/pull/323)).
* **Security:** Update dependency `jsdoc` from 3.6.10 to 3.6.11 (props [@renovate](https://github.com/apps/renovate) via [#311](https://github.com/globeandmail/sophi-for-wordpress/pull/311)).
* **Security:** Bump `terser` from 5.13.1 to 5.14.2 (props [@dependabot](https://github.com/apps/dependabot) via [#312](https://github.com/globeandmail/sophi-for-wordpress/pull/312)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.27 to 8.5.29 (props [@renovate](https://github.com/apps/renovate) via [#316](https://github.com/globeandmail/sophi-for-wordpress/pull/316)).

= 1.2.0 - 2022-08-15 =
* **Added:** New Curator API (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#310](https://github.com/globeandmail/sophi-for-wordpress/pull/310).
* **Added:** Documentation on default handling of Sophi API empty array response (props [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez) via [#307](https://github.com/globeandmail/sophi-for-wordpress/pull/307).
* **Added:** New tutorial sections added to the documentation site (props [@dkotter](https://github.com/dkotter), [@YMufleh](https://github.com/YMufleh) via [#318](https://github.com/globeandmail/sophi-for-wordpress/pull/318).
* **Changed:** Instead of storing responses from Sophi in an option, store that in a new CPT, `sophi-response` (props [@oscarssanchez](https://github.com/oscarssanchez), [@cadic](https://github.com/cadic), [@felipeelia](https://github.com/felipeelia) via [#298](https://github.com/globeandmail/sophi-for-wordpress/pull/298).
* **Changed:** Replaces `WP_Query` with `get_posts` in details about the Sophi Automation block (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#300](https://github.com/globeandmail/sophi-for-wordpress/pull/300).
* **Removed:** Dependency on Auth0 and replaced with the new Curator API (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#310](https://github.com/globeandmail/sophi-for-wordpress/pull/310).
* **Fixed:** Ensure we are properly getting cached responses (props [@oscarssanchez](https://github.com/oscarssanchez), [@Sidsector9](https://github.com/Sidsector9), [@felipeelia](https://github.com/felipeelia) via [#305](https://github.com/globeandmail/sophi-for-wordpress/pull/305).
* **Security:** Added PHP compatibility checker (props [@Sidsector9](https://github.com/Sidsector9), [@dkotter](https://github.com/dkotter) via [#317](https://github.com/globeandmail/sophi-for-wordpress/pull/317).
* **Security:** Update dependency `10up-toolkit` from v4.0.0 to v4.1.2 (props [@renovate](https://github.com/apps/renovate), [@Sidsector9](https://github.com/Sidsector9) via [#299](https://github.com/globeandmail/sophi-for-wordpress/pull/299), [#303](https://github.com/globeandmail/sophi-for-wordpress/pull/303).
* **Security:** Update dependency `phpunit/phpunit` from v8.5.26 to v8.5.27 (props [@renovate](https://github.com/apps/renovate) via [#304](https://github.com/globeandmail/sophi-for-wordpress/pull/304).

= 1.1.3 - 2022-05-27 =
* **Added:** WordPress and Sophi plugin versions to Javascript and AMP Tracking (props [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul), [@YMufleh](https://github.com/YMufleh) via [#287](https://github.com/globeandmail/sophi-for-wordpress/pull/287)).
* **Added:** Documentation for `sophi_cache_duration` filter (props [@Sidsector9](https://github.com/Sidsector9), [@barryceelen](https://github.com/barryceelen), [@jeffpaul](https://github.com/jeffpaul) via [#285](https://github.com/globeandmail/sophi-for-wordpress/pull/285)).
* **Added:** Unit tests (props [@cadic](https://github.com/cadic) via [#291](https://github.com/globeandmail/sophi-for-wordpress/pull/291)).
* **Changed:** Reverted response saving back to options table from post meta field (props [@oscarssanchez](https://github.com/oscarssanchez) via [#280](https://github.com/globeandmail/sophi-for-wordpress/pull/280)).
* **Changed:** Replace deprecated `block_categories` filter with `block_categories_all` (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#292](https://github.com/globeandmail/sophi-for-wordpress/pull/292)).
* **Changed:** Bump WordPress "tested up to" version 6.0 (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#293](https://github.com/globeandmail/sophi-for-wordpress/pull/293)).
* **Fixed:** Fatal error on tracking a post with empty content (props [@cadic](https://github.com/cadic) via [#289](https://github.com/globeandmail/sophi-for-wordpress/pull/289)).
* **Fixed:** Warning shown due to access of undefined array (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul) via [#292](https://github.com/globeandmail/sophi-for-wordpress/pull/292)).
* **Fixed:** Warning on Widget page via check before accessing property (props [@Sidsector9](https://github.com/Sidsector9) via [#295](https://github.com/globeandmail/sophi-for-wordpress/pull/295)).
* **Fixed:** PHPCS check and linting issues (props [@cadic](https://github.com/cadic) via [#291](https://github.com/globeandmail/sophi-for-wordpress/pull/291)).

= 1.1.2 - 2022-05-18 =
* **Fixed:** Incorrect posts rendered due to wrong transient name (props [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic)).

= 1.1.1 - 2022-05-17 =
* **Added:** `sophi_bypass_curated_posts_cache` filter to bypess curated posts cache (props [@cadic](https://github.com/cadic)).
* **Added:** Send all Categories in new `allSections` field to Sophi (props [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul), [@tott](https://github.com/tott)).
* **Changed:** `$result` is used instead of `$request` to store return value of the `sophi_request_result` filter (props [@faisal-alvi](https://github.com/faisal-alvi), [@Sidsector9](https://github.com/Sidsector9), [@cadic](https://github.com/cadic)).
* **Changed:** Suppress Emitter debug output and writing logs (props [@cadic](https://github.com/cadic)).
* **Fixed:** [Hook Docs](https://globeandmail.github.io/sophi-for-wordpress/) deployment GitHub Action and included filter docs (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic)).

= 1.1.0 - 2022-05-06 =
* **Added:** Filter `sophi_request_args` filters arguments used in Sophi HTTP request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257)).
* **Added:** Filter `sophi_request_result` filters a Sophi HTTP request immediately after the response is received (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#261](https://github.com/globeandmail/sophi-for-wordpress/pull/261)).
* **Added:** Filter `sophi_cms_tracking_request_data` filters the data used in Sophi track event request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#263](https://github.com/globeandmail/sophi-for-wordpress/pull/263)).
* **Added:** Action `sophi_cms_tracking_result` fires after tracker sends the request (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257), [#263](https://github.com/globeandmail/sophi-for-wordpress/pull/263)).
* **Added:** Filter `sophi_tracker_emitter_debug` allows to enable debug mode in the tracking emitter (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/globeandmail/sophi-for-wordpress/pull/257)).
* **Added:** Documentation on rational for block vs. custom query integration uses and `WP_Query` caveat on WordPress VIP (props [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez) via [#242](https://github.com/globeandmail/sophi-for-wordpress/pull/242), [#249](https://github.com/globeandmail/sophi-for-wordpress/pull/249), [#251](https://github.com/globeandmail/sophi-for-wordpress/pull/251)).
* **Changed:** Update response saving from options table to post meta field (props [@Rahmon](https://github.com/Rahmon), [@oscarssanchez](https://github.com/oscarssanchez) via [#253](https://github.com/globeandmail/sophi-for-wordpress/pull/253)).
* **Changed:** Bump WordPress "tested up to" version 5.9 (props [@jeffpaul](https://github.com/jeffpaul) via [#248](https://github.com/globeandmail/sophi-for-wordpress/pull/248)).
* **Changed:** Updated hookdocs site (now [Developer Docs](https://globeandmail.github.io/sophi-for-wordpress/)) to properly run on release and migrate in docs from readme (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#256](https://github.com/globeandmail/sophi-for-wordpress/pull/256)).
* **Fixed:** Check if `$terms` is an array before using `count()` within `get_the_terms()` (props [@barryceelen](https://github.com/barryceelen), [@Sidsector9](https://github.com/Sidsector9) via [#215](https://github.com/globeandmail/sophi-for-wordpress/pull/215)).
* **Fixed:** Update `track_event` arguments list inside `sync` WP-CLI command (props [@cadic](https://github.com/cadic), [@Sidsector9](https://github.com/Sidsector9) via [#250](https://github.com/globeandmail/sophi-for-wordpress/pull/250)).
* **Security:** Update `actions/checkout` action to from v2.4.0 to v3.0.2 (props [@renovate](https://github.com/apps/renovate), [@iamdharmesh](https://github.com/iamdharmesh) via [#240](https://github.com/globeandmail/sophi-for-wordpress/pull/240), [#241](https://github.com/globeandmail/sophi-for-wordpress/pull/241), [#243](https://github.com/globeandmail/sophi-for-wordpress/pull/243)).
* **Security:** Update `actions/setup-node` action from v1 to v3 (props [@renovate](https://github.com/apps/renovate), [@iamdharmesh](https://github.com/iamdharmesh) via [#244](https://github.com/globeandmail/sophi-for-wordpress/pull/244)).
* **Security:** Update dependency `10up-toolkit` from 3.1.2 to 4.0.0 (props [@renovate](https://github.com/apps/renovate), [@cadic](https://github.com/cadic) via [#264](https://github.com/globeandmail/sophi-for-wordpress/pull/264)).

= 1.0.13 - 2022-04-20 =
* **Changed:** `keywords` data type from string to array (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchez](https://github.com/oscarssanchez), [@YMufleh](https://github.com/YMufleh)).
* **Security:** Bump `node-forge` from 1.2.1 to 1.3.0 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `minimist` from 1.2.5 to 1.2.6 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Update dependency `10up-toolkit` from 3.0.3 to 3.1.2 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.25 to 8.5.26 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Bump `async` from 2.6.3 to 2.6.4 (props [@dependabot](https://github.com/apps/dependabot)).

= 1.0.12 - 2022-03-23 =
* **Fixed:** PHP fatal error when duplicating posts across sites (props [@YMufleh](https://github.com/YMufleh)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.23 to 8.5.25 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `10up-toolkit` from 3.0.2 to 3.0.3 (props [@renovate](https://github.com/apps/renovate)).

= 1.0.11 - 2022-03-03 =
* **Changed:** Update `package-lock.json` (props [@jeffpaul](https://github.com/jeffpaul)).
* **Security:** Update `actions/setup-node` action from v2 to v3 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update `actions/checkout` action from v2 to v3 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `10up-toolkit` from 2.1.1 to 3.0.2 (props [@renovate](https://github.com/apps/renovate)).

= 1.0.10 - 2022-02-28 =
* **Changed:** Sets the default timeout of the first Sophi request and the cron request to 3 seconds (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia), [@tott](https://github.com/tott)).
* **Security:** Update dependency `snowplow/snowplow-tracker` from 0.4.0 to 0.5.0 (props [@renovate](https://github.com/apps/renovate)).

= 1.0.9 - 2022-02-18 =
* **Added:** `hostname` and `path` fields to schema (props [@Rahmon](https://github.com/Rahmon), [@dinhtungdu](https://github.com/dinhtungdu)).
* **Added:** - `sophi_curated_post_list` filter to modify the Sophi API response to allow manual posts to be injected previous to returning the filterable value from `posts_pre_query` (e.g., via a fallback method to inject posts that would be a good fit) (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia)).
* **Fixed:** Sophi API empty Post ID array response changed from using `WP_Query` default results to a "no results" response (props [@oscarssanchez](https://github.com/oscarssanchez), [@barryceelen](https://github.com/barryceelen), [@felipeelia](https://github.com/felipeelia)).
* **Security:** Update dependency `phpunit/phpunit` from 8.5.21 to 8.5.23 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `prop-types` from 15.8.0 to 15.8.1 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `10up-toolkit` from `1.0.13` to `2.1.1` (props [@renovate](https://github.com/apps/renovate), [@oscarssanchez](https://github.com/oscarssanchez), [@jeffpaul](https://github.com/jeffpaul)).
* **Security:** Update dependency `marked` from 2.1.3 to 4.0.10 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `jsdoc` from 3.6.8 to 3.6.10 (props [@renovate](https://github.com/apps/renovate)).
* **Security:** Update dependency `dealerdirect/phpcodesniffer-composer-installer` from 0.7.1 to 0.7.2 (props [@renovate](https://github.com/apps/renovate)).

= 1.0.8 - 2021-12-23 =
* **Changed:** Updated `auth_url` and `audience` parameters to get respective Staging and Development environment access tokens (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia)).
* **Fixed:** Issue where multiple Sophi blocks on the same page had duplicated content (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia), [@jeffpaul](https://github.com/jeffpaul), [@dinhtungdu](https://github.com/dinhtungdu)).
* **Fixed:** Testing environment deploy process (props [@Rahmon](https://github.com/Rahmon), [@felipeelia](https://github.com/felipeelia)).
* **Security:** update dependency `prop-types` from 15.7.2 to 15.8.0 (props [@renovate](https://github.com/marketplace/renovate)).

= 1.0.7 - 2021-10-29 =
* **Changed:** Sophi Auth URL value used for Production, Staging, and Development (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@barryceelen](https://github.com/barryceelen), [@amckie](https://github.com/amckie)).
* **Changed:** Sophi Site Automation API URL structure (props [@rahmohn](https://profiles.wordpress.org/rahmohn/)).
* **Changed:** Sophi Audience URL used in authorization (props [@rahmohn](https://profiles.wordpress.org/rahmohn/)).

= 1.0.6 - 2021-10-27 =
Note: this was a hotfix release to fix an issue with deploys to WordPress.org.

= 1.0.5 - 2021-10-27 =
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

= 1.0.4 - 2021-07-29 =
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

= 1.0.3 - 2021-06-15 =
* **Added:** Data attributes to Site Automation widgets/blocks (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Changed:** Revisions to tracking data collected to improve Site Automation results (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/)).
* **Changed:** Update default Collector URL helper text (props [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Changed:** Set default environment according to `wp_get_environment_type()` (props [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Fixed:** Only load Snowplow analytics once Sophi plugin is configured (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/), [@barryceelen](https://profiles.wordpress.org/barryceelen/)).
* **Fixed:** VIP CLI dry-run mode defaults to `false`, corrects sync limiting, adds progress bar (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@barryceelen](https://profiles.wordpress.org/barryceelen/)).

= 1.0.2 - 2021-04-26 =
* **Changed:** Bump `vipwpcs` from 2.2.0 to 2.3.0 and `php_codesniffer` from 3.5.8 to 3.6.0 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Fixed:** WP-CLI command now supports both WordPress VIP and non-VIP environments (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@dkotter](https://profiles.wordpress.org/dkotter/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).

= 1.0.1 - 2021-04-23 =
* **Added:** `noConfigFile` setting (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Added:** GitHub Actions to deploy releases and update readme/asset changes for WordPress.org (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/)).
* **Changed:** Renamed plugin from `Sophi for WordPress` to `Sophi` (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Fixed:** Minor fixes from WordPress VIP code scan (props [@dkotter](https://profiles.wordpress.org/dkotter/)).

= 1.0.0 - 2021-04-14 =
* Initial public release! 🎉

== Upgrade Notice ==

= 1.3.0 =
This version bumps the minimum WordPress version from 5.6 to 6.0 and adds an integration with the Sophi Override feature.
