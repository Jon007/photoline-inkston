<?php
/**
 * @package Inkston
 */
?>

<?php
$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
if  ( $thumbnail ) { $thumbnail = $thumbnail[0]; }
else { $thumbnail = inkston_catch_image(); }
/* $extract = inkston_get_excerpt( 25 );  just plain text, use inkston_excerpt( 40 ); for html..*/
$class="tile h-entry";
$tile_content = '<h3>' . get_the_title() . '</h3><p class="p-summary">' . inkston_get_excerpt() . '</p>';
if (is_search()){
  if (function_exists('relevanssi_highlight_terms')) {
      $tile_content = relevanssi_highlight_terms($tile_content, get_search_query());
  }
}

if  ( strrpos( $thumbnail, "no-image.png") !== false  ) {$class .= ' nopic';}  
?><div class="<?php echo($class); ?>" id="post-<?php the_ID(); ?>" style="background-image:url('<?php echo $thumbnail; ?>')"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo($tile_content); ?></a></div>