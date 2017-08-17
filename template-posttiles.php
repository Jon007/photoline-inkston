<?php
/**
 * The template for displaying page without Sidebar.
 * Template Name: PostTiles
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
  $query = array(
//    'post_type' => array( 'product', 'post' ),
    'post_type' => array( 'post' ),
    'orderby' => 'modified',
    'posts_per_page' => '36',
    'order' => 'DESC'
  );
/*
  $query = array(
    'post_type' => array( 'product', 'post' )
  );
  */
  $post_list = new WP_Query( $query );      
  if ( $post_list->have_posts() ) {
    while ( $post_list->have_posts() ) {
      $post_list->the_post();          ?>
      <?php get_template_part( 'content', 'tile-thumb' ); ?>
<?php
    }
  }
//FEATURED PRODUCTS 
$meta_query   = WC()->query->get_meta_query();
$meta_query = array(
array(
        'key'       => '_visibility',
        'value'     => 'hidden',
        'compare'   => '!=',
    )
);
$args = array(
    'post_type'   =>  'product',
    'showposts'   =>  48,
    'orderby'     =>  'modified',
    'order'       =>  'DESC',
    'meta_query'  =>  $meta_query,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'compare'   => 'IN',
        ),
    ),
);    
  $post_list = new WP_Query( $args );      
  if ( $post_list->have_posts() ) {
    while ( $post_list->have_posts() ) {
      $post_list->the_post();          
      ?><?php get_template_part( 'content', 'tile-thumb' ); ?><?php
    }
  }
  
//TOP NON-FEATURED PRODUCTS 
$meta_query   = WC()->query->get_meta_query();
$meta_query = array(
array(
        'key'       => '_visibility',
        'value'     => 'hidden',
        'compare'   => '!=',
    )
);
$args = array(
    'post_type'   =>  'product',
    'showposts'   =>  24,
    'orderby'     =>  'modified',
    'order'       =>  'DESC',
    'meta_query'  =>  $meta_query,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'compare'   => 'NOT IN',
        ),
    ),
);    
  $post_list = new WP_Query( $args );      
  if ( $post_list->have_posts() ) {
    while ( $post_list->have_posts() ) {
      $post_list->the_post();          
      ?><?php get_template_part( 'content', 'tile-thumb' ); ?><?php
    }
  }


//REMAINING NON-FEATURED PRODUCTS as load more, with option to exclude papers
    echo do_shortcode('[ajax_load_more posts_per_page="24" offset="24" max_pages="99" '
//            . 'taxonomy="product_cat" taxonomy_terms="paper" taxonomy_operator="NOT IN" '
            . ' taxonomy="product_visibility" taxonomy_terms="featured" taxonomy_operator="NOT IN" taxonomy_field="name"  '
            . ' post_type="product" orderby="modified" scroll_distance="50"]');
  
endwhile; // end of the loop. ?>
	</div><!-- fixbox -->

		</main><!-- #main -->
	</div><!-- #primary -->
  <p> </p> 
<?php 
/* additional space is needed in the footer to force the scroll load to work*/
get_footer(); ?>
