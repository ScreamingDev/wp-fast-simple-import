<?php

function fsi_term_meta_update( $term_id, $data ) {
	foreach ( $data as $meta_key => $meta_value ) {
		update_term_meta( $term_id, $meta_key, $meta_value );
	}
}

function fsi_term_meta_replace( $term_id, $data ) {
	$meta_keys = array_keys( get_term_meta( $term_id ) );

	foreach ( $meta_keys as $meta_key ) {
		delete_term_meta( $term_id, $meta_key );
	}

	fsi_term_meta_update( $term_id, $data );
}