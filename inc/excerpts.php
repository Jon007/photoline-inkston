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
        if (is_search() && 13==$length){$length=36;}
        $id = $post->ID;
        if ( has_excerpt( $id ) ) {
            // $post->post_excerpt
            $output = wp_trim_words( strip_shortcodes( get_the_excerpt( $id ) ), $length);
            //$output = wp_trim_words( strip_shortcodes( get_the_excerpt( $id ) ), 20) . "\r\n";
            //$output .= wp_trim_words( strip_shortcodes( get_the_content( $id ) ), $length);
        } else {
            if ($post->post_type =='wpbdp_listing'){
                $output = $post->post_content;
            } else {
                $output = get_the_content( $id ) ;
            }
            $output = wp_trim_words( strip_shortcodes( $output ), $length);
        }
        if ( (! is_search()) && ($post->post_type=='product') ){
            $product = wc_get_product($post);
            $output .= $product->get_price_html();
        }
        return $output;
    }
}


/**
 * Change default excerpt read more style
*/
if ( !function_exists( 'inkston_excerpt_more' ) ) {
	function inkston_excerpt_more($more) {
		global $post;
		return '...';
	}
}
add_filter( 'excerpt_more', 'inkston_excerpt_more' );