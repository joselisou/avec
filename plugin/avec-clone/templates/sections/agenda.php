<?php
/**
 * "Agenda" section — lists avec_agendamento posts, most recent first.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
require AVEC_CLONE_DIR . 'templates/nav.php';

$agendamentos = get_posts(
	array(
		'post_type'      => 'avec_agendamento',
		'posts_per_page' => 50,
		'orderby'        => 'meta_value',
		'meta_key'       => '_avec_data',
		'order'          => 'DESC',
	)
);
?>
<main class="avec-clone-section avec-clone-section--agenda">
	<h1><?php esc_html_e( 'Agenda', 'avec-clone' ); ?></h1>

	<?php if ( ! $agendamentos ) : ?>
		<p><?php esc_html_e( 'Nenhum agendamento importado ainda.', 'avec-clone' ); ?></p>
	<?php else : ?>
		<ul class="avec-clone-list">
			<?php foreach ( $agendamentos as $post ) : ?>
				<li class="avec-clone-card">
					<span class="avec-clone-card__date"><?php echo esc_html( get_post_meta( $post->ID, '_avec_data', true ) ); ?></span>
					<span class="avec-clone-card__title"><?php echo esc_html( $post->post_title ); ?></span>
					<span class="avec-clone-card__value"><?php echo esc_html( number_format_i18n( (float) get_post_meta( $post->ID, '_avec_valor', true ), 2 ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>
<?php
get_footer();
