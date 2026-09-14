<?php
/**
 * Enqueues the compiled front-end assets (assets/build/index.css, assets/build/index.js) only on
 * the plugin's own `/minha-conta/*` routes and its login screen — never site-wide.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Avec_Clone_Assets.
 */
class Avec_Clone_Assets {

	/**
	 * Hooks asset enqueueing into WordPress.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
	}

	/**
	 * Enqueues the built CSS/JS bundle when the current request is one of our front-end routes.
	 */
	public static function maybe_enqueue() {
		if ( ! get_query_var( 'avec_account' ) ) {
			return;
		}

		$asset_file = AVEC_CLONE_DIR . 'assets/build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return; // Front-end assets haven't been built yet (`npm run build`).
		}

		$asset = require $asset_file;

		wp_enqueue_style( 'avec-clone', AVEC_CLONE_URL . 'assets/build/style-index.css', array(), $asset['version'] );
		wp_enqueue_script( 'avec-clone', AVEC_CLONE_URL . 'assets/build/index.js', $asset['dependencies'], $asset['version'], true );
	}
}
