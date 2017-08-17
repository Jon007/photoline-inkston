<?php
/**
 * The template for displaying page without Sidebar.
 * Template Name: PostTiles3
 *
 * @package Inkston
 */

get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main"><!--template-posttiles-->

<?php while ( have_posts() ) : the_post();
  get_template_part( 'content', 'page-full' );
  get_template_part( 'template-parts/posts', 'wrap-start-small-fix' );
?><div class="fixbox"><?php
    $final_posts = get_featured_posts();
    foreach ( $final_posts as $key => $post ) {	
		setup_postdata( $post ); 
        get_template_part( 'content', 'tile-thumb' );
    }

//REMAINING NON-FEATURED PRODUCTS as load more, with option to exclude papers
    echo do_shortcode('[ajax_load_more posts_per_page="24" offset="20" max_pages="99" '
//            . 'taxonomy="product_cat" taxonomy_terms="paper" taxonomy_operator="NOT IN" '
//            . ' taxonomy="product_visibility" taxonomy_terms="featured" taxonomy_operator="NOT IN" taxonomy_field="name" '
            . ' post__not_in="' . implode(',', array_merge( array( 0 ), wc_get_product_ids_on_sale(), wc_get_featured_product_ids() )) . '"'
            . ' post_type="product" orderby="modified" scroll_distance="50"]');

    wp_reset_postdata();

endwhile; // end of the loop. ?>
	</div><!-- fixbox -->

		</main><!-- #main -->
	</div><!-- #primary -->
  <p> </p> 
<?php 
/* additional space is needed in the footer to force the scroll load to work*/
get_footer(); ?>
