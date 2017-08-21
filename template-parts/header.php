<?php
/**
 * @package Inkston
 * 
 * moved functions to functions.php because this page not called in some circumstances.
 */
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
		if (is_shop()) {
            $do_breadcrumb = false;
            $do_cart = true;
        //finally cart must always be there on woocommerce page since the pate will be cached..
        //} elseif (sizeof(WC()->cart->cart_contents) == 0){
        //    $do_cart = false;        
        }
	}
	elseif ( is_product() ) {
		/* product page title omitted and shown below header, cart link should show on same line as breadcrumb */
		$do_cart = true;
	}
	elseif ( is_cart()){
		$do_cart = true;   /* cart link auto switches to checkout link if on cart page */
		$do_breadcrumb = false;
//	} elseif (sizeof(WC()->cart->cart_contents) == 0){
//		$do_cart = false;
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

    echo('<div id="header-thumbnail" class="fixbox">');
    $feature_posts = get_featured_posts();
    $i = 0;
    foreach ( $feature_posts as $key => $post ) {	
		setup_postdata( $post ); 
        get_template_part( 'content', 'tile-thumb' );
        $i++;
        if ($i>7){break;}
    }
    echo('</div>');
} else {
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
    }


    if  (! ( $fullsize ) ) {
        echo ( $thumbnail_img );
    }
    else{
        echo('<div class="images" style="opacity:1;"><a rel="' . $rel_lightbox . '" href="' . $fullsize . '"  itemprop="image" class="woocommerce-main-image zoom" title="" data-rel="prettyPhoto">' . $thumbnail_img . '</a></div>');
    }
}
?>
</header>
