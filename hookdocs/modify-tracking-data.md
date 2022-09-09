The plugin generates tracking data and sent it to Sophi Collector on normal (non-AMP) page views. While the intention of this plugin is to accurately capture all the necessary information automatically, there are some use cases that will need to modify this data prior to it being sent to Sophi.

The generated tracking data is passed through a custom filter ([`sophi_tracking_data`]{@link sophi_tracking_data}), which allows you to customize this information. See below for a few examples of how to utilize this filter.

A few things to note:

1. Where you add this code will depend on your individual setup. If you are using a custom theme, you can add these to your main `functions.php` file. If you're using a public theme, you might want to create a custom plugin to hold these code changes, so you don't lose those changes when you update your theme
2. If data is changed or added, you may need to coordinate with the Sophi.io team to ensure tracking data is appropriately received
3. Data that is changed needs to stay the same type as it is currently. For instance, if something is currently an array, it should stay an array

### Modify existing data

One of the fields that are sent is called `type`. This is currently set to the content type of the post. If you need to change this to something else, like "video", you can do the following:

```php
/**
 * Change the tracking data we send to Sophi.
 *
 * Update the content type and page type data to video.
 *
 * @param array $tracking_data Formatted tracking data.
 * @return array
 */
function custom_prefix_sophi_tracking_data( $tracking_data = [] ) {
	$tracking_data['data']['page']['type']    = 'video';
	$tracking_data['data']['content']['type'] = 'video';

	return $tracking_data;
}
add_filter( 'sophi_tracking_data', 'custom_prefix_sophi_tracking_data' );
```

### Add new data

If you need to add an entirely new field, that can be done as well:

```php
/**
 * Change the tracking data we send to Sophi.
 *
 * Add a new field called videoIds.
 *
 * @param array $tracking_data Formatted tracking data.
 * @return array
 */
function custom_prefix_sophi_tracking_data( $tracking_data = [] ) {
	// This custom function is not provided here but this would
	// run some logic to get video IDs from the post object.
	$tracking_data['data']['content']['videoIds'] = custom_prefix_get_video_ids();

	return $tracking_data;
}
add_filter( 'sophi_tracking_data', 'custom_prefix_sophi_tracking_data' );
```
