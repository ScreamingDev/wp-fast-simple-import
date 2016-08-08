<?php

/**
 * @param $thumbnail_file
 *
 * @return \WP_Post
 */
function fsi_resolve_thumbnail_file( $thumbnail_file ) {
	/** @var \wpdb $wpdb */
	global $wpdb;

	// check via _wp_attached_file
	$result = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT post_id
FROM `' . $wpdb->postmeta . '`
WHERE meta_key = "_wp_attached_file"
AND meta_value = %s',
			$thumbnail_file
		)
	);

	foreach ( $result as $row ) {
		$attachment = get_post( $row->post_id );

		if ( is_wp_error( $attachment ) ) {
			continue;
		}

		if ( $attachment ) {
			return $attachment;
		}
	}

	// check via guid
	$result = $wpdb->get_results(
		$wpdb->prepare(
			'
				SELECT `ID`
				FROM ' . $wpdb->posts . '
				WHERE `guid` LIKE %s
			    AND `post_type` = "attachment"
			',
			[
				'%' . $thumbnail_file,
			]
		)
	);

	foreach ( $result as $row ) {
		$attachment = get_post( $row->ID );

		if ( is_wp_error( $attachment ) ) {
			continue;
		}

		if ( $attachment ) {
			return $attachment;
		}
	}

	return false;
}