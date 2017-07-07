<?php
/**
 * @package Inkston
 */

?>

<?php
$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
if ($thumbnail) {
    $thumbnail = $thumbnail[0];
} else {
    $thumbnail = inkston_catch_image();
}
/* $extract = inkston_get_excerpt( 25 );  just plain text, use inkston_excerpt( 40 ); for html.. */
$class = "tile h-entry";
$title = get_the_title();
$excerpt = inkston_get_excerpt();

if ((is_search()) && (function_exists('relevanssi_highlight_terms'))) {
    $search = get_search_query();
    $tile_content = '<h3>' . relevanssi_highlight_terms($title, $search) . '</h3><p class="p-summary">'
        . relevanssi_highlight_terms($excerpt, $search);
    global $post;
    if ($post->post_type=='product'){
        $product = wc_get_product($post);
        $tile_content .= ' ' . $product->get_price_html();
    }
    $tile_content .= '</p>';
} else {
    $tile_content = '<h3>' . $title . '</h3><p class="p-summary">' . $excerpt . '</p>';
}

if (strrpos($thumbnail, "no-image.png") !== false) {
    $class .= ' nopic';
}

$beforelink='';
if ($post->post_type=='product'){
    $product = wc_get_product($post);
    $beforelink .= wc_get_rating_html($product->get_average_rating(), $product->get_rating_count());
    if ( $product->is_on_sale() ){
        $beforelink .= apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product );
    }
}

?><div class="<?php echo($class); ?>" id="post-<?php the_ID(); ?>" style="background-image:url('<?php echo $thumbnail; ?>')"><?php echo($beforelink); ?><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo($tile_content); ?></a></div>