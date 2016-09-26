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

if ( ! defined( 'FSI_THUMBNAIL_PATH' ) ) {
	define( 'FSI_THUMBNAIL_PATH', FSI_IMPORT_PATH );
}

foreach ( glob( __DIR__ . '/includes/*.php' ) as $item ) {
	require_once $item;
}