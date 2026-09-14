<?php
/**
 * Shared section navigation, included by every `templates/sections/*.php` template.
 * Renders as a sidebar on desktop and a bottom tab bar on mobile (styling lands in Fase 5).
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_section = isset( $GLOBALS['avec_clone_current_section'] ) ? $GLOBALS['avec_clone_current_section'] : '';
$labels          = array(
	'agenda'      => __( 'Agenda', 'avec-clone' ),
	'comandas'    => __( 'Comandas', 'avec-clone' ),
	'comissoes'   => __( 'Comissões', 'avec-clone' ),
	'clientes'    => __( 'Clientes', 'avec-clone' ),
	'vale-rapido' => __( 'Vale Rápido', 'avec-clone' ),
);
?>
<nav class="avec-clone-nav" aria-label="<?php esc_attr_e( 'Navegação do painel', 'avec-clone' ); ?>">
	<ul class="avec-clone-nav__list">
		<?php foreach ( $labels as $slug => $label ) : ?>
			<li class="avec-clone-nav__item<?php echo $slug === $current_section ? ' is-active' : ''; ?>">
				<a href="<?php echo esc_url( Avec_Clone_Frontend_Router::url_for( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
