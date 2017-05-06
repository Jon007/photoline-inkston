<?php
/**
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php if ( is_single() ) : ?>
<!-- DEBUG: CONTENT.PHP -->
	<div class="entry-content">
	<?php the_content(); ?>

			<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'photoline-inkston' ),
				'after'  => '</div>',
			) );
			?>
	</div><!-- .entry-content -->

	<footer class="entry-meta<?php if ( !is_active_sidebar( 'sidebar-1' ) ) { ?> no-sidebar<?php } ?>">

		<div class="posted">
			<?php inkston_posted_on(); ?>
		</div>
		<div class="extrameta">
			<?php inkston_posted_extra(); ?>
		</div>

		<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<span class="edit-link">', '</span>' ); ?>

	</footer><!-- .entry-meta -->

<?php else : ?>


		<?php get_template_part( 'content', 'tile' ); ?>

<?php endif; ?>

</article><!-- #post-## -->