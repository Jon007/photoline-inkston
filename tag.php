<?php
/**
 * The template for displaying Category default template.
 * @package Inkston
 */

get_header();
?>
<!-- SquarePost-->
<!--<div id="squareTiles" class="clearfix">-->
<?php get_template_part( 'template-parts/posts', 'wrap-start-small' ); ?>
  <h1 class="page-title entry-title"><?php echo(inkston_title()); ?></h1>
<?php if (is_tag()) { ?>
<?php $description= tag_description(); if ($description) {echo $description;}  ?>
<?php } ?>

	<?php if ( have_posts() ) : ?>
    <div class="site-main">
		<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'tile' ); ?>
		<?php endwhile;?>
    </div>
		<?php
    		//echo do_shortcode('[ajax_load_more posts_per_page="12" offset="12" max_pages="99" ]');
/*
		$cat = get_category( get_query_var( 'cat' ) );
		$category = $cat->slug;
		echo do_shortcode('[ajax_load_more category="'.$category.'" posts_per_page="12"  offset="12" max_pages="0" button_label="More.." button_loading_label="More .. .. .." transition="fade" ]');
 * 
 */
		//echo do_shortcode('[ajax_load_more category="'.$category.'" posts_per_page="12"  offset="12" max_pages="0" button_label="More.." button_loading_label="More .. .. .." transition="fade" pause="true" pause_override="true"]');

		?>
	<?php else : ?>
		<?php get_template_part( 'no-results', 'index' ); ?>
	<?php endif;  // have_posts() ?>

</div><!-- grid -->
</main><!-- #main -->

<div class="clearfix"></div>
<?php
/* pointless if using infinite scroll..  .. needed until infinite scroll query is written*/
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
