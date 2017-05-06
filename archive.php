<?php
/**
 * The template for displaying Archive pages.
 * @package Inkston
 */

get_header(); ?>
<!--archive.php-->
<?php if ( have_posts() ) : ?>

<?php get_template_part( 'template-parts/posts', 'wrap-start-small' ); ?>

			<?php while ( have_posts() ) : the_post(); ?><?php get_template_part( 'content', 'tile' ); ?><?php endwhile;?>


		<?php else : ?>

			<?php get_template_part( 'no-results', 'archive' ); ?>

		<?php endif; ?>

	</div><!-- .grid -->
		</main><!-- #main -->

<div class="clearfix"></div>

<?php
if( true === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_paging_nav(); // numbers pagination
}

if( false === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_content_nav( 'nav-below' );
}
?>

	</div><!-- #primary -->

<?php if ( is_active_sidebar( 'sidebar-1' ) ) { get_sidebar(); } ?>
<?php get_footer(); ?>
