<?php
/**
 * PHPUnit bootstrap — loads the WP core test suite (provided by wp-env's tests-cli container)
 * and this plugin, following the standard wp-cli `scaffold plugin-tests` layout.
 *
 * @package AvecClone
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Loads the plugin under test.
 */
function _avec_clone_manually_load_plugin() {
	require dirname( __DIR__ ) . '/avec-clone.php';
}
tests_add_filter( 'muplugins_loaded', '_avec_clone_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
