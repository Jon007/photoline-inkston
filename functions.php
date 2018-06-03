<?php
/**
 * Theme functions and definitions
 *
 * @package Inkston
 */
/**
 * Query WooCommerce activation
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		return class_exists( 'woocommerce' ) ? true : false;
	}

}

/**
 * Set the content width for theme design
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /*  1060 TODO: should be 1200 if no sidebar otherwise lower  */
}

if ( ! function_exists( 'inkston_setup' ) ) :
	function inkston_setup() {
		/** Markup for search form, comment form, and comments valid HTML5. */
		add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		/* Make theme available for translation */
		load_theme_textdomain( 'photoline-inkston', get_template_directory() . '/languages' );

		//wooCommerce3 gallery features
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		/* Add default posts and comments RSS feed links to head */
		add_theme_support( 'automatic-feed-links' );

		/* Enable support for Excerpt on Pages. See http://codex.wordpress.org/Excerpt */
		add_post_type_support( 'page', 'excerpt' );

		//allow forums to have featured images
		add_post_type_support( 'forum', array( 'thumbnail' ) );
		add_post_type_support( 'topic', array( 'thumbnail' ) );


		/* Enable support WooCommerce */
		add_theme_support( 'woocommerce' );

		/*
		 * Let WordPress 4.1+ manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 300, 300, true );
		//medium large is built in size 768*unconstrained
		//add_image_size( 'medium_large', 600, 600, false);
		/**
		 * This theme uses wp_nav_menu() in one location.
		 */
		register_nav_menus( array(
			'top'		 => __( 'Top Menu', 'photoline-inkston' ),
			'hamburger'	 => __( 'Hamburger Menu', 'photoline-inkston' ),
			'primary'	 => __( 'Primary Menu', 'photoline-inkston' ),
			'social'	 => __( 'Social Menu', 'photoline-inkston' ),
			'footer'	 => __( 'Footer Menu', 'photoline-inkston' ),
		) );
		/* this code handles non-Polylang subsite allowing different language menus */
		if ( ! function_exists( 'pll_the_languages' ) ) {
			register_nav_menus( array(
				'topfr_FR'		 => __( 'Top Menu', 'photoline-inkston' ) . ' Français',
				'topes_ES'		 => __( 'Top Menu', 'photoline-inkston' ) . ' Español',
				'topde_DE'		 => __( 'Top Menu', 'photoline-inkston' ) . ' Deutsche',
				'footerfr_FR'	 => __( 'Footer Menu', 'photoline-inkston' ) . ' Français',
				'footeres_ES'	 => __( 'Footer Menu', 'photoline-inkston' ) . ' Español',
				'footerde_DE'	 => __( 'Footer Menu', 'photoline-inkston' ) . ' Deutsche',
			) );
		}
		/**
		 * Setup the WordPress core custom header image.
		 */
		add_theme_support( 'custom-header', apply_filters( 'inkston_custom_header_args', array(
			'header-text'			 => true,
			'default-text-color'	 => '1a1919',
			'width'					 => 1020,
			'height'				 => 450,
			'flex-height'			 => true,
			'flex-width'			 => true,
			'wp-head-callback'		 => 'inkston_header_style',
			'admin-head-callback'	 => 'inkston_admin_header_style',
			'admin-preview-callback' => 'inkston_admin_header_image',
		) ) );

		/**
		 * Setup the WordPress core custom background feature.
		 */
		add_theme_support( 'custom-background', apply_filters( 'inkston_custom_background_args', array(
			'default-color' => 'ffffff',
		) ) );
	}

endif; // inkston_setup
add_action( 'after_setup_theme', 'inkston_setup' );
/**
 * Register widgetized area and update sidebar with default widgets
 */
function inkston_widgets_init() {
	register_sidebar( array(
		'name'			 => __( 'Sidebar Posts', 'photoline-inkston' ),
		'id'			 => 'sidebar-1',
		'before_widget'	 => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'	 => '</aside>',
		'before_title'	 => '<p class="widget-title">',
		'after_title'	 => '</p>',
	) );
	register_sidebar( array(
		'name'			 => __( 'Sidebar Pages', 'photoline-inkston' ),
		'id'			 => 'sidebar-2',
		'before_widget'	 => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'	 => '</aside>',
		'before_title'	 => '<p class="widget-title">',
		'after_title'	 => '</p>',
	) );
	register_sidebar( array(
		'name'			 => __( 'Footer1', 'photoline-inkston' ),
		'description'	 => __( 'Located in the footer left.', 'photoline-inkston' ),
		'id'			 => 'footer1',
		'before_title'	 => '<h5 class="widget-title">',
		'after_title'	 => '</h5>',
		'before_widget'	 => '<div class="widget">',
		'after_widget'	 => '</div>'
	) );
	register_sidebar( array(
		'name'			 => __( 'Footer2', 'photoline-inkston' ),
		'description'	 => __( 'Located in the footer center.', 'photoline-inkston' ),
		'id'			 => 'footer2',
		'before_title'	 => '<h5 class="widget-title">',
		'after_title'	 => '</h5>',
		'before_widget'	 => '<div class="widget">',
		'after_widget'	 => '</div>'
	) );
	register_sidebar( array(
		'name'			 => __( 'Footer3', 'photoline-inkston' ),
		'description'	 => __( 'Located in the footer right.', 'photoline-inkston' ),
		'id'			 => 'footer3',
		'before_title'	 => '<h5 class="widget-title">',
		'after_title'	 => '</h5>',
		'before_widget'	 => '<div class="widget">',
		'after_widget'	 => '</div>'
	) );
}

add_action( 'widgets_init', 'inkston_widgets_init' );
/**
 * =Enqueue scripts
 */
function inkston_scripts() {
	$suffix			 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$template_uri	 = get_template_directory_uri();
	$scriptname		 = '/inkston' . $suffix . '.css';
	wp_enqueue_style( 'inkston-style', $template_uri . $scriptname, array(), filemtime( get_stylesheet_directory() . $scriptname ) );
	//font-genericons currently enables cart symbol
	wp_enqueue_style( 'font-genericons', $template_uri . '/genericons/genericons.css' );
	//includes the navigation arrows - local version appeared slow..
	//wp_enqueue_style( 'font-awesome', $template_uri . '/font-awesome/css/font-awesome.min.css?v=4.4');
	wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );

	wp_enqueue_script( 'skip-link-focus-fix', $template_uri . '/js/skip-link-focus-fix' . $suffix . '.js', array(), '25062015', true );

	$scriptname = '/js/main' . $suffix . '.js';
	wp_enqueue_script( 'inkston-main', $template_uri . $scriptname, array( 'jquery' ), filemtime( get_stylesheet_directory() . $scriptname ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'keyboard-image-navigation', $template_uri . '/js/keyboard-image-navigation.js', array( 'jquery' ), '25062015' );
	}

	if ( is_woocommerce_activated() ) {
		//if ( (!is_cart()) && ( !is_checkout()) ){
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET[ 'pay_for_order' ] ) && ! is_add_payment_method_page() ) {
			wp_dequeue_style( 'wjecf-style' );
			wp_dequeue_script( 'wjecf-free-products' );
		}
	}
	?><script type="text/javascript">window.loginurl = '<?php
	$referer = (isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : '');
	echo(wp_login_url( $referer ))
	?>';</script><?php
}

add_action( 'wp_enqueue_scripts', 'inkston_scripts', 1000 );
function inkston_dequeue_script() {

	if ( is_woocommerce_activated() ) {
		wp_dequeue_script( 'wc-add-to-cart-variation' );
		wp_deregister_script( 'wc-add-to-cart-variation' );

		if ( is_single() && (is_product()) ) {
			$suffix			 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$template_uri	 = get_template_directory_uri();
			$scriptname		 = '/js/add-to-cart-variation' . $suffix . '.js';
			wp_register_script( 'wc-add-to-cart-variation', $template_uri . $scriptname, array( 'jquery', 'wp-util' ), filemtime( get_stylesheet_directory() . $scriptname ), true );
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		/* avoid refreshing cart fragments on pages which are not cached anyway...
		  especially cart/checkout pages are already over-heavy due to not being cached and extra stripe scripts etc */
		if ( is_cart() || is_checkout() || isset( $_GET[ 'pay_for_order' ] ) || is_add_payment_method_page() || is_account_page() ) {
			wp_dequeue_script( 'wc-cart-fragments' );
		}
	}
//    wp_dequeue_script( 'wpla_product_matcher');
}

add_action( 'wp_print_scripts', 'inkston_dequeue_script', 1000 );

/*
 * dequeue all unnecessary csss
 */
function inkston_dequeue_styles() {
	wp_dequeue_style( 'photoswipe-default-skin' );
	wp_dequeue_style( 'photoswipe' );

	wp_dequeue_style( 'decent-comments-widget' );

	if ( is_woocommerce_activated() ) {
		if ( ! is_single() || ( ! is_product()) ) {
			wp_dequeue_style( 'woosb' );
		}
	}
}

add_action( 'wp_print_styles', 'inkston_dequeue_styles', 1000 );
//$content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );


/**
 * Breadcrumbs
 */
require get_template_directory() . '/inc/breadcrumbs.php';

/**
 * Custom Pagination
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Custom gallery layout
 */
require( get_template_directory() . '/inc/gallery-layout.php');

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Contextual Help Function File
 */
//require( get_template_directory() . '/inc/contextual-help.php' );

/**
 * Welcome Screen
 */
//require_once( get_template_directory() . '/inc/welcome.php' );

/**
 * Theme hooks
 */
// see template-tags.php
//add_action( 'before_content', 'inkston_before_content' );
//add_action( 'before_loop_posts', 'inkston_before_loop_posts' );
//add_action( 'after_main_posts', 'inkston_after_main_posts' );
add_action( 'display_submenu_sidebar', 'inkston_get_submenu' );
add_action( 'inkston_credits', 'inkston_txt_credits' );
function output_cart( $do_cart ) {
	if ( ! $do_cart ) {
		return;
	}
	//handle that these functions can now be turned off in inkston_integration and may not exist
	if ( function_exists( 'inkston_cart_link' ) ) {
		inkston_cart_link();
	}
	if ( function_exists( 'output_ccy_switcher_button' ) ) {
		output_ccy_switcher_button();
	}
}

//include_once( '_fn/_plug.php' ); //functions moved to inkston-integration plugin
include_once( 'functions2.php' );
