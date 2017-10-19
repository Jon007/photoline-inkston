<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  

	<?php if ( has_excerpt() ) { ?>
  	<?php if ( is_front_page() ) {       ?>
      <span class="entry-title" style="display:none"><?php the_excerpt(); ?></span>
    	<h1 class="entry-header"><?php the_excerpt(); ?></h1><!-- .entry-header -->
    <?php } else { ?>
      <span class="entry-title" style="display:none"><?php echo(wp_title('&raquo;', false, '')); ?></span>
    	<header class="entry-header"><?php the_excerpt(); ?></header><!-- .entry-header -->
    <?php } //is_front_page() ?>
  <?php } else { ?>      
      <h1 class="page-title entry-title"><?php echo(inkston_title()); ?></h1>
  <?php } //has_excerpt() ?>

<div class="entry-content">

		<?php the_content(); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'photoline-inkston' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
<?php    ink_sharing(); ?>
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
