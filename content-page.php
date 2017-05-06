<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <h1 class="page-title entry-title"><?php echo(inkston_title()); ?></h1>


	<?php if ( has_excerpt() ) : ?>
		<header class="entry-header"><?php the_excerpt(); ?></header><!-- .entry-header -->
	<?php endif; //has_excerpt() ?>

	<div class="entry-content">
		<?php
		the_content();
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
</article><!-- #post-## -->
