<?php

$fsi_import_thumbnail_file_map = [ ];

function fsi_import_thumbnail( $thumbnail, $post_id = null ) {
	if ( is_string( $thumbnail ) && filter_var( $thumbnail, FILTER_VALIDATE_URL ) ) {
		// Thumbnail might be URL => Download file
		return fsi_import_thumbnail_url( $thumbnail, $post_id );
	}

	if ( is_string( $thumbnail ) && ! is_numeric( $thumbnail ) ) {
		return fsi_import_thumbnail_file( $thumbnail, $post_id );
	}
}

/**
 * @param string $thumbnail_file
 *
 * @return int
 */
function fsi_import_thumbnail_file( $thumbnail_file, $post_id = null ) {
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

	require_once ABSPATH . '/wp-admin/includes/image.php';
	$attach_data = wp_generate_attachment_metadata( $attachment_id, $thumbnail_file );
	wp_update_attachment_metadata( $attachment_id, $attach_data );

	if ( $post_id ) {
		set_post_thumbnail( $post_id, $attachment_id );
	}

	return $attachment_id;
}

function fsi_import_thumbnail_url( $thumbnail_file, $post_id = null ) {

	$basename    = basename( parse_url( $thumbnail_file, PHP_URL_PATH ) );
	$source_file = FSI_THUMBNAIL_PATH . '/' . $basename;

	if ( ! is_dir( FSI_THUMBNAIL_PATH ) ) {
		mkdir( FSI_THUMBNAIL_PATH, 0777, true );
	}

	if ( ! file_exists( $source_file ) ) {
		// not downloaded yet => fetch
		$data = wp_remote_get( $thumbnail_file );

		file_put_contents( $source_file, $data['body'] );
	}

	return fsi_import_thumbnail_file( $source_file, $post_id );
}
