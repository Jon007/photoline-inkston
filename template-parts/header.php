<?php
/**
 * @package Inkston
 */
if ( ! function_exists( 'inkston_title' ) ) {
	function inkston_title(){
		static $title;
		if (!isset($title)) {

			/* get default title, overridden by Yoast SEO as appropriate */
			$title = wp_title('&raquo;', false, '');
      if (is_search()){
        if (get_search_query()==''){
          $title = __( 'Search Inkston.', 'photoline-inkston' );;
        }
        else{
          global $wp_query;
          $title .= ' (' . $wp_query->found_posts . ' ' . __('results', 'photoline-inkston' ) . ')';
        }
      }
			/**
			 * Template WooCommerce
			 */
			if (is_woocommerce_activated()) {
				if (is_woocommerce() && !is_product()) {
					$title = woocommerce_page_title(false);
				}
			}    /* if ( is_woocommerce_activated() ) */
		}
    /* remove trailing Inkston if added by Yoast SEO  */
    $title = str_replace ( '- Inkston', '', $title );

		return $title;
	}
}

if ( ! function_exists( 'inkston_output_paging' ) ) {
	/**
	 * Display navigation to next/previous pages when applicable
	 */
	function inkston_output_paging()
	{
		/* all posts pages */
		if (is_single() && !is_attachment()) {
			/**
			 * add navigation for posts pages - works also for custom post types ie wooCommerce product
			 */ ?>
			<nav id="single-nav">
				<?php 
        if (is_woocommerce() && is_product()) {
          previous_post_link('<div id="single-nav-right">%link</div>', '<i class="fa fa-chevron-left"></i>', true, '' , 'product_cat');
          next_post_link('<div id="single-nav-left">%link</div>', '<i class="fa fa-chevron-right"></i>', true, '', 'product_cat'); 
        }
        else {
          previous_post_link('<div id="single-nav-right">%link</div>', '<i class="fa fa-chevron-left"></i>', true);
          next_post_link('<div id="single-nav-left">%link</div>', '<i class="fa fa-chevron-right"></i>', true); 
        }
        ?>
			</nav><!-- /single-nav -->
			<?php
		} /* image media attachment pages - not in fact used currently, disabled by one of the plugins*/
		elseif (is_attachment()) { ?>
			<nav id="single-nav">
				<div
					id="single-nav-right"><?php previous_image_link('%link', '<i class="fa fa-chevron-left"></i>'); ?></div>
				<div
					id="single-nav-left"><?php next_image_link('%link', '<i class="fa fa-chevron-right"></i>'); ?></div>
			</nav><!-- /single-nav -->
			<?php
		}
		/*
        elseif (  is_woocommerce_activated() ) {
            if (is_product_category())
                echo ("<!--DEBUG TEST OF WOOCOMMERCE PAGINATION -->");
                echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
                    'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                    'format'       => '',
                    'add_args'     => false,
                    'current'      => max( 1, get_query_var( 'paged' ) ),
                    'total'        => 2, // $wp_query->max_num_pages,
                    'prev_text'    => '&larr;',
                    'next_text'    => '&rarr;',
                    'type'         => 'list',
                    'end_size'     => 3,
                    'mid_size'     => 3
                ) ) );
            }
        }
        */
	}
}
?>

<header class="page-header wrap">
<?php

/*
 * first decide:
 * 		are we going to output title (and get the title)
 * 		are we going to output breadcrumb (and get the breadcrumb)
 * 		should the cart be shown in the header row?
 * 		do we have paging?
 */
$do_breadcrumb = true;
$do_cart = false;
$title = inkston_title();
$display_title=$title;

if ( is_woocommerce_activated() ) {
		$do_cart = true;
	if ( is_woocommerce() && !is_product() ) {
		if (is_shop()) {$do_breadcrumb = false;}
		$do_cart = true;
	}
	elseif ( is_product() ) {
		/* product page title omitted and shown below header, cart link should show on same line as breadcrumb */
		$do_cart = true;
	}
	elseif ( is_cart()){
		$do_cart = true;   /* cart link auto switches to checkout link if on cart page */
		$do_breadcrumb = false;
	}
  if (is_checkout() && (is_checkout_pay_page()) ){$do_cart = false;}
}	/* if ( is_woocommerce_activated() ) */


/* override, suppress excess titling on home pages*/
if ( is_home() ) {
	$do_breadcrumb = false;
	$do_cart = false;
	$display_title = '';
  /*turn off wordpress html filter so special home page code isn't munged*/
  remove_filter( 'the_content', 'wpautop' );

}
/*
 *the logic to output the right information in the header is:
 * 		if there is breadcrumb,
 * 			output breadcrumb, cart, paging if any
 * 			output title
 * 		elseif title
 * 				output title, cart, paging if any
 * 		else
 * 			[omit h1 as no title]
 * 			output cart, paging
 * 		endif
 */
/*TODO: testing turn off title in header and applying in boty*/
$display_title='';
if ($display_title!=''){
	if ( $do_breadcrumb != false){
		inkston_breadcrumb();
		inkston_output_paging();
		echo('<h1 class="page-title" title="' . $display_title . '">' . $display_title);if ($do_cart!=false){inkston_cart_link();}echo('</h1>');
	}
	else{
		echo('<h1 class="page-title" title="' . $display_title . '">' . $display_title);
		if ($do_cart!=false){inkston_cart_link();output_ccy_switcher_button();}
		inkston_output_paging();
		echo('</h1>');
	}
}
elseif ( $do_breadcrumb != false){
	inkston_breadcrumb();
	if ($do_cart!=false){inkston_cart_link();output_ccy_switcher_button();}
	inkston_output_paging();
}
else{
	if ($do_cart!=false){inkston_cart_link();output_ccy_switcher_button();}
	inkston_output_paging();
}

/*THUMBNAIL REVIEW - IDEALLY OUTPUT ZOOM with thumbnail */

$thumbnail_id='';
$thumbnail_img='';
//$img_attr='width="auto" height="150" id="header-thumbnail"';
$img_attr='id=header-thumbnail';
$fullsize='';
$rel_lightbox="lightbox";
global $post;
if ( is_single() && !is_attachment() ) {
	$rel_lightbox=$rel_lightbox . '[' . $post->ID.']';
}

if ( is_woocommerce_activated() ) {
	if (is_product_category()) {
		global $wp_query;
		$cat = $wp_query->get_queried_object();
		$thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
		/*
		$thumbnail_img= wp_get_attachment_image_src( $thumbnail_id, 'medium' );
		if  ( $thumbnail_img ) { $thumbnail_img = $thumbnail_img[0]; }
		else { $thumbnail_img = inkston_catch_image(); }
		*/
		$thumbnail_img= wp_get_attachment_image( $thumbnail_id, 'thumbnail', false, $img_attr );
		if (! ($thumbnail_img)){$thumbnail_img= '<img ' . $img_attr . ' src="' . inkston_catch_image() . '" alt="category image" />';}

    //disabled as photoswipe was disabled on category pages
		$fullsize = wp_get_attachment_url($thumbnail_id);
	}
	elseif (is_shop() ) {
		/* do not show random product thumbnail on shop front page */
		$img_attr='width="auto" height="150" id="header-thumbnail"';
		$thumbnail_img = '<img ' . $img_attr . ' src="' . get_template_directory_uri() . '/img/no-image.png" alt="inkston logo" />';
	}
}
if ( ! ( $thumbnail_img )  ) {
	$thumbnail_id = get_post_thumbnail_id();
	$thumbnail_img= wp_get_attachment_image( $thumbnail_id, 'thumbnail', false, $img_attr );
	if (! ($thumbnail_img)){ $thumbnail_img = '<img ' . $img_attr . ' src="' . inkston_catch_image() . '" alt="thumbnail" />';}
  //only get the link for single pages as the photoswipe won't do the single posts page links at the moment
  //if (is_single()){
    if (is_woocommerce_activated() && is_product()){
      $fullsize = 0;
    }else{
      $fullsize = wp_get_attachment_url($thumbnail_id);
    }
  //}
}

if  (! ( $fullsize ) ) {
	echo ( $thumbnail_img );
}
else{
/*THUMBNAIL REVIEW -  OUTPUT THIS TYPE OF STRUCTURE to enable same ZOOM as woocommerce product*/
	echo('<div class="images" style="opacity:1;"><a rel="' . $rel_lightbox . '" href="' . $fullsize . '"  itemprop="image" class="woocommerce-main-image zoom" title="" data-rel="prettyPhoto">' . $thumbnail_img . '</a></div>');
}
?>
</header>
