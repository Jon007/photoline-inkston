<?php

/**
 * User Login Form
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">
	<fieldset class="bbp-form">
		<legend><?php esc_html_e( 'Log In', 'bbpress' ); ?></legend>

		<div class="bbp-username">
			<input type="text" name="log" placeholder="<?php 
                esc_html_e( 'Username', 'bbpress' ); 
                ?>" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" />
		</div>

		<div class="bbp-password">
			<input type="password" name="pwd"  placeholder="<?php 
                esc_html_e( 'Password', 'bbpress' ); 
                ?>" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" autocomplete="off" />
		</div>

		<div class="bbp-remember-me">
			<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ) ); ?> id="rememberme" />
			<label for="rememberme"><?php esc_html_e( 'Keep me signed in', 'bbpress' ); ?></label>
		</div>

		<?php do_action( 'login_form' ); ?>

		<div class="bbp-submit-wrapper">

			<button type="submit" name="user-submit" class="button submit user-submit"><?php esc_html_e( 'Log In', 'bbpress' ); ?></button>

			<?php bbp_user_login_fields(); ?>

		</div>
	</fieldset>
</form>