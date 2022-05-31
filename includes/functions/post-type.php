<?php
/**
 * The Sophi Post type register file.
 */
namespace SophiWP\PostType;

/**
 * Registers the sophi-response post type.
 */
function post_type() {
	$args = [
		'description'        => esc_html__( 'Sophi Responses', 'sophi-wp' ),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => [ 'title' ],
	];

	register_post_type( 'sophi-response', $args );
}
