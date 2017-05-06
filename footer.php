<?php
/**
 * The template for displaying the footer.
 * @package Inkston
 */
?>

	</div><!-- #content -->
</div><!--#wrap-content-->

<div class="clearfix"></div>

<div class="out-wrap site-footer">

	<footer id="colophon" class="wrap site-footer" role="contentinfo">

<?php if ( is_active_sidebar( 'footer1' ) || is_active_sidebar( 'footer2' ) || is_active_sidebar( 'footer3' ) ) { ?>

<div class="clearfix">
 <div class="col">
	<?php dynamic_sidebar('footer1'); ?>
</div>
 <div class="col">
	<?php dynamic_sidebar('footer2'); ?>
</div>
 <div class="col">
	<?php dynamic_sidebar('footer3'); ?>
</div>
</div><!--.grid3-->

<div class="footer-border"></div>

<?php } ?>

<div id="search-footer-bar">
	<?php get_search_form(); ?>
</div>

		<div class="site-info">
<div class="clearfix">
 	<div class="col"><?php 
  $tr_id = pll_get_post(17); // translate the About page in the current language
  $about = get_page_link($tr_id); // displays the link
  echo '<a href="' . $about . '">&copy; '.date('Y'); ?> <span id="footer-copyright"><?php echo esc_html( get_theme_mod( 'copyright_txt', 'All rights reserved' ) ); ?></a></span><span class="sep"> &middot; </span>
		<?php do_action( 'inkston_credits' ); ?>
	</div>
	 <div class="col" width="33%">
		<div class="search-footer">
			<a href="#search-footer-bar"><i class="fa fa-search"></i></a>
		</div>
	</div><!-- .col -->
	 <div class="alignright">
<?php if ( has_nav_menu( 'social' ) ) {
wp_nav_menu(
	array(
	'theme_location'  => 'social',
	// 'container_id'    => 'icon-footer',
	'container_class' => 'icon-footer', 
	'menu_id'         => 'menu-social',
	'depth'           => 1,
	'link_before'     => '<span class="screen-reader-text">',
	'link_after'      => '</span>',
	'fallback_cb'     => '',
	)
);
} ?>
	</div><!-- .col -->
</div><!--grid2-->
		</div><!-- .site-info -->

            <div id="back-to-top">
	<a href="#masthead" id="scroll-up" ><i class="fa fa-chevron-up"></i></a>
            </div>

	</footer><!-- #colophon -->
</div><!-- .out-wrap -->

<?php wp_footer(); ?>

</body>
</html>