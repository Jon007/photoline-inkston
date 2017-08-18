<?php
/**
 * The Header Theme
 * @package Inkston
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
		<![endif]-->

<?php wp_head(); ?>
</head>

<?php
	$home_image = get_header_image();
	$header_text_color = get_header_textcolor();
	$header_text_style='';
	if (($header_text_color!='blank') && ($header_text_color!='')) {
		$header_text_style=' style="color:#' . $header_text_color . '"';
	 }
	//$logo = get_theme_mod( 'logo_upload' );
  $logo = str_replace( array( 'http:', 'https:' ), '', get_theme_mod( 'logo_upload' )); 
?>

<body <?php body_class(); ?>  ontouchstart="">

<div id="head-wrap" class="out-wrap" style="background: <?php echo esc_attr( get_theme_mod( 'inkston_headerbg_color', '#ffffff' ) ); ?><?php if( !empty($home_image) ) { ?> url(<?php echo esc_url( $home_image );?>) no-repeat 50%;background-size: cover<?php } ?>;">

<?php if ( has_nav_menu( 'hamburger' ) ) { 
		wp_nav_menu(
			array(
			'theme_location'  => 'hamburger',
			'menu_id'         => 'menu-ham',
			'depth'           => 1,
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'fallback_cb'     => '',
			)
		);    
}

    $topmenu = 'top' ;  
    $locale = get_locale();
    switch ($locale){
        case 'fr_FR':
            if ( has_nav_menu( 'topfr_FR' ) ) {
                $topmenu = 'topfr_FR' ;                  
            }            
            break;
        case 'es_ES':
            if ( has_nav_menu( 'topes_ES' ) ) {
                $topmenu = 'topes_ES' ;                  
            }            
            break;
    }

    if ( has_nav_menu( $topmenu ) ) { ?>
	<div class="top-menu">
		<?php
		wp_nav_menu(
			array(
			'theme_location'  => $topmenu,
			'menu_id'         => 'menu-top',
			'depth'           => 1,
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'fallback_cb'     => '',
			)
		);
?><form id="topsearch" action="<?php echo( trailingslashit(esc_url( home_url())))?>"><input id="topsearch-input" name="s" /> <a id="topsearchanchor" class="fa fa-search"></a></form><?php
		inkston_cart_link("top-menu header-cart") ?>
	</div>
<?php } ?>

	<div id="wrap-header" class="wrap hfeed site">
		<?php do_action( 'before' ); ?>
		<header id="masthead" class="site-header" role="banner">
          
			<div class="site-branding clearfix"><div id="logo"><?php
					 if ( !is_front_page() ) { 
						if ( !empty($logo) ) { 
?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( $logo); ?>" alt="<?php bloginfo( 'name' ); ?>" <?php if( false === get_theme_mod( 'inkston_frame_logo' ) ) { ?>class="roundframe" <?php } ?> /></a><?php
            } else { 
						?><div class="title-group"><h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" <?php echo($header_text_style); ?>><?php bloginfo( 'name' ); ?></a></h1>
							<h2 class="site-description" <?php echo($header_text_style); ?>><?php bloginfo( 'description' ); ?></h2></div><?php 
            }
           } else { 
             //is front page           
            if ( !empty($logo) ) {
							?><img src="<?php echo esc_url( $logo); ?>" alt="<?php bloginfo( 'name' ); ?>" <?php if( false === get_theme_mod( 'inkston_frame_logo' ) ) { ?>class="roundframe"<?php } ?> />
            <?php } else { ?>
						<div class="title-group">
							<h1 class="site-title" <?php echo($header_text_style); ?>><?php bloginfo( 'name' ); ?></h1>
							<h2 class="site-description" <?php echo($header_text_style); ?>><?php bloginfo( 'description' ); ?></h2>
						</div>
            <?php } //!empty ?>
            <?php } //!is_front_page() ?>
</div></div>
			<?php if ( has_nav_menu('primary') ) { ?>
				<nav id="site-navigation" class="main-navigation" role="navigation">
				<h1 class="menu-toggle"><span class="screen-reader-text"><?php _e( 'Menu', 'photoline-inkston' ); ?></span></h1>
				<!-- navigation -->
				<?php wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_class' => 'nav-menu',
							'container'       => 'div',
							'container_class' => 'menu-main'
						) );
				?>
				</nav><!-- #site-navigation -->
			<?php	} // has_nav_menu ?>
		</header><!-- #masthead -->
	</div><!-- #wrap-header -->
<?php
if ( !is_front_page() ) {
	get_template_part( 'template-parts/header' );
} ?>
</div><!-- .out-wrap -->

<div id="wrap-content" class="wrap">
	<div id="content" class="site-content">