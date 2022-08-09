By default, the Sophi for WordPress plugin only adds support for the standard `post` and `page` post types. This value is passed through a custom filter ([`sophi_supported_post_types`]{@link sophi_supported_post_types}), which allows you to customize those values. See below for a few examples of how to utilize this filter.

A few things to note:

1. Where you add this code will depend on your individual setup. Often it makes sense to drop these in your main `functions.php` file
2. The examples here are set up to support any other code that may be using the same filters. If you're sure that's not the case, the code can be simplified as needed

### Remove a default post type

The `post` and `page` post types are supported by default. If you want to remove the `page` post type, for instance, you can do the following:

```php
/**
 * Change the post types Sophi supports.
 *
 * Remove the `page` post type from being supported.
 *
 * @param array $post_types Default supported post types.
 * @return array
 */
function custom_prefix_sophi_supported_post_types( $post_types = [] ) {
	$post_types_to_remove = [ 'page' ];
	return array_diff( $post_types, $post_types_to_remove );
}
add_filter( 'sophi_supported_post_types', 'custom_prefix_sophi_supported_post_types' );
```

### Add a new post type to the default list

If you have a custom post type that you want to support, along with the `post` and `page` post types, you can do the following:

```php
/**
 * Change the post types Sophi supports.
 *
 * Add our custom `video` post type while still
 * supporting the default post types.
 *
 * @param array $post_types Default supported post types.
 * @return array
 */
function custom_prefix_sophi_supported_post_types( $post_types = [] ) {
	$post_types_to_add = [ 'video' ];
	return array_merge( $post_types, $post_types_to_add );
}
add_filter( 'sophi_supported_post_types', 'custom_prefix_sophi_supported_post_types' );
```

### Replace default post types with custom ones

If you only want to support custom post types and not the defaults, you can do the following:

```php
/**
 * Change the post types Sophi supports.
 *
 * Remove the `post` and `page` post types
 * and add our custom video and gallery post types.
 *
 * @param array $post_types Default supported post types.
 * @return array
 */
function custom_prefix_sophi_supported_post_types( $post_types = [] ) {
	$post_types_to_remove = [ 'post', 'page' ];
	$post_types_to_add    = [ 'video', 'gallery' ];
	return array_merge(
		array_diff( $post_types, $post_types_to_remove ),
		$post_types_to_add
	);
}
add_filter( 'sophi_supported_post_types', 'custom_prefix_sophi_supported_post_types' );
```
