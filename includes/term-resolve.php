<?php

function fsi_term_resolve( $name, $taxonomy = 'category' ) {
	$term = get_term_by( 'name', $name, $taxonomy );

	if ( $term ) {
		return $term->term_id;
	}

	return false;
}

/**
 * @param string $taxonomy
 * @param string $field
 * @param string $value
 *
 * @return array
 */
function fsi_get_all_terms_by( $taxonomy, $field, $value ) {
	$query = array( 'taxonomy' => $taxonomy, 'hide_empty' => false );

	if ( property_exists( WP_Term::class, $field ) ) {
		$query[ $field ] = $value;
	} else {
		$query['meta_query'] = array(
			array(
				'key'   => $field,
				'value' => $value,
			),
		);
	}

	$terms = get_terms( $query );

	if ( is_wp_error( $terms ) || is_int( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * @param $taxonomy
 * @param $field
 * @param $value
 *
 * @return null|WP_Term
 */
function fsi_get_term_by( $taxonomy, $field, $value ) {
	$result = (array) fsi_get_all_terms_by( $taxonomy, $field, $value );

	return current( $result );
}