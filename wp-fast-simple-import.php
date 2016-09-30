<?php
/*
Plugin Name: Fast Simple Import
Text Domain: rmp_fastsimpleimport
Domain Path: /languages
Description: Easily import data.
Author: Mike Pretzlaw
Author URI: http://mike-pretzlaw.de
Plugin URI: http://mike-pretzlaw.de
Version: 4.1.2
*/

if ( ! defined( 'FSI_IMPORT_PATH' ) ) {
	define( 'FSI_IMPORT_PATH', WP_CONTENT_DIR . '/import' );
}

$thumb_path  = FSI_IMPORT_PATH;
$upload_path = wp_upload_dir();
if ( false == $upload_path['error'] ) {
	$thumb_path = $upload_path['basedir'];
}

if ( ! defined( 'FSI_THUMBNAIL_PATH' ) ) {
	define( 'FSI_THUMBNAIL_PATH', $thumb_path );
}

foreach ( glob( __DIR__ . '/includes/*.php' ) as $item ) {
	require_once $item;
}
