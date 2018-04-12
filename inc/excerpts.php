<?php
/**
 * Excerpt related functions
 * Based on wp_trim_words
 * Learn more at http://codex.wordpress.org/Function_Reference/wp_trim_words
 */
/* todo - have increased excerpt length to 20*/

if ( !function_exists( 'inkston_excerpt' ) ) {
	function inkston_excerpt($length=30, $readmore=false ) {
		global $post;
		$id = $post->ID;
    $output = '<p class="inkston-excerpt">' . inkston_get_excerpt($length);
    if ( $readmore == true ) {
      $readmore_link = '<span class="inkston-readmore"><a href="'. get_permalink( $id ) .'" title="'. __('reading', 'photoline-inkston' ) .'" rel="bookmark">'. __('Read more', 'photoline-inkston' ) .'</a></span>';
      $output .= apply_filters( 'inkston_readmore_link', $readmore_link );
    }
    $output .= '</p>';
    echo $output;
/*
    if ( has_excerpt( $id ) ) {
			$output = '<p class="inkston-excerpt">' . wp_trim_words( strip_shortcodes( get_the_excerpt( $id ) ), 20) . '</p>';
			$output .= '<p>' . wp_trim_words( strip_shortcodes( get_the_content( $id ) ), $length) . '</p>';
		} else {
			$output = '<p>' . wp_trim_words( strip_shortcodes( get_the_content( $id ) ), $length) . '</p>';
			if ( $readmore == true ) {
				$readmore_link = '<span class="inkston-readmore"><a href="'. get_permalink( $id ) .'" title="'. __('reading', 'photoline-inkston' ) .'" rel="bookmark">'. __('Read more', 'photoline-inkston' ) .'</a></span>';
				$output .= apply_filters( 'inkston_readmore_link', $readmore_link );
			}
		}
		echo $output;
 */
	}
}

if ( !function_exists( 'inkston_get_excerpt' ) ) {
    function inkston_get_excerpt($length=25, $readmore=false ) {
        global $post;
        $output = '';
        if (is_search() && 13==$length){$length=36;}
        $id = $post->ID;
        if ( has_excerpt( $id ) ) {
            $output = get_the_excerpt( $id );
        } 
        if ($output == '') {
            if ($post->post_type =='wpbdp_listing'){
                $output = $post->post_content;
            } else {
                $output = get_the_content() ;
            }
        }
        if ($output && $output!=''){
            $output = wp_trim_words( strip_shortcodes( $output ), $length);
        }
        if ( (! is_search()) && ($post->post_type=='product') ){
            if (is_woocommerce_activated()){
                $product = wc_get_product($post);
                //quick check for product with no description
                if ($output==''){
                   $output= $product->get_name(); 
                }
                $output .= $product->get_price_html();
            }
        }
        return $output;
    }
    function inkston_filter_excerpt($excerpt){        
        global $post;
        if ($post){
            if (!$excerpt || $excerpt=='Spread the love'){  //bogus excerpt creeping in from SuperSocializer
                if ($post->post_type =='wpbdp_listing'){
                    $excerpt = $post->post_content;
                } else {
                    $excerpt = get_the_content() ;
                }
                $excerpt = wp_trim_words( strip_shortcodes( $excerpt ), inkston_excerpt_length(36));
            }

            //if a woocommerce product always add price and buy link to excerpt
            if (($post->post_type=='product') && (is_woocommerce_activated())) {
                $product = wc_get_product($post);
                if ($product){
                    //quick check for product with no description
                    if ($excerpt==''){
                        $excerpt= $product->get_name(); 
                        $excerpt .= ' ' . $product->get_price_html();
                    }
                }
            }
        } 
        
        if ( ( is_feed() ) || ( stripos($_SERVER['REQUEST_URI'], '/feed') ) ) 
        {
            $excerpt = strip_shortcodes( $excerpt );
            if ($post){
                $excerpt .= ink_wp_hashtags($post);
            }
        }
        return $excerpt;
    }
    add_filter( 'get_the_excerpt', 'inkston_filter_excerpt', 10, 1);
}


/**
 * Change default excerpt read more style
*/
if ( !function_exists( 'inkston_excerpt_more' ) ) {
	function inkston_excerpt_more($more) {
		return '...';
	}
}
add_filter( 'excerpt_more', 'inkston_excerpt_more' );

/*
 * Yoast enable product price parameter
 */
function register_replacements(){
    wpseo_register_var_replacement(
        'wc_price', 'get_product_price', 'basic', 'The product\'s price.'
    );
    wpseo_register_var_replacement(
        'home', 'get_home_url', 'basic', 'The current language home page.'
    );
}
add_action( 'wpseo_register_extra_replacements',  'register_replacements' );
function get_seo_home(){
    return get_site_url(1);
    //return get_home_url(1, '', 'https');
}
function get_product_price()
{
    global $post;
    if (!$post){return;}
    
    //if a woocommerce product always add price and buy link to excerpt
    if (($post->post_type=='product') && (is_woocommerce_activated())) {
        $product = wc_get_product($post);
        if ($product){
            if (method_exists($product, 'get_price')) {
                $price_string = $product->get_price('edit');
                if ($product->is_on_sale()){
                    $price_string = __( 'Sale!', 'woocommerce' ) . $price_string;                
                }
                return wp_strip_all_tags(wc_price($product->get_price()), true);
            }
        }
    }
    return '';
}
