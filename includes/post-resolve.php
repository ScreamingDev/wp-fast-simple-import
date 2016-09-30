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
		'post_type' => get_post_types(),
	];

	$query = array_merge( $query, array_intersect_key( $data, $query ) );

	// use _import_uid if given
	if ( isset( $data['_import_uid'] ) && $data['_import_uid'] ) {
		$id_query               = $query;
		$id_query['meta_key']   = '_import_uid';
		$id_query['meta_value'] = $data['_import_uid'];
		$id_query['post_status'] = array(
			'draft',
			'publish',
			'trash',
			'private',
		);

		$posts = get_posts( $id_query );

		if ( count( $posts ) > 1 ) {
			throw new \DomainException(
				'The following post has duplicates. "_import_uid" should exist only once in a post-type: '
				. var_export( $data, true )
			);
		}

		if ( count( $posts ) == 1 ) {
			return current( $posts );
		}

		// nothing found, by exact match of _import_uid => force import
		return false;
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
