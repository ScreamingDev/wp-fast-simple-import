<?php

$fsi_import_thumbnail_file_map = [ ];

function fsi_import_thumbnail( $post_id, $thumbnail ) {
	if ( is_string( $thumbnail ) && ! is_numeric( $thumbnail ) ) {
		return fsi_import_thumbnail_by_file( $post_id, $thumbnail );
	}
}

/**
 * @param string $thumbnail_file
 *
 * @return int
 */
function fsi_import_thumbnail_file( $thumbnail_file ) {
	/** @var \wpdb $wpdb */
	global $wpdb;

	$attachment = fsi_resolve_thumbnail_file( $thumbnail_file );

	if ( $attachment ) {
		return $attachment->ID;
	}

	$file_type = wp_check_filetype( $thumbnail_file );

	$attachment_id = wp_insert_attachment(
		[
			'post_title'     => basename( $thumbnail_file ),
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_mime_type' => $file_type['type'],
		],
		$thumbnail_file
	);

	return $attachment_id;
}