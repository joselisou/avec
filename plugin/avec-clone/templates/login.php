<?php
/**
 * Login gate shown for `/minha-conta/*` when the visitor isn't authenticated.
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$error       = isset( $_GET['avec_clone_login_error'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$redirect_to = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '/minha-conta/' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
?>
<div class="avec-clone-login">
	<h1><?php esc_html_e( 'Entrar', 'avec-clone' ); ?></h1>

	<?php if ( $error ) : ?>
		<p class="avec-clone-login__error"><?php esc_html_e( 'E-mail ou senha inválidos.', 'avec-clone' ); ?></p>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=avec_clone_login' ) ); ?>">
		<?php wp_nonce_field( 'avec_clone_login', 'avec_clone_login_nonce' ); ?>
		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />

		<label for="avec-clone-log"><?php esc_html_e( 'E-mail ou usuário', 'avec-clone' ); ?></label>
		<input type="text" name="log" id="avec-clone-log" required />

		<label for="avec-clone-pwd"><?php esc_html_e( 'Senha', 'avec-clone' ); ?></label>
		<input type="password" name="pwd" id="avec-clone-pwd" required />

		<button type="submit"><?php esc_html_e( 'Entrar', 'avec-clone' ); ?></button>
	</form>
</div>
<?php
get_footer();
