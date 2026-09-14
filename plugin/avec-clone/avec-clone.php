<?php
/**
 * Plugin Name:       Avec Clone
 * Description:       Clona os dados e a UI funcional do painel Avec Pro (agenda, comandas, comissões, clientes) via import WXR em Custom Post Types.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Avec Clone
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       avec-clone
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'AVEC_CLONE_VERSION', '0.1.0' );
define( 'AVEC_CLONE_FILE', __FILE__ );
define( 'AVEC_CLONE_DIR', plugin_dir_path( __FILE__ ) );
define( 'AVEC_CLONE_URL', plugin_dir_url( __FILE__ ) );

require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-cpt-registrar.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-taxonomy-registrar.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-importer.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-cleaner.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-admin-page.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-cli-commands.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-auth-gate.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-frontend-router.php';
require_once AVEC_CLONE_DIR . 'includes/class-avec-clone-assets.php';

/**
 * Boots every plugin subsystem. Kept as plain function calls (no container/DI) since the plugin
 * is small and each class only needs `add_action`/`add_filter` wiring, not shared state.
 */
function avec_clone_bootstrap() {
	Avec_Clone_CPT_Registrar::init();
	Avec_Clone_Taxonomy_Registrar::init();
	Avec_Clone_Importer::init();
	Avec_Clone_Cleaner::init();
	Avec_Clone_Admin_Page::init();
	Avec_Clone_Auth_Gate::init();
	Avec_Clone_Frontend_Router::init();
	Avec_Clone_Assets::init();

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		Avec_Clone_CLI_Commands::init();
	}
}
add_action( 'plugins_loaded', 'avec_clone_bootstrap' );

/**
 * Registers CPTs/taxonomies and flushes rewrite rules so their archives/endpoints work immediately.
 */
function avec_clone_activate() {
	Avec_Clone_CPT_Registrar::register();
	Avec_Clone_Taxonomy_Registrar::register();
	Avec_Clone_Frontend_Router::register_endpoints();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'avec_clone_activate' );

/**
 * Deactivation only flushes rewrite rules — imported data is never deleted implicitly.
 * Use the admin "Limpar dados" action (or `wp avec-clone clean`) to remove it explicitly.
 */
function avec_clone_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'avec_clone_deactivate' );
