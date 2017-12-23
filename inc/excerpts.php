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
            $product = wc_get_product($post);
            $output .= $product->get_price_html();
        }
        return $output;
    }
    function inkston_filter_excerpt($excerpt){        
        if (!$excerpt){
            global $post;
            if ($post){
                if ($post->post_type =='wpbdp_listing'){
                    $excerpt = $post->post_content;
                } else {
                    $excerpt = get_the_content() ;
                }
                $excerpt = wp_trim_words( strip_shortcodes( $excerpt ), inkston_excerpt_length(36));
            }
        }
        if ( ( is_feed() ) || ( stripos($_SERVER['REQUEST_URI'], '/feed') ) ) 
        {
            global $post;
            $excerpt = strip_shortcodes( $excerpt );
            $excerpt .= ink_wp_hashtags($post);
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
