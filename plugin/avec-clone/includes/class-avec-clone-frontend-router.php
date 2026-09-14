<?php
/**
 * Front-end "Minha Conta" routing — mirrors WooCommerce My Account's pattern of one base route
 * plus rewrite endpoints per section, without requiring WooCommerce itself.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Avec_Clone_Frontend_Router.
 */
class Avec_Clone_Frontend_Router {

	const BASE_SLUG = 'minha-conta';

	/**
	 * Sections and the template file (under templates/sections/) that renders each one.
	 *
	 * @var array<string, string>
	 */
	const SECTIONS = array(
		'agenda'      => 'agenda.php',
		'comandas'    => 'comandas.php',
		'comissoes'   => 'comissoes.php',
		'clientes'    => 'clientes.php',
		'vale-rapido' => 'vale-rapido.php',
	);

	const DEFAULT_SECTION = 'agenda';

	/**
	 * Hooks routing into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_endpoints' ) );
		add_filter( 'query_vars', array( __CLASS__, 'register_query_vars' ) );
		add_filter( 'show_admin_bar', array( __CLASS__, 'hide_admin_bar_on_our_routes' ) );
		add_filter( 'template_include', array( __CLASS__, 'maybe_render' ) );
	}

	/**
	 * Registers the rewrite rules for `/minha-conta/` and `/minha-conta/{section}/`.
	 */
	public static function register_endpoints() {
		add_rewrite_rule(
			'^' . self::BASE_SLUG . '/([^/]+)/?$',
			'index.php?avec_account=1&avec_account_section=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^' . self::BASE_SLUG . '/?$',
			'index.php?avec_account=1&avec_account_section=' . self::DEFAULT_SECTION,
			'top'
		);
	}

	/**
	 * Declares the query vars our rewrite rules feed into `index.php`.
	 *
	 * @param string[] $vars Existing public query vars.
	 * @return string[]
	 */
	public static function register_query_vars( $vars ) {
		$vars[] = 'avec_account';
		$vars[] = 'avec_account_section';
		return $vars;
	}

	/**
	 * Hides the wp-admin toolbar on our routes — the real Avec Pro app is full-screen with no
	 * host-site chrome, and the query vars this decision would otherwise rely on aren't parsed
	 * yet this early, so the request URI is checked directly instead.
	 *
	 * @param bool $show Whether WordPress currently intends to show the admin bar.
	 * @return bool
	 */
	public static function hide_admin_bar_on_our_routes( $show ) {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';

		if ( $path && 0 === strpos( $path, '/' . self::BASE_SLUG ) ) {
			return false;
		}

		return $show;
	}

	/**
	 * If the current request is one of our routes, short-circuits WordPress's own template
	 * selection and renders our own PHP template instead (login form or the requested section).
	 *
	 * @param string $template The template WordPress would otherwise load.
	 * @return string
	 */
	public static function maybe_render( $template ) {
		if ( ! get_query_var( 'avec_account' ) ) {
			return $template;
		}

		if ( ! Avec_Clone_Auth_Gate::is_authorized() ) {
			return AVEC_CLONE_DIR . 'templates/login.php';
		}

		$section                               = get_query_var( 'avec_account_section' );
		$section_file                          = isset( self::SECTIONS[ $section ] ) ? self::SECTIONS[ $section ] : self::SECTIONS[ self::DEFAULT_SECTION ];
		$GLOBALS['avec_clone_current_section'] = isset( self::SECTIONS[ $section ] ) ? $section : self::DEFAULT_SECTION;

		return AVEC_CLONE_DIR . 'templates/sections/' . $section_file;
	}

	/**
	 * Builds the front-end URL for a given section, e.g. `home_url('/minha-conta/comandas/')`.
	 *
	 * @param string $section One of the {@see self::SECTIONS} keys.
	 * @return string
	 */
	public static function url_for( $section ) {
		return home_url( '/' . self::BASE_SLUG . '/' . $section . '/' );
	}
}
