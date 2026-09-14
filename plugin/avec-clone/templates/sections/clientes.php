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

$avec_search = isset( $_GET['busca'] ) ? sanitize_text_field( wp_unslash( $_GET['busca'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$clientes = get_posts(
	array(
		'post_type'      => 'avec_cliente',
		'posts_per_page' => 100,
		'orderby'        => 'title',
		'order'          => 'ASC',
		's'              => $avec_search,
	)
);
// A plain, generic person-silhouette icon (own artwork) — not a copy of any
// icon from the source app.
$avatar_icon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.4 0-9 2.2-9 5v3h18v-3c0-2.8-4.6-5-9-5Z"/></svg>';
// Material Symbols "add" (Apache 2.0, Google) — same open icon set the source app itself uses.
$icon_add = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z"/></svg>';
?>
<main class="avec-clone-section avec-clone-section--clientes">
	<div class="avec-clone-clientes-header">
		<form method="get" class="avec-clone-search">
			<div class="avec-clone-search__field">
				<label for="avec-clone-search-kind" class="screen-reader-text"><?php esc_html_e( 'Buscar por', 'avec-clone' ); ?></label>
				<select id="avec-clone-search-kind" disabled>
					<option><?php esc_html_e( 'Nome', 'avec-clone' ); ?></option>
				</select>
			</div>
			<div class="avec-clone-search__field avec-clone-search__field--text">
				<label for="avec-clone-search-busca"><?php esc_html_e( 'Procurar Clientes:', 'avec-clone' ); ?></label>
				<input type="text" id="avec-clone-search-busca" name="busca" value="<?php echo esc_attr( $avec_search ); ?>" placeholder="<?php esc_attr_e( 'Digite aqui...', 'avec-clone' ); ?>" />
			</div>
			<button type="submit" class="avec-clone-btn avec-clone-btn--primary avec-clone-search__submit"><?php esc_html_e( 'Buscar', 'avec-clone' ); ?></button>
		</form>
		<button type="button" class="avec-clone-clientes-header__add" aria-label="<?php esc_attr_e( 'Adicionar cliente', 'avec-clone' ); ?>">
			<?php echo $icon_add; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, hand-written SVG constant. ?>
		</button>
	</div>

	<?php if ( ! $clientes ) : ?>
		<p>
			<?php
			echo esc_html(
				$avec_search
					? __( 'Nenhum cliente encontrado para essa busca.', 'avec-clone' )
					: __( 'Nenhum cliente importado ainda.', 'avec-clone' )
			);
			?>
		</p>
	<?php else : ?>
		<h2 class="avec-clone-panel__title"><?php esc_html_e( 'Últimos Atendimentos', 'avec-clone' ); ?></h2>
		<ul class="avec-clone-list" data-avec-no-instant-filter>
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
