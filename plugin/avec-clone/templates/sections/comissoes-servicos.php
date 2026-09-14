<?php
/**
 * "Serviços" detail — the real app opens this as its own screen (back arrow instead of the
 * hamburger/drawer) when the Comissões "Serviços" panel is tapped, rather than expanding inline.
 * Lists every day in the current período as its own card (date + produção/rateio), each
 * expandable to the day's individual service line items.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		'posts_per_page' => 200, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- a full período's worth of line items, not a paginated listing.
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

$avec_by_day = array();
foreach ( $recibos as $post ) {
	$valor    = (float) get_post_meta( $post->ID, '_avec_valor', true );
	$comissao = (float) get_post_meta( $post->ID, '_avec_comissao', true );
	$dia      = get_the_date( 'Y-m-d', $post );

	if ( ! isset( $avec_by_day[ $dia ] ) ) {
		$avec_by_day[ $dia ] = array(
			'producao' => 0.0,
			'rateio'   => 0.0,
			'itens'    => array(),
		);
	}
	$avec_by_day[ $dia ]['producao'] += $valor;
	$avec_by_day[ $dia ]['rateio']   += $valor * ( $comissao / 100 );
	$avec_by_day[ $dia ]['itens'][]   = $post;
}
krsort( $avec_by_day );

$money           = array( 'Avec_Clone_Formatting', 'money' );
$avec_back_url   = add_query_arg(
	array(
		'inicio' => $avec_filter_inicio,
		'fim'    => $avec_filter_fim,
		'recibo' => $avec_filter_recibo,
	),
	Avec_Clone_Frontend_Router::url_for( 'comissoes' )
);
$avec_back_title = __( 'Serviços', 'avec-clone' );

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/partials/back-topbar.php';
?>
<main class="avec-clone-section avec-clone-section--comissoes-servicos">
	<?php if ( ! $avec_by_day ) : ?>
		<p><?php esc_html_e( 'Nenhum serviço no período selecionado.', 'avec-clone' ); ?></p>
	<?php endif; ?>
	<?php foreach ( $avec_by_day as $avec_dia => $avec_day_data ) : ?>
		<div class="avec-clone-panel" data-avec-panel-toggle>
			<div class="avec-clone-panel__header">
				<div class="avec-clone-panel__body">
					<span class="avec-clone-panel__day-date"><?php echo esc_html( gmdate( 'd/m', strtotime( $avec_dia ) ) ); ?></span>
					<div class="avec-clone-panel__columns">
						<div class="avec-clone-panel__column">
							<span class="avec-clone-panel__column-label"><?php esc_html_e( 'produção', 'avec-clone' ); ?></span>
							<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( $avec_day_data['producao'] ) ); ?></span>
						</div>
						<div class="avec-clone-panel__column">
							<span class="avec-clone-panel__column-label"><?php esc_html_e( 'rateio', 'avec-clone' ); ?></span>
							<span class="avec-clone-panel__column-value"><?php echo esc_html( $money( $avec_day_data['rateio'] ) ); ?></span>
						</div>
					</div>
				</div>
				<span class="avec-clone-panel__chevron" aria-hidden="true">&rsaquo;</span>
			</div>
			<div class="avec-clone-panel__collapse" hidden>
				<ul class="avec-clone-list">
					<?php foreach ( $avec_day_data['itens'] as $avec_item ) : ?>
						<?php
						$avec_valor        = (float) get_post_meta( $avec_item->ID, '_avec_valor', true );
						$avec_comissao     = (float) get_post_meta( $avec_item->ID, '_avec_comissao', true );
						$avec_comanda_id   = (int) get_post_meta( $avec_item->ID, '_avec_comanda_post_id', true );
						$avec_numero       = $avec_comanda_id ? get_post_meta( $avec_comanda_id, '_avec_numero', true ) : '';
						$avec_cliente_id   = $avec_comanda_id ? (int) get_post_meta( $avec_comanda_id, '_avec_cliente_post_id', true ) : 0;
						$avec_cliente_nome = $avec_cliente_id ? get_the_title( $avec_cliente_id ) : __( 'Cliente não identificado', 'avec-clone' );
						?>
						<li class="avec-clone-card avec-clone-card--servico">
							<span class="avec-clone-card__meta">
								<?php
								printf(
									/* translators: 1: comanda number, 2: date */
									esc_html__( 'Nº %1$s %2$s', 'avec-clone' ),
									esc_html( $avec_numero ? $avec_numero : '—' ),
									esc_html( gmdate( 'd/m/Y', strtotime( $avec_dia ) ) )
								);
								?>
							</span>
							<span class="avec-clone-card__title"><?php echo esc_html( $avec_item->post_title ); ?></span>
							<span class="avec-clone-card__meta">
								<?php
								printf(
									/* translators: %s: client name */
									esc_html__( 'Cliente: %s', 'avec-clone' ),
									esc_html( $avec_cliente_nome )
								);
								?>
							</span>
							<span class="avec-clone-card__meta">
								<?php
								printf(
									/* translators: %s: formatted currency value */
									esc_html__( 'Valor: %s', 'avec-clone' ),
									esc_html( $money( $avec_valor ) )
								);
								?>
							</span>
							<span class="avec-clone-card__value">
								<?php
								printf(
									/* translators: 1: formatted commission value, 2: commission percentage */
									esc_html__( 'Comissão: %1$s (%2$s%%)', 'avec-clone' ),
									esc_html( $money( $avec_valor * ( $avec_comissao / 100 ) ) ),
									esc_html( $avec_comissao )
								);
								?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endforeach; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
