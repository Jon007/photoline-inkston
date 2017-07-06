<?php
/**
 * @package Inkston
 */
?>

<?php
$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
if  ( $thumbnail ) { $thumbnail = $thumbnail[0]; }
else { $thumbnail = inkston_catch_image(); }
/* $extract = inkston_get_excerpt( 25 );  just plain text, use inkston_excerpt( 40 ); for html..*/
$title=get_the_title();
$class="tile h-entry";
$beforelink='';
if ($post->post_type=='product'){
    $product = wc_get_product($post);
    $beforelink.=wc_get_rating_html($product->get_average_rating(), $product->get_rating_count());
    if ( $product->is_on_sale() ){
        $beforelink .= apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product );
    }
}

if  ( strrpos( $thumbnail, "no-image.png") !== false  ) {$class .= ' nopic';}  
?><div class="<?php echo($class); ?>" id="post-<?php the_ID(); ?>" style="background-image:url('<?php echo $thumbnail; ?>');"><?php echo($beforelink); ?><a href="<?php the_permalink(); ?>" rel="bookmark"><h3><?php echo($title); ?></h3><p class="p-summary"><?php echo(inkston_get_excerpt( 13));?></p></a></div>