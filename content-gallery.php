<?php
/**
 * The template for Gallery post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_single() ) : ?>

	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
			<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-meta<?php if ( !is_active_sidebar( 'sidebar-1' ) ) { ?> no-sidebar<?php } ?>">
			
                                <?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
	<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'photoline-inkston' ), __( '1 Comment', 'photoline-inkston' ), __( 'Comments: %', 'photoline-inkston' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->

<?php else : // is_single() ?>

		<?php get_template_part( 'content', 'tile' ); ?>

<?php endif; ?>
</article><!-- #post-## -->
