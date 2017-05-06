<?php
/**
 * inkston back compat functionality
 * @package Inkston
 */

/**
 * Switches to the default theme
 */
function inkston_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'inkston_upgrade_notice' );
}
add_action( 'after_switch_theme', 'inkston_switch_theme' );

/**
 * Add message for unsuccessful theme switch
 */
function inkston_upgrade_notice() {
	$message = sprintf( __( 'inkston requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'photoline-inkston' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevent the Customizer from being loaded on WordPress versions prior to 4.1.
 */
function inkston_customize() {
	wp_die( sprintf( __( 'inkston requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'photoline-inkston' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'inkston_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 4.1.
 */
function inkston_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'inkston requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'photoline-inkston' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'inkston_preview' );
