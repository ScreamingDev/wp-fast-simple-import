<?php

/**
 * @param        $name
 * @param string $taxonomy
 *
 * @return bool|int
 */
function fsi_term_import( $name, $taxonomy = 'category' ) {
	$term_id = fsi_term_resolve( $name, $taxonomy );

	if ( $term_id ) {
		return $term_id;
	}

	$term_id = wp_insert_term( $name, $taxonomy );

	if ( is_wp_error( $term_id ) ) {
		return false;
	}

	return $term_id['term_id'];
}