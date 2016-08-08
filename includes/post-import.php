<?php

function fsi_import_post( $data ) {
	$post = fsi_resolve_post( $data );

	if ( is_wp_error( $post ) ) {
		throw new \Exception(
			'Something is wrong with the query.'
		);
	}

	if ( ! $post ) {
		// todo create
	}

	// _thumbnail_id
	if ( isset( $data['_thumbnail'] ) && $data['_thumbnail'] ) {
		fsi_import_thumbnail( $post->ID, $data['_thumbnail'] );
	}
}

/**
 * @param                           $post_id
 * @param array|string|int|\WP_Term $term_name
 * @param string                    $taxonomy
 */
function fsi_post_add_term( $post_id, $term_name, $taxonomy = 'category' ) {
	if ( $post_id instanceof \WP_Post ) {
		$post_id = $post_id->ID;
	}

	if ( $term_name instanceof \WP_Term ) {
		$term_name = $term_name->name;
	}

	$term_id = fsi_term_import( $term_name, $taxonomy );

	if ( ! $term_id ) {
		return false;
	}

	wp_set_post_terms( $post_id, $term_id, $taxonomy, true );

	return $term_id;
}