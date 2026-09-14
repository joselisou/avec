<?php
/**
 * Shared date-range filter bar (start date, end date, "Buscar") used by Comandas and Comissões,
 * matching the real app's own filter header. Expects the including template to have already set
 * `$avec_filter_inicio` and `$avec_filter_fim` ("YYYY-MM-DD" strings).
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$avec_calendar_icon = '<svg viewBox="0 0 1024 1024" aria-hidden="true"><path d="M960 95.888 703.776 95.889V32.113c0-17.68-14.32-32-32-32s-32 14.32-32 32v63.76h-256v-63.76c0-17.68-14.32-32-32-32s-32 14.32-32 32v63.76H64c-35.344 0-64 28.656-64 64v800c0 35.343 28.656 64 64 64h896c35.344 0 64-28.657 64-64v-800c0-35.329-28.656-63.985-64-63.985m0 863.985H64v-800h255.776v32.24c0 17.679 14.32 32 32 32s32-14.321 32-32v-32.224h256v32.24c0 17.68 14.32 32 32 32s32-14.32 32-32v-32.24H960zM736 511.888h64c17.664 0 32-14.336 32-32v-64c0-17.664-14.336-32-32-32h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32m0 255.984h64c17.664 0 32-14.32 32-32v-64c0-17.664-14.336-32-32-32h-64c-17.664 0-32 14.336-32 32v64c0 17.696 14.336 32 32 32m-192-128h-64c-17.664 0-32 14.336-32 32v64c0 17.68 14.336 32 32 32h64c17.664 0 32-14.32 32-32v-64c0-17.648-14.336-32-32-32m0-255.984h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32h64c17.664 0 32-14.336 32-32v-64c0-17.68-14.336-32-32-32m-256 0h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32h64c17.664 0 32-14.336 32-32v-64c0-17.68-14.336-32-32-32m0 255.984h-64c-17.664 0-32 14.336-32 32v64c0 17.68 14.336 32 32 32h64c17.664 0 32-14.32 32-32v-64c0-17.648-14.336-32-32-32"/></svg>';

$avec_period_fields = array(
	'inicio' => $avec_filter_inicio,
	'fim'    => $avec_filter_fim,
);
?>
<div class="avec-clone-filterbar">
	<form method="get" class="avec-clone-filterbar__row">
		<?php foreach ( $avec_period_fields as $avec_field_id => $avec_field_iso ) : ?>
			<div class="avec-clone-datepicker avec-clone-datepicker--field" data-avec-datepicker data-date="<?php echo esc_attr( $avec_field_iso ); ?>" data-target="avec-period-<?php echo esc_attr( $avec_field_id ); ?>">
				<input type="hidden" id="avec-period-<?php echo esc_attr( $avec_field_id ); ?>" name="<?php echo esc_attr( $avec_field_id ); ?>" value="<?php echo esc_attr( $avec_field_iso ); ?>" />
				<button type="button" class="avec-clone-datepicker__toggle avec-clone-datepicker__toggle--field">
					<span class="avec-clone-datepicker__display"><?php echo esc_html( gmdate( 'd/m/Y', strtotime( $avec_field_iso ) ) ); ?></span>
					<?php echo $avec_calendar_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG constant. ?>
				</button>
				<div class="avec-clone-datepicker__panel" hidden></div>
			</div>
		<?php endforeach; ?>
		<button type="submit" class="avec-clone-btn avec-clone-btn--primary avec-clone-filterbar__submit"><?php esc_html_e( 'Buscar', 'avec-clone' ); ?></button>
	</form>
</div>
