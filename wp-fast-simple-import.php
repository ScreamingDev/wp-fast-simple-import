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

foreach ( glob( __DIR__ . '/includes/*.php' ) as $item ) {
	require_once $item;
}