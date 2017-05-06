<?php
/**
 * The template for Link post format
 * @package Inkston
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

		<div class="entry-content"<?php inkston_bgimage_postformats(); ?>>
	<?php
$textlink = get_the_title();
$content = get_the_content();

$content = apply_filters('the_content', $content);

$repl = array( '<p>', '</p>', '<br />', 'http://' );
$torepl   = array( '', '">' . $textlink . '</a>', '', '' );

$content = '<div class="metka genericon genericon-' . get_post_format() . '" style="color:#FFF;"></div><a style="color:#FFF;text-decoration:none;" href="http://' . str_replace( $repl, $torepl, $content );

echo $content;
?>
		</div><!-- .entry-content -->

	<footer class="entry-meta<?php if ( !is_active_sidebar( 'sidebar-1' ) ) { ?> no-sidebar<?php } ?>">

		<div class="posted">
			<?php inkston_posted_on(); ?>
		</div>
		<div class="extrameta">
			<?php inkston_posted_extra(); ?>
		</div>

		<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<span class="edit-link">', '</span>' ); ?>

	</footer><!-- .entry-meta -->

	<?php else : ?>
		<div class="entry-content"<?php inkston_bgimage_postformats(); ?>>

<?php
$textlink = get_the_title();
$content = get_the_content();

$content = apply_filters('the_content', $content);

$repl = array( '<p>', '</p>', '<br />', 'http://' );
$torepl   = array( '', '">' . $textlink . '</a>', '', '' );

$content = '<h1 class="entry-title"><a style="color:#FFF;text-decoration:none;" href="http://' . str_replace( $repl, $torepl, $content );

echo $content . '</h1><i class="fa fa-link"></i>';
?>
		</div><!-- .entry-content -->
	<?php endif; ?>

</article><!-- #post-## -->
