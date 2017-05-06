<?php
/**
 * The template for Quote post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


	<?php if ( is_single() ) : ?>

		<div class="entry-content">
	<?php
$content = get_the_content();
$content = apply_filters('the_content', $content);

$repl = array('<p>', '</p>', '<br />');
$torepl   = array('<blockquote>', '</blockquote>', '');

$cite = get_the_title();

$content = str_replace( $repl, $torepl, $content );

echo '<cite>' . $cite . ':</cite>';
?>
<div<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' ); ?> style="margin-bottom:10px;padding-top:20px;color:#FFF;background:<?php echo get_theme_mod( 'inkston_link_color', '#2d2d2d' ); ?><?php if  ( $thumbnail ) { ?> url(<?php echo $thumbnail[0]; ?>) no-repeat; background-position: 50%; background-size: cover<?php } ?>;">
<?php
echo $content . '</div>';
?>
		</div><!-- .entry-content -->

	<footer class="entry-meta<?php if ( !is_active_sidebar( 'sidebar-1' ) ) { ?> no-sidebar<?php } ?>">
		<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->

	<?php else : ?>

		<div class="entry-content"<?php inkston_bgimage_postformats(); ?>>

			<i class="fa fa-quote-left"></i>
			<h4><em><?php inkston_excerpt( 40 ); ?></em></h4>
			<h5>&ndash; <?php the_title(); ?> &ndash;</h5>

		</div><!-- .entry-content -->
		
	<?php endif; ?>

</article><!-- #post-## -->
