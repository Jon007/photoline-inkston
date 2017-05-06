<?php
/**
 * The template for Status post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

	<div class="entry-content" style="margin-bottom:20px;">
		<div class="status">
<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_avatar( get_the_author_meta( 'user_email' ), 48 ); ?></a>
		</div><!--.status-->

		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		<p><?php echo mb_substr( strip_tags( get_the_content() ), 0, 140 ); ?></p>

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

		<div class="entry-content">

	<?php echo get_avatar( get_the_author_meta( 'user_email' ), 72 ); ?>
<h3 class="page-title"><?php the_title(); ?></h3>
<i class="fa fa-flag"></i>
<p><?php echo mb_substr( strip_tags( get_the_content() ), 0, 80 ); ?></p>

		</div><!-- .entry-content -->

	<?php endif; ?>

</article><!-- #post-## -->
