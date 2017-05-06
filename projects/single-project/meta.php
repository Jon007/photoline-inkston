<?php
/**
 * Single Project Meta
 *
 * @author WooThemes
 * @package Inkston
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
?>
<ul class="project-meta">

	<?php
		// Meta
		$date 		= esc_attr( get_post_meta( $post->ID, '_date', true ) );
		$client 		= esc_attr( get_post_meta( $post->ID, '_client', true ) );
		$url 			= esc_url( get_post_meta( $post->ID, '_url', true ) );

		do_action( 'projects_before_meta' );

		/**
		 * Display date if set
		 */
		if ( $date) {
			echo '<li>';
			echo '<strong>' . __( 'Date', 'photoline-inkston' ) . ' </strong>';
			echo '<span class="project-date">' . $date . '</span>';
			echo '</li>';
		}

		/**
		 * Display client if set
		 */
		if ( $client ) {
			echo '<li>';
			echo '<strong>' . __( 'Client', 'photoline-inkston' ) . ' </strong>';
			echo '<span class="project-client">' . $client . '</span>';
			echo '</li>';
		}

		/**
		 * Display link if set
		 */
		if ( $url ) {
			echo '<li>';
			echo '<strong>' . __( 'Link', 'photoline-inkston' ) . ' </strong>';
			echo '<span class="project-url"><a href="' . $url . '">' . $url . '</a></span>';
			echo '</li>';
		}

		do_action( 'projects_after_meta' );
	?>
</ul>