<?php

namespace SophiWP\Curator;

class Integration {
	private $request;

	public function __construct( $request ) {
		$this->request = $request;

		add_filter( 'posts_pre_query', 'get_curated_posts', 10, 2 );
	}

	public function get_curated_posts( $posts, $query ) {
		$query_vars = $query->query_vars;

		if ( empty( $query_vars['sophi_integrate'] ) ) {
			return $posts;
		}

		return $posts;

		// $new_post = $this->request->get_articles( $page, $)

		// $fields = $query->get( 'fields', '' );
	}
}
