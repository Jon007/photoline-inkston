<?php
/**
 * Theme Contextual Help
 * @package Inkston
 */
add_filter( 'contextual_help', 'inkston_admin_contextual_help', 10 );

function inkston_admin_contextual_help() {

	$screen = get_current_screen();

if ( $screen->id == 'themes' ) {

  $screen->add_help_tab( array(
      'id' => 'inkston_wellcom_tab',
      'title' => __( 'inkston Theme', 'photoline-inkston' ),
      'content' => '<p><strong>' . __( 'Thank you for choosing this Theme!', 'photoline-inkston' ) . '</strong></p><p>' . __( 'The Theme has a contextual help for some admin screens. More information, help and support you will find on the website inkston.net.', 'photoline-inkston' ) . '</p><p><strong>' . __( 'Quick Start', 'photoline-inkston' ) . '</strong></p><p>' . __( 'Using Customizer set your color, upload a logo (avatar - for personal) image, upload a background image (or select any color), upload the image (or select any color) header and other settings.', 'photoline-inkston' ) . '</p><p>' . __( 'The theme has page templates for the Home Page. Home Mosaic Tiles and Home Square Tiles Templates show 4 (or more) posts image format. Into Home Tagline section can be used plugins shortcode.', 'photoline-inkston' ) . '</p><p><strong>' . __( 'Note', 'photoline-inkston' ) . '</strong></p><p>' . __( 'If you want to display the posts without the sidebar just leave blank Sidebar posts.', 'photoline-inkston' ) . '</p>',
  ) );

}

if ( $screen->id == 'post' ) {

	$screen->add_help_tab( array(
		'id'      => 'inkston-post-fimg',
		'title'   => __( 'Theme Features', 'photoline-inkston' ),
		'content' => '<p><strong>' . __( 'Theme Features', 'photoline-inkston' ) . '</strong></p><p><strong>' . 
      __( 'Use Featured image', 'photoline-inkston' ) . '</strong></p><p>' .
      __( 'Featured image used for the cover of the post formats. Upload the image that will be displayed header on single post formats standard and image.', 'photoline-inkston' ) . '</p><p><strong>' . 
      __( 'Use Excerpt', 'photoline-inkston' ) . '</strong></p><p>' . 
      __( 'Enter text in field Excerpt to display announcement of the post.', 'photoline-inkston' ) . '</p>',
  ) );

}

if ( $screen->id == 'page' ) {

  $screen->add_help_tab( array(
      'id' => 'inkston_page_tab',
      'title' => __( 'Theme Features', 'photoline-inkston' ),
	'content' =>  '<p><strong>' . __( 'Theme Features', 'photoline-inkston' ) . '</strong></p><p><strong>' . __( 'Use Featured image', 'photoline-inkston' ) . '</strong></p><p>' . __( 'Upload the image that will be displayed header on page.', 'photoline-inkston' ) . '</p><p><strong>' . __( 'Use Excerpt', 'photoline-inkston' ) . '</strong></p><p>' . __( 'Enter text in field Excerpt to display announcement of the post.', 'photoline-inkston' ) . '</p><p><strong>' . __( 'Templates', 'photoline-inkston' ) . '</strong></p><p>' . __( 'The theme has several page templates. Use metabox Page Attributes > dropdown Template.', 'photoline-inkston' ) . '</p>'
  ) );

}

if ( $screen->id == 'plugins' ) {

  $screen->add_help_tab( array(
      'id' => 'inkston_wellcom_tab',
      'title' => __( 'Recommend', 'photoline-inkston' ),
      'content' =>  '<p><strong>' . __( 'Recommended plugins for inkston Theme', 'photoline-inkston' ) . '</strong></p><ul><li>' . __( 'Jetpack By WordPress.com', 'photoline-inkston' ) . '</li><li>' . __( 'Contact Form 7 By Takayuki Miyoshi', 'photoline-inkston' ) . '</li><li>' . __( 'Shortcodes Ultimate By Vladimir Anokhin.', 'photoline-inkston' ) . '</li></ul>',
	) );

}

if ( $screen->id == 'nav-menus' ) {

	$screen->add_help_tab( array(
		'id'      => 'inkston-social-menus',
		'title'   => __( 'Social Menu', 'photoline-inkston' ),
		'content' =>  '<p><strong>' . __( 'Custom widgets', 'photoline-inkston' ) . '</strong></p><p>' . __( 'Menu icons social media is displayed in the footer. Included all popular icons of social media, and Feedburner. To create a menu item, use the tab Links (Edit Menus). And select Social Menu as Theme locations.', 'photoline-inkston' ) . '</p><p>' . __( 'Example:<br />tab <strong>Links</strong><br /><em>URL</em> http://twitter.com/your<br /><em>Navigation Label</em> Twitter', 'photoline-inkston' ) . '</p>',
	) );
	$screen->add_help_tab( array(
		'id'      => 'inkston-top-menus',
		'title'   => __( 'Top Menu', 'photoline-inkston' ),
		'content' => __('<p><strong>Top Menu</strong></p><p>The theme has an additional top menu bar.</p><p>If you create a menu item using Links tab (Edit Menus) and links will be http://tel: OR http://mailto: OR https://goo.gl (short link google map) it will be displayed icons Font Awesome.</p><p>Example:<br />tab <strong>Links</strong><br /><em>URL</em> http://tel:+1916000000<br /><em>Navigation Label</em> +1 916 00.00.00</p>', 'photoline-inkston' ),
	) );
}

/**
*else
*/
      return;
}
?>