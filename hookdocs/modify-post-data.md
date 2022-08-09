When a supported post item is saved (whether a publish, update, delete or unpublish event), data about that item is synced over to the Sophi service. While the intention of this plugin is to accurately capture all the necessary information automatically, there are some use cases that will need to modify this data prior to it being synced.

The data about each supported post item is passed through a custom filter ([`sophi_post_data`]{@link sophi_post_data}), which allows you to customize this information. See below for a few examples of how to utilize this filter.

A few things to note:

1. Where you add this code will depend on your individual setup. Often it makes sense to drop these in your main `functions.php` file
2. If data is changed or added, you may need to coordinate with the Sophi.io team to ensure content is appropriately received
3. Data that is changed needs to stay the same type as it is currently. For instance, if something is currently an array, it should stay an array

### Replace existing data

One of the fields that is sent is called `byline`. This is currently set to the author display name. If you need to change this to something else, like the author's nickname, you can do the following:

```php
/**
 * Change the post data we send to Sophi.
 *
 * Update the byline value to use the author's nickname.
 *
 * @param array $post_data Formatted post data.
 * @return array
 */
function custom_prefix_sophi_post_data( $post_data = [] ) {
	// Ensure we have a proper post object.
	$post = get_post( (int) $post_data['contentId'] );
	if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
		return $post_data;
	}

	// This field needs to be an array, even though it only has one value.
	$post_data['byline'] = [ get_the_author_meta( 'nickname', $post->post_author ) ];

	return $post_data;
}
add_filter( 'sophi_post_data', 'custom_prefix_sophi_post_data' );
```

Another field that is sent is called `isSponsored`. This is currently hardcoded to `false`. If you need to change this to `true`, you can do the following:

```php
/**
 * Change the post data we send to Sophi.
 *
 * Update the isSponsored value to true.
 *
 * @param array $post_data Formatted post data.
 * @return array
 */
function custom_prefix_sophi_post_data( $post_data = [] ) {
	// Ensure we have a proper post object.
	$post = get_post( (int) $post_data['contentId'] );
	if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
		return $post_data;
	}

	$post_data['isSponsored'] = true;

	return $post_data;
}
add_filter( 'sophi_post_data', 'custom_prefix_sophi_post_data' );
```

### Modify existing data

One of the fields that is sent is called `allSections`. This is currently set to an array of category paths. If you want to add additional taxonomy paths to this value, you can do the following:

```php
/**
 * Change the post data we send to Sophi.
 *
 * Add more paths to the allSections value.
 *
 * @param array $post_data Formatted post data.
 * @return array
 */
function custom_prefix_sophi_post_data( $post_data = [] ) {
	// Ensure we have a proper post object.
	$post = get_post( (int) $post_data['contentId'] );
	if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
		return $post_data;
	}

	$current_sections = $post_data['allSections'];

	// This custom function is not provided here but this would be similar to the
	// SophiWP\Utils\get_post_categories_paths utility function
	$new_sections = custom_prefix_get_taxonomy_paths( $post->ID, 'custom_taxonomy_slug' );

	// Add our new sections to the end of our existing sections.
	$post_data['allSections'] = array_merge( $current_sections, $new_sections );

	return $post_data;
}
add_filter( 'sophi_post_data', 'custom_prefix_sophi_post_data' );
```

### Add new data

If you need to add an entirely new field, that can be done as well:

```php
/**
 * Change the post data we send to Sophi.
 *
 * Add a new field called videoIds.
 *
 * @param array $post_data Formatted post data.
 * @return array
 */
function custom_prefix_sophi_post_data( $post_data = [] ) {
	// Ensure we have a proper post object.
	$post = get_post( (int) $post_data['contentId'] );
	if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
		return $post_data;
	}

	// This custom function is not provided here but this would
	// run some logic to get video IDs from the post object.
	$post_data['videoIds'] = custom_prefix_get_video_ids( $post->ID );

	return $post_data;
}
add_filter( 'sophi_post_data', 'custom_prefix_sophi_post_data' );
```
