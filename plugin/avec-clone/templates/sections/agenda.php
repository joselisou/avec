<?php
/**
 * "Agenda" section — a single-day calendar grid (like the real Avec Pro agenda: a time-slot
 * column plus a schedule column with colored appointment blocks positioned/sized by duration),
 * not a flat list. See the "Auditoria visual por tela" section of the project plan for the
 * measurements this is based on (44px per 30-minute row, block colors, etc.).
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/nav.php';

const AVEC_CLONE_AGENDA_ROW_HEIGHT = 44;
const AVEC_CLONE_AGENDA_START_HOUR = 9;
const AVEC_CLONE_AGENDA_END_HOUR   = 20;

$selected_date = isset( $_GET['data'] ) ? sanitize_text_field( wp_unslash( $_GET['data'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ! $selected_date || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $selected_date ) ) {
	$selected_date = current_time( 'Y-m-d' );
}

$dias_semana    = array(
	'Sunday'    => 'Domingo',
	'Monday'    => 'Segunda-feira',
	'Tuesday'   => 'Terça-feira',
	'Wednesday' => 'Quarta-feira',
	'Thursday'  => 'Quinta-feira',
	'Friday'    => 'Sexta-feira',
	'Saturday'  => 'Sábado',
);
$timestamp      = strtotime( $selected_date );
$dia_semana_en  = gmdate( 'l', $timestamp );
$dia_semana_pt  = isset( $dias_semana[ $dia_semana_en ] ) ? $dias_semana[ $dia_semana_en ] : $dia_semana_en;
$data_formatada = gmdate( 'd/m/Y', $timestamp );

$agendamentos = get_posts(
	array(
		'post_type'      => 'avec_agendamento',
		'posts_per_page' => -1,
		'meta_key'       => '_avec_data',
		'meta_value'     => $selected_date,
	)
);

$blocos = array();
foreach ( $agendamentos as $post ) {
	$hora_inicio = (int) get_post_meta( $post->ID, '_avec_hora_inicio', true );
	$hora_fim    = (int) get_post_meta( $post->ID, '_avec_hora_fim', true );
	$cliente_id  = (int) get_post_meta( $post->ID, '_avec_cliente_post_id', true );
	$cliente     = $cliente_id ? get_the_title( $cliente_id ) : __( 'Cliente não identificado', 'avec-clone' );
	$servico     = get_post_meta( $post->ID, '_avec_servico', true );

	$grid_start  = AVEC_CLONE_AGENDA_START_HOUR * 60;
	$top         = max( 0, ( $hora_inicio - $grid_start ) / 30 * AVEC_CLONE_AGENDA_ROW_HEIGHT );
	$height      = max( 1, ( $hora_fim - $hora_inicio ) / 30 * AVEC_CLONE_AGENDA_ROW_HEIGHT );
	$is_bloqueio = false !== stripos( $cliente, 'bloque' ) || false !== stripos( $cliente, 'folga' ) || false !== stripos( $cliente, 'almoco' ) || false !== stripos( $cliente, 'almoço' );

	$blocos[] = array(
		'top'      => $top,
		'height'   => $height,
		'cliente'  => $cliente,
		'servico'  => $servico,
		'bloqueio' => $is_bloqueio,
	);
}

$total_rows  = ( AVEC_CLONE_AGENDA_END_HOUR - AVEC_CLONE_AGENDA_START_HOUR ) * 2 + 1;
$grid_height = $total_rows * AVEC_CLONE_AGENDA_ROW_HEIGHT;
?>
<main class="avec-clone-section avec-clone-section--agenda">
	<div class="avec-clone-agenda__header">
		<span class="avec-clone-agenda__date-label"><?php echo esc_html( "{$dia_semana_pt} - {$data_formatada}" ); ?></span>
		<form method="get" class="avec-clone-agenda__date-picker">
			<input type="date" name="data" value="<?php echo esc_attr( $selected_date ); ?>" onchange="this.form.submit()" aria-label="<?php esc_attr_e( 'Escolher data', 'avec-clone' ); ?>" />
		</form>
	</div>

	<div class="avec-clone-agenda__grid" style="height: <?php echo (int) $grid_height; ?>px;">
		<?php for ( $minutes = AVEC_CLONE_AGENDA_START_HOUR * 60; $minutes <= AVEC_CLONE_AGENDA_END_HOUR * 60; $minutes += 30 ) : ?>
			<div class="avec-clone-agenda__row">
				<span class="avec-clone-agenda__time"><?php echo esc_html( sprintf( '%02d:%02d', (int) ( $minutes / 60 ), $minutes % 60 ) ); ?></span>
				<span class="avec-clone-agenda__slot"></span>
			</div>
		<?php endfor; ?>

		<?php foreach ( $blocos as $bloco ) : ?>
			<div class="avec-clone-agenda__booking<?php echo $bloco['bloqueio'] ? ' is-blocked' : ''; ?>" style="top: <?php echo (int) $bloco['top']; ?>px; height: <?php echo (int) $bloco['height']; ?>px;">
				<strong><?php echo esc_html( $bloco['cliente'] ); ?></strong>
				<?php if ( $bloco['servico'] ) : ?>
					<span><?php echo esc_html( $bloco['servico'] ); ?></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( ! $blocos ) : ?>
		<p class="avec-clone-agenda__empty"><?php esc_html_e( 'Nenhum agendamento nesta data.', 'avec-clone' ); ?></p>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
