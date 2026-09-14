<?php
/**
 * Registers the taxonomies used to classify Avec Clone data.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Avec_Clone_Taxonomy_Registrar.
 */
class Avec_Clone_Taxonomy_Registrar {

	const TAXONOMIES = array(
		'agendamento_status' => 'avec_agendamento_status',
		'recibo_tipo'        => 'avec_recibo_tipo',
	);

	/**
	 * Hooks registration into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Registers every Avec Clone taxonomy. Terms themselves are created dynamically by the
	 * importer from the values actually seen in the source data, not declared here, since the
	 * Avec API doesn't expose a closed enum for status/type values.
	 */
	public static function register() {
		register_taxonomy(
			self::TAXONOMIES['agendamento_status'],
			array( Avec_Clone_CPT_Registrar::POST_TYPES['agendamento'] ),
			array(
				'label'        => __( 'Status do agendamento', 'avec-clone' ),
				'public'       => false,
				'show_ui'      => true,
				'hierarchical' => false,
			)
		);

		register_taxonomy(
			self::TAXONOMIES['recibo_tipo'],
			array( Avec_Clone_CPT_Registrar::POST_TYPES['recibo'] ),
			array(
				'label'        => __( 'Tipo de lançamento', 'avec-clone' ),
				'public'       => false,
				'show_ui'      => true,
				'hierarchical' => false,
			)
		);
	}
}
