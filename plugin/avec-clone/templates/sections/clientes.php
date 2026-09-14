<?php
/**
 * "Clientes" section — lists avec_cliente posts.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/nav.php';

$clientes = get_posts(
	array(
		'post_type'      => 'avec_cliente',
		'posts_per_page' => 100,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
// A plain, generic person-silhouette icon (own artwork) — not a copy of any
// icon from the source app.
$avatar_icon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.4 0-9 2.2-9 5v3h18v-3c0-2.8-4.6-5-9-5Z"/></svg>';
?>
<main class="avec-clone-section avec-clone-section--clientes">
	<h1><?php esc_html_e( 'Clientes', 'avec-clone' ); ?></h1>

	<?php if ( ! $clientes ) : ?>
		<p><?php esc_html_e( 'Nenhum cliente importado ainda.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Últimos Atendimentos', 'avec-clone' ); ?></h2>
		<ul class="avec-clone-list">
			<?php foreach ( $clientes as $post ) : ?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__avatar"><?php echo $avatar_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, hand-written SVG constant, no user input. ?></span>
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?></span>
					<span class="avec-clone-card__meta"><?php echo esc_html( get_post_meta( $post->ID, '_avec_celular', true ) ); ?></span>
					<span class="avec-clone-card__meta"><?php echo esc_html( get_post_meta( $post->ID, '_avec_email', true ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
