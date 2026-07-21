<?php
/**
 * Logged-out My Account view. Login + register side-by-side.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="dc-account dc-account--login">
	<header class="dc-account__header">
		<div class="dc-account__eyebrow"><?php esc_html_e( 'Welcome', 'dankcave' ); ?></div>
		<h1 class="dc-account__title"><?php esc_html_e( 'Sign in to your cave', 'dankcave' ); ?></h1>
	</header>

	<div class="dc-login-grid <?php echo 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ? 'has-register' : ''; ?>">

		<div class="dc-login-card u-login-column">
			<h2 class="dc-login-card__title"><?php esc_html_e( 'Log in', 'dankcave' ); ?></h2>

			<form class="woocommerce-form woocommerce-form-login login" method="post">
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row">
					<label for="username"><?php esc_html_e( 'Email or username', 'dankcave' ); ?> <span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
				</p>
				<p class="woocommerce-form-row">
					<label for="password"><?php esc_html_e( 'Password', 'dankcave' ); ?> <span class="required" aria-hidden="true">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="dc-login-card__actions">
					<label class="dc-login-card__remember">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
						<span><?php esc_html_e( 'Remember me', 'dankcave' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit dc-login-card__submit" name="login" value="<?php esc_attr_e( 'Log in', 'dankcave' ); ?>"><?php esc_html_e( 'Log in', 'dankcave' ); ?></button>
				</p>
				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'dankcave' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<div class="dc-login-card u-register-column">
				<h2 class="dc-login-card__title"><?php esc_html_e( 'Create an account', 'dankcave' ); ?></h2>

				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<p class="woocommerce-form-row">
							<label for="reg_username"><?php esc_html_e( 'Username', 'dankcave' ); ?> <span class="required" aria-hidden="true">*</span></label>
							<input type="text" class="input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
						</p>
					<?php endif; ?>

					<p class="woocommerce-form-row">
						<label for="reg_email"><?php esc_html_e( 'Email address', 'dankcave' ); ?> <span class="required" aria-hidden="true">*</span></label>
						<input type="email" class="input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<p class="woocommerce-form-row">
							<label for="reg_password"><?php esc_html_e( 'Password', 'dankcave' ); ?> <span class="required" aria-hidden="true">*</span></label>
							<input type="password" class="input-text" name="password" id="reg_password" autocomplete="new-password" />
						</p>
					<?php else : ?>
						<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'dankcave' ); ?></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="dc-login-card__actions">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit dc-login-card__submit" name="register" value="<?php esc_attr_e( 'Create account', 'dankcave' ); ?>"><?php esc_html_e( 'Create account', 'dankcave' ); ?></button>
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );
