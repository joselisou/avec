<?php
/**
 * WP-CLI commands: `wp avec-clone import-wxr <path>` and `wp avec-clone clean`.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Avec_Clone_CLI_Commands.
 */
class Avec_Clone_CLI_Commands {

	/**
	 * Registers the `wp avec-clone` command family. Only called when WP_CLI is loaded.
	 */
	public static function init() {
		WP_CLI::add_command( 'avec-clone import-wxr', array( __CLASS__, 'import_wxr' ) );
		WP_CLI::add_command( 'avec-clone clean', array( __CLASS__, 'clean' ) );
	}

	/**
	 * Imports a WXR file.
	 *
	 * ## OPTIONS
	 *
	 * <path>
	 * : Absolute or relative path to the WXR (XML) file to import.
	 *
	 * ## EXAMPLES
	 *
	 *     wp avec-clone import-wxr ../../data/fake/avec-fake-dataset.xml
	 *
	 * @param array $args Positional arguments.
	 */
	public static function import_wxr( $args ) {
		list( $path ) = $args;

		try {
			$result = Avec_Clone_Importer::import_file( $path );
		} catch ( InvalidArgumentException $e ) {
			WP_CLI::error( $e->getMessage() );
			return;
		}

		WP_CLI::success(
			sprintf(
				'Imported %d posts (%d created, %d updated).',
				$result['total'],
				$result['created'],
				$result['updated']
			)
		);
	}

	/**
	 * Deletes every post previously imported by Avec Clone.
	 *
	 * ## EXAMPLES
	 *
	 *     wp avec-clone clean
	 */
	public static function clean() {
		$deleted = Avec_Clone_Cleaner::clean();
		WP_CLI::success( sprintf( 'Deleted %d posts.', $deleted ) );
	}
}
