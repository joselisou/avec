<?php
/**
 * "Comandas" section — lists avec_comanda posts with their resolved client name.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
require AVEC_CLONE_DIR . 'templates/nav.php';

$comandas = get_posts(
	array(
		'post_type'      => 'avec_comanda',
		'posts_per_page' => 50,
		'orderby'        => 'meta_value',
		'meta_key'       => '_avec_data',
		'order'          => 'DESC',
	)
);
?>
<main class="avec-clone-section avec-clone-section--comandas">
	<h1><?php esc_html_e( 'Comandas', 'avec-clone' ); ?></h1>

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
					<span class="avec-clone-card__value"><?php echo esc_html( number_format_i18n( (float) get_post_meta( $post->ID, '_avec_total', true ), 2 ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
get_footer();
