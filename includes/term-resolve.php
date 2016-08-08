<?php

function fsi_term_resolve( $name, $taxonomy = 'category' ) {
	$term = get_term_by( 'name', $name, $taxonomy );

	if ( $term ) {
		return $term->term_id;
	}

	return false;
}