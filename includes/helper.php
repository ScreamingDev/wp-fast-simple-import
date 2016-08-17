<?php

/**
 * Use data while SQL fetches them.
 *
 * WordPress fetches the data from SQL and store them in the PHP context.
 * Due to that you are unable to yield data out of the data flow and continue to work.
 * Instead you have to wait for the data to be ready.
 * Therefor the \wpdb is replaced with PDO here.
 *
 * @param       $query
 * @param array $args
 *
 * @return Generator
 */
function fsi_query( $query, $args = [ ] ) {
	$pdo = fsi_pdo();

	$stmt = $pdo->query( $query );

	if ( ! $args ) {
		$args = null;
	}

	$stmt->execute( $args );

	while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
		yield $row;
	}
}

function fsi_pdo() {
	/** @var \PDO $pdo */
	static $pdo;

	if ( $pdo ) {
		return $pdo;
	}

	$pdo = new PDO(
		'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST,
		DB_USER,
		DB_PASSWORD,
		array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET )
	);

	return $pdo;
}

function fsi_enable_all_caps() {
	add_filter(
		'user_has_cap',
		function ( $allcaps, $caps, $args, $wp_user ) {
			$allcaps[ $args[0] ] = true;

			var_dump( $args[0] );

			return $allcaps;
		},
		10,
		4
	);
}

/**
 * @param string[] $cap_array
 */
function fsi_enable_caps( $cap_array ) {
	$cap_array = (array) $cap_array;

	// do not register the filter multiple times for the same cap
	static $enabled_caps = array();
	$cap_array    = array_diff( $cap_array, $enabled_caps );

	if ( ! $cap_array ) {
		return;
	}

	$enabled_caps = array_merge( $enabled_caps, $cap_array );

	add_filter(
		'user_has_cap',
		function ( $allcaps ) use ( $cap_array ) {
			foreach ( $cap_array as $cap_name ) {
				$allcaps[ $cap_name ] = true;
			}

			return $allcaps;
		}
	);
}