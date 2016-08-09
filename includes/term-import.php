<?php

/**
 * @param        $name
 * @param string $taxonomy
 *
 * @return bool|int
 */
function fsi_term_import( $name, $taxonomy = 'category', $args = array() ) {
	$term_id = fsi_term_resolve( $name, $taxonomy );

	if ( isset( $args['parent'] ) && $args['parent'] ) {
		// check if valid as parent
		$term = get_term( $args['parent'] );

		if ( is_wp_error( $term ) ) {
			// wp error => passthru
			throw new \Exception( $term->get_error_message() );
		}

		if ( $term->taxonomy != $taxonomy ) {
			throw new \DomainException(
				sprintf(
					'Entry "%s" ("%s") can not have %d as parent because it is a "%s".',
					$name,
					$taxonomy,
					$args['parent'],
					$term->taxonomy
				)
			);
		}
	}

	if ( $term_id ) {
		wp_update_term( $term_id, $taxonomy, $args );

		return $term_id;
	}

	$term_id = wp_insert_term( $name, $taxonomy, $args );

	if ( is_wp_error( $term_id ) ) {
		return false;
	}

	return $term_id['term_id'];
}