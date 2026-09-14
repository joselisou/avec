<?php
/**
 * Registers the Custom Post Types that hold data imported from Avec Pro.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Avec_Clone_CPT_Registrar.
 */
class Avec_Clone_CPT_Registrar {

	/**
	 * Post type slugs, keyed by a short internal name used elsewhere in the plugin.
	 *
	 * @var array<string, string>
	 */
	const POST_TYPES = array(
		'cliente'     => 'avec_cliente',
		'comanda'     => 'avec_comanda',
		'agendamento' => 'avec_agendamento',
		'recibo'      => 'avec_recibo',
		'vale_rapido' => 'avec_vale_rapido',
	);

	/**
	 * Hooks registration into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Registers every Avec Clone post type. Safe to call directly (e.g. on activation) in
	 * addition to the `init` hook, since `register_post_type` is idempotent per request.
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPES['cliente'],
			self::args(
				__( 'Clientes', 'avec-clone' ),
				__( 'Cliente', 'avec-clone' ),
				'dashicons-admin-users'
			)
		);

		register_post_type(
			self::POST_TYPES['comanda'],
			self::args(
				__( 'Comandas', 'avec-clone' ),
				__( 'Comanda', 'avec-clone' ),
				'dashicons-media-spreadsheet'
			)
		);

		register_post_type(
			self::POST_TYPES['agendamento'],
			self::args(
				__( 'Agendamentos', 'avec-clone' ),
				__( 'Agendamento', 'avec-clone' ),
				'dashicons-calendar-alt'
			)
		);

		register_post_type(
			self::POST_TYPES['recibo'],
			self::args(
				__( 'Lançamentos de Comissão', 'avec-clone' ),
				__( 'Lançamento', 'avec-clone' ),
				'dashicons-money-alt'
			)
		);

		register_post_type(
			self::POST_TYPES['vale_rapido'],
			self::args(
				__( 'Vale Rápido', 'avec-clone' ),
				__( 'Vale Rápido', 'avec-clone' ),
				'dashicons-tickets-alt'
			)
		);
	}

	/**
	 * Builds the shared `register_post_type` args for an Avec Clone CPT.
	 *
	 * @param string $plural_label   Plural label shown in the admin menu.
	 * @param string $singular_label Singular label used in "Add New X" etc.
	 * @param string $menu_icon      Dashicon slug for the admin menu.
	 * @return array<string, mixed>
	 */
	private static function args( $plural_label, $singular_label, $menu_icon ) {
		return array(
			'label'           => $plural_label,
			'labels'          => array(
				'name'          => $plural_label,
				'singular_name' => $singular_label,
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'avec-clone',
			'supports'        => array( 'title', 'custom-fields' ),
			'menu_icon'       => $menu_icon,
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		);
	}
}
