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
?>
<main class="avec-clone-section avec-clone-section--clientes">
	<h1><?php esc_html_e( 'Clientes', 'avec-clone' ); ?></h1>

	<?php if ( ! $clientes ) : ?>
		<p><?php esc_html_e( 'Nenhum cliente importado ainda.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<ul class="avec-clone-list">
			<?php foreach ( $clientes as $post ) : ?>
				<li class="avec-clone-card">
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
