<?php
/**
 * The template for displaying pages.
 *
 * @package Inkston
 */

get_header();
// currently ignoring if ( !is_front_page() ) :  treat as the same, to get special front page use full width or posts
?>
	<div id="primary" class="content-area<?php if ( !is_active_sidebar( 'sidebar-2' ) ) { ?> no-sidebar<?php } ?>">
		<main id="main" class="site-main" role="main">


			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php do_action( 'inkston_after_main_content' ); ?>

				<?php
				// If comments are open or we have at least one comment, load up the comment template
        /*no, actually no comments on standard pages, 
         *eg undesirable to have comments on front page, login, cart, shop main page, 
         * re-enabled Mar 2017: it will be desirable for FAQ page etc
        */
        if ( comments_open() || '0' != get_comments_number() )
					comments_template();
				?>
			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php get_sidebar();
	/* NOTE: this theme is not currently tested for re-enabling sidebars ( is_active_sidebar( 'sidebar-2' ) ) { get_sidebar(); } */
	?>

<?php get_footer(); ?>
