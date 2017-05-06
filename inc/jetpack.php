<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Inkston
 */

/**
 * Add theme support for infinity scroll
 */
function inkston_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
				'container' => 'masonry', // infinite
				'render'	=> 'inkston_infinite_scroll_render',
				'posts_per_page' => true,
				'footer'	=> false,
				'type'	=> 'click'
			) );
}
add_action( 'after_setup_theme', 'inkston_infinite_scroll_init' );

/**
 * Set the code to be rendered on for calling posts for infinity scroll
 */
function inkston_infinite_scroll_render() {
		the_post();
		get_template_part( 'content', get_post_format() );

}

/**
 * Remove sharedaddy
 */
function inkston_sidebar_sharedaddy() {
	remove_filter( 'the_content', 'sharing_display', 19 );
}
add_action( 'dynamic_sidebar', 'inkston_sidebar_sharedaddy' );

function inkston_excerpt_sharedaddy() {
    remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
add_action( 'loop_start', 'inkston_excerpt_sharedaddy' );