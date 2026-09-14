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
$avec_filter_recibo = isset( $_GET['recibo'] ) && 'nao_pago' === $_GET['recibo'] ? 'nao_pago' : 'todos'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$recibos = get_posts(
	array(
		'post_type'      => 'avec_recibo',
		'posts_per_page' => 100,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'date_query'     => array(
			array(
				'after'     => $avec_filter_inicio,
				'before'    => $avec_filter_fim,
				'inclusive' => true,
			),
		),
		'meta_query'     => 'nao_pago' === $avec_filter_recibo // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			? array(
				array(
					'key'   => '_avec_status',
					'value' => '0',
				),
			)
			: array(),
	)
);

$producao    = 0.0;
$rateio      = 0.0;
$avec_by_day = array();
foreach ( $recibos as $post ) {
	$valor     = (float) get_post_meta( $post->ID, '_avec_valor', true );
	$comissao  = (float) get_post_meta( $post->ID, '_avec_comissao', true );
	$producao += $valor;
	$rateio   += $valor * ( $comissao / 100 );

	$dia = get_the_date( 'Y-m-d', $post );
	if ( ! isset( $avec_by_day[ $dia ] ) ) {
		$avec_by_day[ $dia ] = array(
			'producao' => 0.0,
			'rateio'   => 0.0,
		);
	}
	$avec_by_day[ $dia ]['producao'] += $valor;
	$avec_by_day[ $dia ]['rateio']   += $valor * ( $comissao / 100 );
}
krsort( $avec_by_day );

$money = array( 'Avec_Clone_Formatting', 'money' );

// Material Symbols "content_cut" / "money_off" (Apache 2.0, Google) — the same open icon set
// the source app itself uses here, so this matches its visual language without copying any of
// its own drawn assets.
$icon_scissors = '<svg class="avec-clone-panel__icon-svg" viewBox="0 0 25.792 25.792" aria-hidden="true"><path d="M21.917 1.271h3.879v1.275l-9.021 9.083-2.6-2.6Zm-9.021 12.275a.68.68 0 0 0 .48-.186.6.6 0 0 0 .2-.465.69.69 0 0 0-.682-.682.6.6 0 0 0-.465.2.68.68 0 0 0-.186.48.636.636 0 0 0 .651.651Zm-7.75 9.7a2.53 2.53 0 0 0 1.829-.76 2.58 2.58 0 0 0 0-3.689 2.58 2.58 0 0 0-3.658 0 2.58 2.58 0 0 0 0 3.689 2.53 2.53 0 0 0 1.829.76m0-15.5a2.53 2.53 0 0 0 1.829-.759 2.58 2.58 0 0 0 0-3.689 2.58 2.58 0 0 0-3.658 0 2.58 2.58 0 0 0 0 3.689 2.53 2.53 0 0 0 1.829.759m4.712-.5 15.938 16v1.271h-3.879L12.896 15.5l-3.038 3.038a4.8 4.8 0 0 1 .438 2.108 4.96 4.96 0 0 1-.7 2.573 5.26 5.26 0 0 1-1.875 1.875 5.1 5.1 0 0 1-5.146 0 5.26 5.26 0 0 1-1.879-1.875 5.1 5.1 0 0 1 0-5.146 5.26 5.26 0 0 1 1.875-1.875 4.96 4.96 0 0 1 2.573-.7 4.8 4.8 0 0 1 2.108.434l3.044-3.036-3.038-3.038a4.8 4.8 0 0 1-2.108.434 4.96 4.96 0 0 1-2.573-.7A5.26 5.26 0 0 1 .696 7.719a5.1 5.1 0 0 1 0-5.146A5.26 5.26 0 0 1 2.571.698a5.1 5.1 0 0 1 5.146 0 5.26 5.26 0 0 1 1.879 1.875 4.96 4.96 0 0 1 .7 2.573 4.8 4.8 0 0 1-.438 2.108Z"/></svg>';
$icon_money    = '<svg class="avec-clone-panel__icon-svg" viewBox="0 0 25.08 28.5" aria-hidden="true"><path d="M2.014 1.71 25.08 24.852l-2.014 2.014-3.458-3.572a7.7 7.7 0 0 1-3.876 1.786v3.42h-4.75v-3.42a8.14 8.14 0 0 1-3.99-1.976A5.98 5.98 0 0 1 5.13 19h3.5a2.99 2.99 0 0 0 1.17 2.356 5.66 5.66 0 0 0 3.572.988 6.1 6.1 0 0 0 2.47-.456 3.1 3.1 0 0 0 1.292-1.026l-5.548-5.51a10.9 10.9 0 0 1-4.484-2.318A5.12 5.12 0 0 1 5.43 9.12L0 3.724Zm11.362 4.446a6.1 6.1 0 0 0-2.47.456l-2.28-2.318a11.4 11.4 0 0 1 2.356-.874V0h4.75v3.5a6.6 6.6 0 0 1 3.762 2.28 6.24 6.24 0 0 1 1.292 3.72h-3.5a3.55 3.55 0 0 0-.912-2.432 4.3 4.3 0 0 0-2.998-.912" /></svg>';
?>
<main class="avec-clone-section avec-clone-section--comissoes">
	<?php require AVEC_CLONE_DIR . 'templates/partials/period-filter.php'; ?>
	<div class="avec-clone-filterbar__recibo">
		<label for="avec-clone-recibo-filter"><?php esc_html_e( 'Filtrar por recibo:', 'avec-clone' ); ?></label>
		<select id="avec-clone-recibo-filter" data-avec-query-select="recibo">
			<option value="todos" <?php selected( $avec_filter_recibo, 'todos' ); ?>><?php esc_html_e( 'Todos', 'avec-clone' ); ?></option>
			<option value="nao_pago" <?php selected( $avec_filter_recibo, 'nao_pago' ); ?>><?php esc_html_e( 'Não pago', 'avec-clone' ); ?></option>
		</select>
	</div>

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

	<div class="avec-clone-panel" data-avec-panel-toggle>
		<div class="avec-clone-panel__header">
			<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Serviços', 'avec-clone' ); ?></h2>
			<div class="avec-clone-panel__body">
				<span class="avec-clone-panel__icon"><?php echo $icon_scissors; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, hand-written SVG constant. ?></span>
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
			<span class="avec-clone-panel__chevron" aria-hidden="true">&rsaquo;</span>
		</div>
		<div class="avec-clone-panel__collapse" hidden>
			<?php if ( $avec_by_day ) : ?>
				<ul class="avec-clone-panel__day-list">
					<?php foreach ( $avec_by_day as $avec_dia => $avec_totais ) : ?>
						<li class="avec-clone-panel__day-row">
							<span class="avec-clone-panel__day-date"><?php echo esc_html( gmdate( 'd/m/Y', strtotime( $avec_dia ) ) ); ?></span>
							<span class="avec-clone-panel__day-value"><?php echo esc_html( $money( $avec_totais['producao'] ) ); ?></span>
							<span class="avec-clone-panel__day-value"><?php echo esc_html( $money( $avec_totais['rateio'] ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'Nenhum serviço no período selecionado.', 'avec-clone' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="avec-clone-panel" data-avec-panel-toggle>
		<div class="avec-clone-panel__header">
			<span class="avec-clone-panel__chevron" aria-hidden="true">&rsaquo;</span>
			<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Descontos e Bônus', 'avec-clone' ); ?></h2>
		</div>
		<div class="avec-clone-panel__collapse" hidden>
			<div class="avec-clone-panel__body">
				<span class="avec-clone-panel__icon"><?php echo $icon_money; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, hand-written SVG constant. ?></span>
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
