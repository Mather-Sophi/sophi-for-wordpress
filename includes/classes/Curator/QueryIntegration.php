<?php

namespace SophiWP\Curator;

class QueryIntegration {
	public function __construct() {
		add_filter( 'posts_pre_query', 'get_curated_posts', 10, 2 );
	}

	public function get_curated_posts( $posts, $query ) {
		return $posts;
	}
}
