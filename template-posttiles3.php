<?php
/**
 * The template for displaying page without Sidebar.
 * Template Name: PostTiles3
 *
 * @package Inkston
 */
function shuffle_assoc($array)
{
    // Initialize
    $shuffled_array = array();

    // Get array's keys and shuffle them.
    $shuffled_keys = array_keys($array);
    shuffle($shuffled_keys);


    // Create same array, but in shuffled order.
    foreach ( $shuffled_keys AS $shuffled_key ) {
        $shuffled_array[  $shuffled_key  ] = $array[  $shuffled_key  ];
    } // foreach

    // Return
    return $shuffled_array;
}            

get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main"><!--template-posttiles-->

<?php while ( have_posts() ) : the_post();
  get_template_part( 'content', 'page-full' );
  get_template_part( 'template-parts/posts', 'wrap-start-small-fix' );
?><div class="fixbox"><?php

    //RECENT POSTS
    $query_args = array(
        'ignore_sticky_posts' => 0, //sticky posts automatically added by WP
        'post_type' => array( 'post' ),
        'orderby' => 'modified',
        'posts_per_page' => 50,
        'showposts'   =>  50,
        'order' => 'DESC'
    );
    $recent_list = new WP_Query( $query_args );
    
    //FEATURED PRODUCTS 
    $query_args = array(
        'posts_per_page' => 20,
        'showposts'   =>  20,
        'post_status' => 'publish',
        'post_type'   => 'product',
        'post__in'    => array_merge( array( 0 ), wc_get_featured_product_ids() ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
    );
    $product_list = new WP_Query( $query_args );      

    //SALE PRODUCTS 
    $query_args = array(
        'posts_per_page' => 20,
        'showposts'   =>  20,
        'post_status' => 'publish',
        'post_type'   => 'product',
        'post__in'    => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
    );
    $sale_list = new WP_Query( $query_args );    


    //RECENT NON-FEATURED PRODUCTS 
    $meta_query   = WC()->query->get_meta_query();
    $meta_query = array(
        array(
            'key'   => '_featured',
            'value' => 'no'
        ), 
        array(
            'key'       => '_visibility',
            'value'     => 'hidden',
            'compare'   => '!=',
        )
    );
    $query_args = array(
        'post_type'   =>  'product',
        'posts_per_page' => 20,
        'showposts'   =>  20,
        'post_status' => 'publish',
        'post__not_in' => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
        'meta_query'  =>  $meta_query
    );    
    $recentproduct_list = new WP_Query( $query_args );      

    $final_posts = array_merge( $recent_list->posts, $product_list->posts, $sale_list->posts, $recentproduct_list->posts  );

    //$final_posts = array_unique ( $final_posts);
    //$final_posts = shuffle_assoc($final_posts);
    shuffle($final_posts);
    foreach ( $final_posts as $key => $post ) {	
		setup_postdata( $post ); 
        get_template_part( 'content', 'tile-thumb' );
    }

//REMAINING NON-FEATURED PRODUCTS as load more, with option to exclude papers
    echo do_shortcode('[ajax_load_more posts_per_page="24" offset="24" max_pages="99" '
//            . 'taxonomy="product_cat" taxonomy_terms="paper" taxonomy_operator="NOT IN" '
            . ' meta_key="_featured" meta_value="no" meta_compare="IN" '
            . ' post__not_in="' . implode(',', wc_get_product_ids_on_sale()) . '"'
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
