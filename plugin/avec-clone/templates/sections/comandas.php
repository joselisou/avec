<?php
/**
 * "Comandas" section — lists avec_comanda posts with their resolved client name.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/nav.php';

$avec_valid_date = function ( $value, $fallback ) {
	$value = is_string( $value ) ? $value : '';
	return preg_match( '/^\\d{4}-\\d{2}-\\d{2}$/', $value ) ? $value : $fallback;
};

$avec_filter_inicio = $avec_valid_date(
	isset( $_GET['inicio'] ) ? wp_unslash( $_GET['inicio'] ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	current_time( 'Y-m-01' )
);
$avec_filter_fim    = $avec_valid_date(
	isset( $_GET['fim'] ) ? wp_unslash( $_GET['fim'] ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	current_time( 'Y-m-d' )
);

$comandas = get_posts(
	array(
		'post_type'      => 'avec_comanda',
		'posts_per_page' => 50,
		'orderby'        => 'meta_value',
		'meta_key'       => '_avec_data',
		'order'          => 'DESC',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_avec_data',
				'value'   => array( $avec_filter_inicio, $avec_filter_fim ),
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			),
		),
	)
);
?>
<main class="avec-clone-section avec-clone-section--comandas">
	<?php require AVEC_CLONE_DIR . 'templates/partials/period-filter.php'; ?>

	<?php if ( ! $comandas ) : ?>
		<p><?php esc_html_e( 'Nenhuma comanda importada ainda.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<ul class="avec-clone-list">
			<?php
			foreach ( $comandas as $post ) :
				$cliente_post_id = (int) get_post_meta( $post->ID, '_avec_cliente_post_id', true );
				$cliente_nome    = $cliente_post_id ? get_the_title( $cliente_post_id ) : __( 'Cliente não identificado', 'avec-clone' );
				?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__date"><?php echo esc_html( get_post_meta( $post->ID, '_avec_data', true ) ); ?></span>
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?> — <?php echo esc_html( $cliente_nome ); ?></span>
					<span class="avec-clone-card__value"><?php echo esc_html( Avec_Clone_Formatting::money( get_post_meta( $post->ID, '_avec_total', true ) ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
