<?php
/**
 * The main template file.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Inkston
 */
get_header(); ?>
<?php get_template_part( 'template-parts/posts', 'wrap-start-small-fix' ); ?>

<?php if ( have_posts() ) : ?>
<?php get_template_part( 'template-parts/posts', 'wrap-start-small' ); ?>
<?php while ( have_posts() ) : the_post(); ?><?php get_template_part( 'content', 'tile' ); ?><?php endwhile;?>

  <?php
  //code supplied for Ajax LoadMore extension didn't work and too low priority to fix
//    $term = (isset($_GET['s'])) ? $_GET['s'] : ''; // Get 's' querystring param
//    echo ($term);
//    echo do_shortcode('[ajax_load_more offset="15" id="relevanssi" search="'. $term .'" post_type="post,page,product"]'); 

    //don't output ajax_load_more without interpreting the full query 
		//echo do_shortcode('[ajax_load_more posts_per_page="12" offset="12" max_pages="99" ]');
		//echo do_shortcode('[ajax_load_more category="'.$category.'" posts_per_page="12"  offset="12" max_pages="0" button_label="More.." button_loading_label="More .. .. .." transition="fade" pause="true" pause_override="true"]');

		?>


		<?php else : ?>

			<?php get_template_part( 'no-results', 'archive' ); ?>

		<?php endif; ?>

	</div><!-- .grid -->
		</main><!-- #main -->

<div class="clearfix"></div>


<?php
//if( true === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_paging_nav(); // numbers pagination
//}
//
//if( false === get_theme_mod( 'numbered_pagination' ) ) {
        inkston_content_nav( 'nav-below' );
//}
?>

	</div><!-- #primary -->
<?php if ( is_active_sidebar( 'sidebar-1' ) ) { get_sidebar(); } ?>
<?php get_footer(); ?>