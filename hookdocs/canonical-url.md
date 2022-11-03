Sophi for WordPress uses `wp_get_canonical_url` function introduced in WordPress 4.6 to get the canonical URL for given post. The plugin compares this canonical URL to post permalink to set the `isCanonical` attribute.

WordPress SEO (Yoast) canonical is supported out of the box. For other SEO plugins and custom implementations, [`get_canonical_url`](https://developer.wordpress.org/reference/functions/wp_get_canonical_url/) filter can be used to change the canonical URL.
