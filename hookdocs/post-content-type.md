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
