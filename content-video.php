<?php
/**
 * The template for Video post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<?php the_excerpt(); ?>
			<?php endif; //has_excerpt() ?>	

		</header><!-- .entry-header -->

		<div class="entry-content">

			<?php the_content(); ?>

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

		<div class="entry-content"<?php inkston_bgimage_postformats(); ?>>
			<a href="<?php the_permalink(); ?>">
				<i class="fa fa-play-circle"></i>
					<h1 class="page-title"><?php the_title(); ?></h1>
			</a>
		</div><!-- .entry-content -->

	<?php endif; ?>

</article><!-- #post-## -->
