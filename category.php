<?php
/**
 * The template for displaying Category default template.
 * @package Inkston
 */

get_header();
get_template_part( 'template-parts/posts', 'wrap-start-small' );
?>
<!-- SquarePost-->
<!--<div id="squareTiles" class="clearfix">-->

  <h1 class="page-title entry-title"><?php echo(inkston_title()); ?></h1>

  <?php if (is_category()) {
    $description=category_description(); 
    echo $description;
   }


	if ( have_posts() ) :
?><div class="site-main"><?php
		while ( have_posts() ) : 
      the_post(); 
      get_template_part( 'content', 'tile' ); 
    endwhile;
		$cat = get_category( get_query_var( 'cat' ) );
		$category = $cat->slug;
		echo do_shortcode( '[ajax_load_more preloaded="true" preloaded_amount="48" category="'.$category.'" posts_per_page="12"  offset="12" max_pages="100" progress_bar="true" progress_bar_color="39aa39" button_label="More.." button_loading_label="More .. .. .." transition="fade" scroll_distance="50"]');
		//echo do_shortcode( '[ajax_load_more category="'.$category.'" posts_per_page="12"  offset="12" max_pages="0" button_label="More.." button_loading_label="More .. .. .." transition="fade" pause="true" pause_override="true"]');

    ?></div>
	<?php else : ?>
		<?php get_template_part( 'no-results', 'index' ); ?>
	<?php endif;  // have_posts() ?>

  	</div><!-- fixbox -->


<?php 
if ( comments_open() || '0' != get_comments_number() )
					comments_template();
?>

		</main><!-- #main -->
<div class="clearfix"></div>
<?php
/* pointless if using infinite scroll..
if( true === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_paging_nav(); // numbers pagination
}

if( false === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_content_nav( 'nav-below' );
}
*/
?>
</div><!-- #primary -->

<?php if ( is_active_sidebar( 'sidebar-1' ) ) { get_sidebar(); } ?>
<?php get_footer(); ?>
