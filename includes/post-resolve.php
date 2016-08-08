<?php

/**
 * @param $data
 *
 * @return array|bool|mixed|null|WP_Post
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

	if ( isset( $data['post_type'] ) && $data['post_type'] ) {
		$query['post_type'] = $data['post_type'];
	}

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
		$post = get_page_by_title( $data['post_title'], OBJECT, $query['post_type'] );

		if ( $post && ! is_wp_error( $post ) ) {
			return $post;
		}
	}

	// give up
	return false;
}