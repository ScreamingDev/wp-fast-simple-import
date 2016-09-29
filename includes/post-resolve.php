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
		$id_query                = $query;
		$id_query['meta_key']    = '_import_uid';
		$id_query['meta_value']  = $data['_import_uid'];
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
	}

	// try by the unique post_name
	if ( isset( $data['post_name'] ) ) {
		$name_query = $query;

		$name_query['post_name'] = $data['post_name'];

		$posts = get_posts( $name_query );

		if ( count( $posts ) == 1 ) {
			$post = current( $posts );
			if ( ! fsi_ensure_resolved_post( $post ) ) {
				throw new \Exception(
					'Warning: Post resolved to different/existing UID on post_name match:' . PHP_EOL
					. var_export( $data, true ) . PHP_EOL
					. var_export( $post, true ) . PHP_EOL
				);
			}

			return $post;
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
				if ( ! fsi_ensure_resolved_post( $post ) ) {
					throw new \Exception(
						'Warning: Post resolved to different/existing UID on title match:' . PHP_EOL
						. var_export( $data, true ) . PHP_EOL
						. var_export( $post, true ) . PHP_EOL
					);
				}

				return $post;
			}
		}
	}

	// give up
	return false;
}

/**
 * Ensure that a resolved post does not belong
 * to another imported instance
 *
 * @var $post   WP_Post
 * @var $uid    int         (optional) default 0: post MUST NOT have an import_uid
 *                          If an uid is given, post MUST have this import_uid
 *
 * @return      bool        False if import_uid violation found, true otherwise
 */
function fsi_ensure_resolved_post( WP_Post $post, $uid = 0 ) {
	return ( (int) get_post_meta( $post->ID, '_import_uid', true ) == (int) $uid );
}
