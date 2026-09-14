<?php
/**
 * "Comissões" section — lists avec_recibo (per-service commission line item) posts, and the
 * total commission owed across all of them.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/nav.php';

$recibos = get_posts(
	array(
		'post_type'      => 'avec_recibo',
		'posts_per_page' => 100,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$total_comissao = 0;
foreach ( $recibos as $post ) {
	$valor           = (float) get_post_meta( $post->ID, '_avec_valor', true );
	$comissao        = (float) get_post_meta( $post->ID, '_avec_comissao', true );
	$total_comissao += $valor * ( $comissao / 100 );
}
?>
<main class="avec-clone-section avec-clone-section--comissoes">
	<h1><?php esc_html_e( 'Comissões', 'avec-clone' ); ?></h1>
	<p class="avec-clone-total">
		<?php
		printf(
			/* translators: %s: formatted currency total */
			esc_html__( 'Total de comissão: %s', 'avec-clone' ),
			esc_html( number_format_i18n( $total_comissao, 2 ) )
		);
		?>
	</p>

	<?php if ( ! $recibos ) : ?>
		<p><?php esc_html_e( 'Nenhum lançamento importado ainda.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<ul class="avec-clone-list">
			<?php
			foreach ( $recibos as $post ) :
				$valor    = (float) get_post_meta( $post->ID, '_avec_valor', true );
				$comissao = (float) get_post_meta( $post->ID, '_avec_comissao', true );
				?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?></span>
					<span class="avec-clone-card__meta"><?php echo esc_html( $comissao ); ?>%</span>
					<span class="avec-clone-card__value"><?php echo esc_html( number_format_i18n( $valor * ( $comissao / 100 ), 2 ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
