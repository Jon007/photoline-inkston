<?php
/**
 * The template for displaying the footer.
 * @package Inkston
 */
?>

	</div><!-- #content -->
</div><!--#wrap-content-->
</div><!--#wrap-content was not getting closed, there is another div..-->

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
    //mostly replacing with menus to avoid hard coded links 
    $blogid = get_current_blog_id();
    //$about = 'https://www.inkston.com/inkston-oriental-arts-materials/about/';
    //$manage_subscription = "https://www.inkston.com/manage-subscriptions/";
    //$wishlist = "https://www.inkston.com/wishlist/";
    $url_about = network_site_url('inkston-oriental-arts-materials/about/');
    $url_manage_subscription = network_site_url('manage-subscriptions/');
    $url_wishlist = network_site_url('wishlist/');
    
    switch ($blogid){
        case 1:
            $page_id = inkGetPageID(17);
            if ($page_id){$url_about = get_page_link($page_id);}
            $page_id = inkGetPageID(15010);
            if ($page_id){$url_manage_subscription = get_page_link($page_id);}
            $page_id = inkGetPageID(16780);
            if ($page_id){$url_wishlist = get_page_link($page_id);}
            break;
        case 2:
            //$url_about = 'https://www.inkston.com/community/directory/inkston/';
            $locale = get_locale();
            switch ($locale){
                case 'fr_FR':
                    $url_manage_subscription = network_site_url('fr/gerer-les-abonnements/');                  
                    $url_wishlist = network_site_url('fr/liste-de-souhaits/');                  
                    $url_about = network_site_url('fr/portail/histoire-inkston/');                  
                    break;
                case 'es_ES':
                    $url_manage_subscription = network_site_url('es/manage-subscriptions-2/');                  
                    $url_wishlist = network_site_url('es/lista-de-deseos/');                  
                    $url_about = network_site_url('es/inkston-arte-y-artesania-oriental/la-historia-de-inkston/');       
                    break;
    }
        default:
            //can't work properly, at least as far as translations are concerned, use default english pages
    }
    /*
    //'<span class="sep"> &middot; </span>'
    $tandclink = '<a href="' . $url_tandc . '"> '. __('terms', 'photoline-inkston') .
        '</span></a>';
    $privacylink = '<a href="' . $url_privacy . '"> '. __('privacy', 'photoline-inkston') .
        '</span></a>';
     * */
?>
<div class="clearfix"></div>
<div class="site-info">
    <div class="col footer-menu">
<?php 
    $footermenu = 'footer' ;  
    /* this code handles non-Polylang subsite allowing different language menus */
    if ( ! function_exists( 'pll_the_languages' ) ) {
        $locale = get_locale();
        switch ($locale){
            case 'fr_FR':
                if ( has_nav_menu( 'footerfr_FR' ) ) {
                    $footermenu = 'footerfr_FR' ;                  
                }            
                break;
            case 'es_ES':
                if ( has_nav_menu( 'footeres_ES' ) ) {
                    $footermenu = 'footeres_ES' ;                  
                }            
                break;
            case 'de_DE':
                if ( has_nav_menu( 'footerde_DE' ) ) {
                    $footermenu = 'footerde_DE' ;                  
                }            
                break;
        }
    }
    if ( has_nav_menu( $footermenu ) ) { ?>
		<?php
        echo '<span id="footer-copyright">&copy; '.date('Y') . '</span> ';
		wp_nav_menu(
			array(
			'theme_location'  => $footermenu,
			'menu_id'         => 'menu-footer',
			'depth'           => 1,
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'fallback_cb'     => '',
			)
		);
        echo '<br/>';
        do_action( 'inkston_credits' ); 
    } else {
        $aboutlink = '<a href="' . $url_about . '">&copy; '.date('Y') . ' <span id="footer-copyright"> ' .
            esc_html( get_theme_mod( 'copyright_txt', 'All rights reserved' ) ) . 
            '</span></a>';
        echo $aboutlink;
        /*
            echo '<span class="sep"> &middot; </span>';
            echo $tandclink;
            echo '<span class="sep"> &middot; </span>';
            echo $privacylink;
         */
    } ?>
    </div>
  
    <div class="col subscribe">
      <span class="search-footer" id="search-footer"><a href="#search-footer-bar"><i class="fa fa-search"></i></a></span>
      <div class="footer-subscribe">
        <a class="button" href="<?php echo($url_manage_subscription); ?>"><i class="fa fa-newspaper-o" aria-hidden="true"></i> subscribe</a></div>
    </div>
    <div class="col social">
      <span class="wishlist_products_counter" title="<?php _e('View saved wishlist items', 'photoline-inkston')
          ?>"><a href="<?php echo($url_wishlist); 
      ?>"><i class="fa fa-heart-o"></i><span class="wishlist_products_counter_number"></span></a></span>
        <?php 
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