<?php
/**
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php if ( is_single() ) : ?>
<!-- DEBUG: CONTENT.PHP -->
  <span class="entry-title" style="display:none"><?php  echo(inkston_title()); ?></span>
	<div class="entry-content">
    <h1 class="page-title"><?php echo(inkston_title()); ?></h1>
	<?php the_content(); ?>

			<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'photoline-inkston' ),
				'after'  => '</div>',
			) );
			?>
	</div><!-- .entry-content -->
		<?php do_action( 'storefront_before_footer' ); ?>
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