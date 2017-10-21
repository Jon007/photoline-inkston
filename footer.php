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
<?php 
    $blogid = get_current_blog_id();
    $about = 'https://www.inkston.com/inkston-oriental-arts-materials/about/';
    $manage_subscription = "https://www.inkston.com/manage-subscriptions/";
    switch ($blogid){
        case 1:
            $page_id = inkGetPageID(17);
            if ($page_id){
                $about = get_page_link($page_id);
            }
            $page_id = inkGetPageID(15010);
            if ($page_id){
                $manage_subscription = get_page_link($page_id);
            }
            break;
        case 2:
            $about = 'https://www.inkston.com/community/directory/inkston/';
    }
    $aboutlink = '<a href="' . $about . '">&copy; '.date('Y') . ' <span id="footer-copyright"> ' .
        esc_html( get_theme_mod( 'copyright_txt', 'All rights reserved' ) ) . 
        '</a></span><span class="sep"> &middot; </span>'; 
?>
<div class="clearfix"></div>
<div class="site-info">
    <div class="col about"><?php 
        echo $aboutlink;
        do_action( 'inkston_credits' ); 
    ?></div>
    <div class="col subscribe">
      <span class="search-footer"><a href="#search-footer-bar"><i class="fa fa-search"></i></a></span>
      <div class="footer-subscribe">
        <a class="button" href="<?php echo($manage_subscription); ?>"><i class="fa fa-newspaper-o" aria-hidden="true"></i> subscribe</a></div>
    </div>
    <div class="col social"><?php 
        if ( has_nav_menu( 'social' ) ) {
            wp_nav_menu(array(
                'theme_location'  => 'social',
                // 'container_id'    => 'icon-footer',
                'container_class' => 'icon-footer', 
                'menu_id'         => 'menu-social',
                'menu_class'         => 'menu-social',
                'depth'           => 1,
                'link_before'     => '<span class="screen-reader-text">',
                'link_after'      => '</span>',
                'fallback_cb'     => '',
            ));
        } ?>
   </div><!-- .footer-subscribe -->
</div><!-- .site-info -->

<div id="back-to-top">
<a href="#masthead" id="scroll-up" ><i class="fa fa-chevron-up"></i></a>
</div>

	</footer><!-- #colophon -->
</div><!-- .out-wrap -->

<?php wp_footer(); ?>

</body>
</html>