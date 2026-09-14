<?php
/**
 * "Vale Rápido" section — this feature is disabled on the source Avec account as of the last
 * reconnaissance (see docs/api-reconnaissance.md), so there is no real data to show yet. The
 * CPT and this template exist so the section is ready the moment the feature is activated and
 * data starts flowing through the extractor.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AVEC_CLONE_DIR . 'templates/app-header.php';
require AVEC_CLONE_DIR . 'templates/nav.php';

$vales = get_posts(
	array(
		'post_type'      => 'avec_vale_rapido',
		'posts_per_page' => 50,
	)
);
?>
<main class="avec-clone-section avec-clone-section--vale-rapido">
	<?php if ( ! $vales ) : ?>
		<p><?php esc_html_e( 'Esta funcionalidade ainda não está ativa na conta de origem — não há dados para mostrar.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<ul class="avec-clone-list">
			<?php foreach ( $vales as $post ) : ?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?></span>
					<span class="avec-clone-card__value"><?php echo esc_html( Avec_Clone_Formatting::money( get_post_meta( $post->ID, '_avec_valor', true ) ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
require AVEC_CLONE_DIR . 'templates/app-footer.php';
