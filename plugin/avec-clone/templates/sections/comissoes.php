<?php
/**
 * "Comissões" section — mirrors the real Avec Pro report layout: a totals panel, then a
 * "Serviços" panel (produção/rateio) and a "Descontos e Bônus" panel, each as its own card with
 * a two-column stat pair. The per-service line items (avec_recibo posts) are listed below as
 * the detail behind those totals.
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

$producao = 0.0;
$rateio   = 0.0;
foreach ( $recibos as $post ) {
	$valor     = (float) get_post_meta( $post->ID, '_avec_valor', true );
	$comissao  = (float) get_post_meta( $post->ID, '_avec_comissao', true );
	$producao += $valor;
	$rateio   += $valor * ( $comissao / 100 );
}

$money = static function ( $value ) {
	return 'R$ ' . number_format_i18n( $value, 2 );
};
?>
<main class="avec-clone-section avec-clone-section--comissoes">
	<h1><?php esc_html_e( 'Comissões', 'avec-clone' ); ?></h1>

	<div class="avec-clone-panel avec-clone-panel--totals">
		<div class="avec-clone-panel__stat-row">
			<span class="avec-clone-panel__label"><?php esc_html_e( 'Total Comissão:', 'avec-clone' ); ?></span>
			<strong><?php echo esc_html( $money( $rateio ) ); ?></strong>
		</div>
		<div class="avec-clone-panel__stat-row">
			<span class="avec-clone-panel__label"><?php esc_html_e( 'Total a Receber:', 'avec-clone' ); ?></span>
			<strong><?php echo esc_html( $money( $rateio ) ); ?></strong>
		</div>
	</div>

	<div class="avec-clone-panel">
		<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Serviços', 'avec-clone' ); ?></h2>
		<div class="avec-clone-panel__columns">
			<div class="avec-clone-panel__column">
				<span class="avec-clone-panel__column-label"><?php esc_html_e( 'produção', 'avec-clone' ); ?></span>
				<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( $producao ) ); ?></span>
			</div>
			<div class="avec-clone-panel__column">
				<span class="avec-clone-panel__column-label"><?php esc_html_e( 'rateio', 'avec-clone' ); ?></span>
				<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( $rateio ) ); ?></span>
			</div>
		</div>
	</div>

	<div class="avec-clone-panel">
		<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Descontos e Bônus', 'avec-clone' ); ?></h2>
		<div class="avec-clone-panel__columns">
			<div class="avec-clone-panel__column">
				<span class="avec-clone-panel__column-label"><?php esc_html_e( 'descontos', 'avec-clone' ); ?></span>
				<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( 0 ) ); ?></span>
			</div>
			<div class="avec-clone-panel__column">
				<span class="avec-clone-panel__column-label"><?php esc_html_e( 'bônus', 'avec-clone' ); ?></span>
				<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( 0 ) ); ?></span>
			</div>
		</div>
	</div>

	<?php if ( $recibos ) : ?>
		<h2 class="avec-clone-panel__title avec-clone-panel__title--detail"><?php esc_html_e( 'Lançamentos', 'avec-clone' ); ?></h2>
		<ul class="avec-clone-list">
			<?php
			foreach ( $recibos as $post ) :
				$valor    = (float) get_post_meta( $post->ID, '_avec_valor', true );
				$comissao = (float) get_post_meta( $post->ID, '_avec_comissao', true );
				?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?></span>
					<span class="avec-clone-card__meta"><?php echo esc_html( $comissao ); ?>%</span>
					<span class="avec-clone-card__value"><?php echo esc_html( $money( $valor * ( $comissao / 100 ) ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p><?php esc_html_e( 'Nenhum lançamento importado ainda.', 'avec-clone' ); ?></p>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
