<?php

function fsi_import_post( $data ) {
	$post = fsi_resolve_post( $data );

	if ( is_wp_error( $post ) ) {
		throw new \Exception(
			'Something is wrong with the query.'
		);
	}

	// put all non-post data to meta keys
	if ( ! isset( $data['meta_input'] ) ) {
		$data['meta_input'] = array();
	}

	foreach ( $data as $key => $value ) {
		if ( property_exists( WP_Post::class, $key ) ) {
			// normal property of posts => do nothing
			continue;
		}

		// keep tax and meta data
		if ( 'tax_input' == $key || 'meta_input' == $key || 'tags_input' == $key ) {
			continue;
		}

		// assume that unknown properties are meant as meta data.
		$data['meta_input'][ $key ] = $value;
		unset( $data[ $key ] );
	}

	if ( isset( $data['tax_input'] ) ) {
		$cap_array = array();
		// allow to assign terms to a post for each taxonomy
		foreach ( array_keys( $data['tax_input'] ) as $tax_name ) {
			$taxonomy_obj = get_taxonomy( $tax_name );

			if ( ! $taxonomy_obj ) {
				throw new \DomainException(
					'Invalid taxonomy ' . $tax_name . ' in data set ' . var_export( $data, true )
				);
			}

			$cap_array[] = $taxonomy_obj->cap->assign_terms;
		}

		fsi_enable_caps( array_unique( $cap_array ) );
	}

	$post_id = null;
	if ( ! $post ) {
		// not found => create
		$post_id = wp_insert_post( $data );

		if ( is_wp_error( $post_id ) ) {
			throw new \Exception(
				sprintf(
					'Could not import "%s" as new "%s".',
					$data['post_title'],
					$data['post_type'] ?: 'post'
				)
			);
		}

		// fetch post for upcoming logic (esp. return value)
		$post = get_post( $post_id );
	}

	if ( ! $post_id ) {
		// not a new post => update
		$data['ID'] = $post->ID;
		wp_update_post( $data );
	}

	// update thumbnail
	if ( isset( $data['_thumbnail'] ) && $data['_thumbnail'] ) {
		fsi_import_thumbnail( $post->ID, $data['_thumbnail'] );
	}

	return $post->ID;
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