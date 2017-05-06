<?php
/**
 * The template for Aside post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta<?php if ( !is_active_sidebar( 'sidebar-1' ) ) { ?> no-sidebar<?php } ?>">

			<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->

	<?php else : ?>

		<div class="box smallbox">
			<?php get_template_part( 'content', 'tile' ); ?>
		</div>


	<?php endif; ?>

</article><!-- #post-## -->
