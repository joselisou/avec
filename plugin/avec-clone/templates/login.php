<?php
/**
 * Login gate shown for `/minha-conta/*` when the visitor isn't authenticated. Layout mirrors the
 * real Avec Pro login (two-column, brand-color panel hidden on mobile) — see the "Design tokens
 * e branding" section of the project plan for the measurements and the branding boundary (own
 * wordmark/illustration, never the source app's actual logo or background artwork).
 *
 * @package AvecClone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/app-header.php';

$error       = isset( $_GET['avec_clone_login_error'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$redirect_to = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '/minha-conta/' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
?>
<div class="avec-clone-login">
	<div class="avec-clone-login__panel">
		<svg class="avec-clone-login__waves" viewBox="0 0 600 300" preserveAspectRatio="none" aria-hidden="true">
			<path d="M0,180 C120,140 180,220 300,190 C420,160 480,240 600,200 L600,300 L0,300 Z" fill="rgba(255,255,255,0.12)" />
			<path d="M0,220 C130,260 220,190 340,225 C440,255 520,200 600,235 L600,300 L0,300 Z" fill="rgba(255,255,255,0.18)" />
		</svg>
		<h1><?php esc_html_e( 'Bem-vindo.', 'avec-clone' ); ?></h1>
		<p><?php esc_html_e( 'O seu painel Avec, espelhado num WordPress só seu — pronto para virar dados e decisões.', 'avec-clone' ); ?></p>
	</div>

	<div class="avec-clone-login__form-panel">
		<p class="avec-clone-login__wordmark"><?php esc_html_e( 'Avec Clone', 'avec-clone' ); ?></p>

		<h2><?php esc_html_e( 'Entrar', 'avec-clone' ); ?></h2>
		<p class="avec-clone-login__hint"><?php esc_html_e( 'Digite seu e-mail e senha do WordPress para acessar o painel.', 'avec-clone' ); ?></p>

		<?php if ( $error ) : ?>
			<p class="avec-clone-login__error"><?php esc_html_e( 'E-mail ou senha inválidos.', 'avec-clone' ); ?></p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=avec_clone_login' ) ); ?>">
			<?php wp_nonce_field( 'avec_clone_login', 'avec_clone_login_nonce' ); ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />

			<label for="avec-clone-log"><?php esc_html_e( 'E-mail', 'avec-clone' ); ?></label>
			<input type="text" name="log" id="avec-clone-log" placeholder="<?php esc_attr_e( 'Digite aqui...', 'avec-clone' ); ?>" required />

			<label for="avec-clone-pwd"><?php esc_html_e( 'Senha', 'avec-clone' ); ?></label>
			<input type="password" name="pwd" id="avec-clone-pwd" placeholder="<?php esc_attr_e( 'Digite sua senha...', 'avec-clone' ); ?>" required />

			<a class="avec-clone-login__forgot" href="<?php echo esc_url( wp_lostpassword_url( $redirect_to ) ); ?>"><?php esc_html_e( 'Esqueci minha senha', 'avec-clone' ); ?></a>

			<button type="submit" class="avec-clone-btn avec-clone-btn--primary"><?php esc_html_e( 'Acessar conta', 'avec-clone' ); ?></button>
			<a class="avec-clone-btn avec-clone-btn--secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Voltar para o site', 'avec-clone' ); ?></a>
		</form>
	</div>
</div>
<?php
require __DIR__ . '/app-footer.php';
