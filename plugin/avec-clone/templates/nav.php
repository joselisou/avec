<?php
/**
 * Shared section navigation, included by every `templates/sections/*.php` template. Renders as a
 * left sidebar on desktop and a bottom tab bar on mobile, with a user greeting/logout header
 * mirroring the real Avec Pro nav's structure (avatar, "Olá, X", sign-out).
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
$current_user    = wp_get_current_user();
?>
<nav class="avec-clone-nav" aria-label="<?php esc_attr_e( 'Navegação do painel', 'avec-clone' ); ?>">
	<div class="avec-clone-nav__user">
		<?php echo get_avatar( $current_user->ID, 40 ); ?>
		<span class="avec-clone-nav__greeting">
			<?php
			printf(
				/* translators: %s: display name of the logged-in user */
				esc_html__( 'Olá, %s', 'avec-clone' ),
				esc_html( $current_user->display_name )
			);
			?>
		</span>
	</div>

	<ul class="avec-clone-nav__list">
		<?php foreach ( $labels as $slug => $label ) : ?>
			<li class="avec-clone-nav__item<?php echo $slug === $current_section ? ' is-active' : ''; ?>">
				<a href="<?php echo esc_url( Avec_Clone_Frontend_Router::url_for( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>

	<a class="avec-clone-nav__logout" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sair', 'avec-clone' ); ?></a>
</nav>
