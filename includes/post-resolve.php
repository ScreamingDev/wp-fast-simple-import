<?php

/**
 * @param $data
 *
 * @return bool|WP_Post false if not found.
 */
function fsi_resolve_post( $data ) {
	// try by ID
	if ( isset( $data['ID'] ) && $data['ID'] ) {
		return get_post( $data['ID'] );
	}

	// build search query
	$query = [
		'post_type'   => get_post_types(),
		'post_status' => get_post_stati(),
	];

	$query = array_merge( $query, array_intersect_key( $data, $query ) );

	// try by the unique post_name
	if ( isset( $data['post_name'] ) ) {
		$name_query = $query;

		$name_query['post_name'] = $data['post_name'];

		$posts = get_posts( $name_query );

		if ( count( $posts ) == 1 ) {
			return current( $posts );
		}
	}

	// try by the title
	if ( isset( $data['post_title'] ) && $data['post_title'] ) {
		$name_query = $query;

		// use general search to find post
		$name_query['s'] = $data['post_title'];

		$posts = get_posts( $name_query );

		foreach ( $posts as $post ) {
			if ( $post->post_title == $data['post_title'] ) {
				return $post;
			}
		}
	}

	// give up
	return false;
}