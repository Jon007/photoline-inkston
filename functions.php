<?php
/**
 * Theme functions and definitions
 *
 * @package Inkston
 */

/**
 * Set the content width for theme design
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /*  1060 TODO: should be 1200 if no sidebar otherwise lower  */
}

			/**
			 * MODIFY woocommerce_single_product_summary hook.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */

//try to add additional cart button BEFORE product descriptions
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 6 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 7 );
add_action( 'woocommerce_after_single_product_summary', 'inkston_woocommerce_after_single_product', 90 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

/*
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 60 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 61 );
*/

/* customize cart buttons on archive screens _template_loop_add_to_cart */ 
function custom_woocommerce_product_add_to_cart_text() { 
  global $product; 
  $product_type = $product->product_type;
  switch ( $product_type ) {
    case 'external':
    return __( 'Buy', 'photoline-inkston' );
    break;
    case 'grouped':
    return __( 'View', 'photoline-inkston' );
    break;
    case 'simple':
    return __( 'Add', 'photoline-inkston' );
    break;
    case 'variable':
    return __( 'Choose', 'photoline-inkston' );
    break;
    default:
    return __( 'Read', 'photoline-inkston' );
  }
}
add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' ); /** * custom_woocommerce

//Disable Emojis
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/*disable contact form 7 scripts*/
add_filter( 'wpcf7_load_css', '__return_false' );
add_filter( 'wpcf7_load_js', '__return_false' );

/*
 * $content_width doesn't actually change the content width, it's an old variable 
 * used by wordpress to limit image/video sizes by come components
if ( ! function_exists( 'inkston_content_width' ) ) :
	function inkston_content_width() {
		global $content_width;
		if ( is_front_page() || is_page_template( array( 'template-fullpage.php' , 'template-posttiles.php'  ) ) ) {
		if ( is_page_template( array( 'template-fullpage.php' , 'template-posttiles.php'  ) ) ) {
			$content_width = 1200;
		}
	}

endif;
add_action( 'template_redirect', 'inkston_content_width' );
*/
if ( ! function_exists( 'inkston_setup' ) ) :
function inkston_setup() {
	 /** Markup for search form, comment form, and comments valid HTML5.*/
	add_theme_support( 'html5', array('comment-form', 'comment-list', 'gallery', 'caption') );

	/* Make theme available for translation */
	load_theme_textdomain( 'photoline-inkston', get_template_directory() . '/languages' );

	/* Add default posts and comments RSS feed links to head */
	add_theme_support( 'automatic-feed-links' );

  /* Enable support for Excerpt on Pages. See http://codex.wordpress.org/Excerpt */
  add_post_type_support( 'page', 'excerpt' );

  /* Enable support WooCommerce */
  add_theme_support( 'woocommerce' );

	/*
	 * TODO: review/remove
   * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	add_editor_style( array( 'css/editor-style.css', inkston_fonts_url() ) );
	 */

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
/*
                add_image_size( 'inkston-aside', 800, 9999 );
                add_image_size( 'inkston-medium', 1200, 9999 );
                add_image_size( 'inkston-big', 1400, 9999 );
*/
	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'top' => __( 'Top Menu', 'photoline-inkston' ),
		'primary' => __( 'Primary Menu', 'photoline-inkston' ),
		'social' => __( 'Social Menu', 'photoline-inkston' ),
	) );


	/**
	 * Setup the WordPress core custom header image.
	 */
	add_theme_support( 'custom-header', apply_filters( 'inkston_custom_header_args', array(
                                'header-text'            => true,
		'default-text-color'     => '1a1919',
		'width'                  => 1020,
		'height'                 => 450,
		'flex-height'            => true,
    'flex-width'             => true,
		'wp-head-callback'       => 'inkston_header_style',
		'admin-head-callback'    => 'inkston_admin_header_style',
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
		'name'          => __( 'Sidebar Posts', 'photoline-inkston' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<p class="widget-title">',
		'after_title'   => '</p>',
	) );
	register_sidebar( array(
		'name'          => __( 'Sidebar Pages', 'photoline-inkston' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<p class="widget-title">',
		'after_title'   => '</p>',
	) );
       register_sidebar(array(
            'name' => __('Footer1', 'photoline-inkston'),
            'description' => __('Located in the footer left.', 'photoline-inkston'),
            'id' => 'footer1',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
            'before_widget' => '<div class="widget">',
            'after_widget' => '</div>'
        ));
       register_sidebar(array(
            'name' => __('Footer2', 'photoline-inkston'),
            'description' => __('Located in the footer center.', 'photoline-inkston'),
            'id' => 'footer2',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
            'before_widget' => '<div class="widget">',
            'after_widget' => '</div>'
        ));
       register_sidebar(array(
            'name' => __('Footer3', 'photoline-inkston'),
            'description' => __('Located in the footer right.', 'photoline-inkston'),
            'id' => 'footer3',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
            'before_widget' => '<div class="widget">',
            'after_widget' => '</div>'
        ));
}
add_action( 'widgets_init', 'inkston_widgets_init' );

/**
 * Register Google fonts for Theme
 * Better way
 */
if ( ! function_exists( 'inkston_fonts_url' ) ) :

function inkston_fonts_url() {
    $fonts_url = '';
 
    $open_sans = _x( 'on', 'Open Sans font: on or off', 'photoline-inkston' );
 
    if ( 'off' !== $open_sans ) {
        $font_families = array();
 
        if ( 'off' !== $open_sans ) {
            $font_families[] = 'Open Sans:300italic,400italic,700italic,400,600,700,300';
        }
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,cyrillic' ),
        );
 
/*
  *     TODO: this should work as a replacement for fonts.googleapis.com, but is only available inside china
  *      $fonts_url = add_query_arg( $query_args, '//fonts.useso.com/css' );
 */
        $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
    }
    return $fonts_url;
}
endif;

/**
 *=Enqueue scripts
 */
function inkston_scripts() {
/*
    wp_enqueue_style( 'inkston-style', get_stylesheet_uri() );
	wp_enqueue_style( 'inkston-fonts', inkston_fonts_url(), array(), null );
    wp_enqueue_script( 'jquery-fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', array( 'jquery' ), '1.1', true );
*/
  //wp_enqueue_style( 'inkston-style', get_template_directory_uri() . '/inkston.css', array(), filemtime( get_stylesheet_directory() . '/inkston.css' ) );
  wp_enqueue_style( 'inkston-style', get_template_directory_uri() . '/inkston.min.css', array(), filemtime( get_stylesheet_directory() . '/inkston.min.css' ) );
    //font-genericons currently enables cart symbol
    wp_enqueue_style( 'font-genericons', get_template_directory_uri() . '/genericons/genericons.css?v=3.4' );
    //includes the navigation arrows
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/font-awesome/css/font-awesome.min.css?v=4.4' );
/* test removing excessive stylesheets
    wp_enqueue_script( 'jquery-flexslider', get_template_directory_uri() . '/js/jquery.flexslider.min.js', array( 'jquery' ), '25062015', true );
    wp_enqueue_script( 'jquery-slimmenu', get_template_directory_uri() . '/js/jquery.slimmenu.min.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'style-slimmenu', get_template_directory_uri() . '/css/slimmenu.css?v=1.0' );
    wp_enqueue_style( 'style-flexslider', get_template_directory_uri() . '/css/flexslider.css?v=25062015' );
*/

    /*
     * TODO:TO BE REPLACED instead of disabling these check other way of ensuring they are only loaded once
    wp_enqueue_style('style-prettyPhoto', get_template_directory_uri().'/css/prettyPhoto.css?v=25062015' );
    wp_enqueue_script( 'jquery-prettyPhoto', get_template_directory_uri() . '/js/jquery.prettyPhoto.min.js', array(), '1.0', true );
    */

    /* this re-uses the woocommerce scripts to do the jquery pretty-photo as these woocommerce scripts are loaded on most pages anyway...
    $suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
    wp_enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.min.js', array( 'jquery' ), '3.1.6', true );
    wp_enqueue_script( 'prettyPhoto-init', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init.min.js', array( 'jquery','prettyPhoto' ) );
    wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );
    */
    /*
    */
    wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '25062015', true );

	wp_enqueue_script( 'inkston-main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), '1.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '25062015' );
	}
?><script type="text/javascript">window.loginurl='<?php echo(wp_login_url()) ?>';</script><?php

}
add_action( 'wp_enqueue_scripts', 'inkston_scripts' );

/*TODO: test and remove stripe from pages where not needed*/
function i_suppress_scripts(){
  /*
    if ( wp_script_is('stripe', 'enqueued') ) {
        error_log('Stripe is enqueued at this point');
    }else {
        error_log('Stripe is NOT enqueued at this point');
    }
    if ( !is_checkout_pay_page() ) {
        wp_dequeue_script('stripe');
        wp_dequeue_script('woocommerce_stripe');
        wp_deregister_script('stripe');
        wp_deregister_script('woocommerce_stripe');
        error_log('attempted to dequeue stripe');
        if ( wp_script_is('stripe', 'enqueued') ) {
            error_log('Stripe is enqueued at this point');
        }else {
            error_log('Stripe is NOT enqueued at this point');
        }
    }
   * 
   */
    wp_dequeue_script( 'wpla_product_matcher' );

  
//  	wp_dequeue_script( 'photoswipe-masonry-js');
//    wp_deregister_script('photoswipe-masonry-js');
//    
//        if ( wp_script_is('photoswipe-masonry-js', 'enqueued') ) {
//            error_log('photoswipe-masonry-js is enqueued at this point');
//        }else {
//            error_log('photoswipe-masonry-js is NOT enqueued at this point');
//        }
//
//    wp_enqueue_script( 'photoswipe-masonry-js', get_template_directory_uri() . '/js/photoswipe-masonry.min.js');
//    wp_enqueue_script( 'photoswipe-masonry-js', get_template_directory_uri() . '/js/photoswipe-masonry.js');

}
add_action( 'wp_print_scripts', 'i_suppress_scripts' );


/**TODO: remove
 * Add lightbox prettyPhoto for link to image
function inkston_prettyPhoto( $html, $id, $size, $permalink, $icon, $text ) {
    if ( ! $permalink )
        return str_replace( '<a', '<a data-rel="prettyPhoto" ', $html );
    else
        return $html;
}
function inkston_addrel_replace ($content) {
global $post;
	$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
	$replacement = '<a$1href=$2$3.$4$5 rel="lightbox['.$post->ID.']"$6>$7</a>';
	$content = preg_replace($pattern, $replacement, $content);
return $content;
}
 */
/* JM: test force on lightbox - not sure it can be turned off
if ( false === get_theme_mod( 'inkston_lightbox_img' ) ) {
	add_filter( 'wp_get_attachment_link', 'inkston_prettyPhoto', 10, 6 );
	add_filter('the_content', 'inkston_addrel_replace', 12);
}
*/

/**
 * Extracting the first's image of post
 */
if ( ! function_exists( 'inkston_catch_image' ) ) :
	function inkston_catch_image() {
  		global $post, $posts;
  		ob_start();
  		ob_end_clean();
        $first_img='';
        if ( is_single() ) {
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            if (0 != $output) {
                /*
                 * NOTE: this gets the image sized as on the page, size not guaranteed,
                 * may also get an external image so no guarantee thumbnail is available  */
                $first_img = $matches [1][0];
            }
        }

        if ( empty($first_img) ) {
    		$first_img = get_template_directory_uri() .'/img/no-image.png';
        }

   		return $first_img;
		}
endif;

/**
 * Add post class  TODO: remove?  any need to add col class?
function inkston_post_class_filter( $classes ) {

    if ( ! is_page() && ! is_single()  && ! is_search() )

        $classes[] = sanitize_html_class( 'col' );

    return $classes;
}
add_filter( 'post_class', 'inkston_post_class_filter' );
*/

/**
 * Add body class
*/
function inkston_body_class_filter( $classes ) {

    /* psgal class enables photoswipe gallery */
    //if ((is_single()) || is_product_category()  || is_category() )
      $classes[] = sanitize_html_class( 'psgal' );

    if ( is_page_template( 'template-fullpage.php' ) )
        $classes[] = sanitize_html_class( 'fullpage' );

    if ( is_page_template( 'template-posttiles.php' ) )
        $classes[] = sanitize_html_class( 'fullpage' );

    if ( ! is_page() && ! is_single() && ! is_search() )
        $classes[] = sanitize_html_class( 'colgrid' );
    
    return $classes;
}
add_filter( 'body_class', 'inkston_body_class_filter' );

/**
 * Shorten excerpt length
 */
function inkston_excerpt_length($length) {
	if ( is_sticky() && is_front_page() && !is_home() ) {
		$length = 90;
	} elseif ( is_sticky() && is_home() || is_sticky() && !is_home() && !is_front_page() ) {
		$length = 28;
	} elseif ( is_home() ) {
		$length = 35;
	} elseif ( is_page() ) {
		$length = 15;
	} else {
		$length = 30;
	}
	return $length;
}
add_filter('excerpt_length', 'inkston_excerpt_length', 999);

/**
 * Replace [...] in excerpts with something new
function inkston_excerpt_more($more) {
	return '&hellip;';
}
add_filter('excerpt_more', 'inkston_excerpt_more');*/

/**
 * Custom excerpt
 */
require_once( get_template_directory() .'/inc/excerpts.php' );

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
 * Remove website URL field in the comment form - no, we want to keep it..
add_filter('comment_form_default_fields', 'inkston_remove_url');
 */
 
function inkston_remove_url($arg) {
    $arg['url'] = '';
    return $arg;
}

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Contextual Help Function File
 */
require( get_template_directory() . '/inc/contextual-help.php' );

/**
 * Wellcom Screen
 */
require_once( get_template_directory() . '/inc/welcome.php' );

/**
 * Theme hooks
 */
// see template-tags.php
//add_action( 'before_content', 'inkston_before_content' );
//add_action( 'before_loop_posts', 'inkston_before_loop_posts' );
//add_action( 'after_main_posts', 'inkston_after_main_posts' );
add_action( 'display_submenu_sidebar', 'inkston_get_submenu' );
add_action( 'inkston_credits', 'inkston_txt_credits' );


/**
 * HOOKs
 * see page.php, single.php and sidebar.php
 */

add_action( 'inkston_after_main_content', 'page_hook_example' );
function page_hook_example() {
	echo '<!-- HOOK-Page -->'; 
}

add_action( 'inkston_after_post_content', 'post_hook_example' );
function post_hook_example() {
	echo '<!-- HOOK-Post -->'; 
}

add_action( 'before_sidebar', 'sidebar_hook_example' );
function sidebar_hook_example() {
	echo '<!-- HOOK-Sidebar -->'; 
}

/**
 * Add metabox Excerpt for Page.
 */
function inkston_add_excerpt_to_pages() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action('init', 'inkston_add_excerpt_to_pages');

/**
 * Load Jetpack compatibility file.
require get_template_directory() . '/inc/jetpack.php';
 */

/**
 * =Ready WooCommerce Plugin
 */
/**/

if ( is_woocommerce_activated() ) {

	function inkston_woocommerce_css() {
		wp_enqueue_style( 'woocommerce-custom-style', get_template_directory_uri() . '/css/woocommerce.css' );
	}
add_action( 'wp_enqueue_scripts', 'inkston_woocommerce_css' );
function inkston_woocommerce_widgets_init() {
		register_sidebar(array(
		'name' => __('Store Sidebar', 'photoline-inkston'),
		'description' => __('Located in the sidebar woocommerce page.', 'photoline-inkston'),
		'id' => 'store',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>'
		));
	}
add_action( 'widgets_init', 'inkston_woocommerce_widgets_init' );
}


/**
 * Include products in main blog loop - this works but now largely replaced by Ajax load more
 *
 * @author Jonathan Moore based on advice from Bill Erickson
 * @link http://www.chipster.co.uk/
 * @param object $query data
 *
 */
/*
add_action( 'pre_get_posts', 'include_products' );
function include_products( $query )
{
    if ($query->is_main_query() && $query->is_home()) {
        $query->set('post_type', array('post', 'product'));
        $query->set( 'posts_per_page', 12 );
        $query->set( 'orderby', 'modified');
        $query->set( 'order', 'DESC');
    }
}
*/

/**
 * Add titles to images
 *
 * @author Bill Erickson
 * @link https://github.com/billerickson/display-posts-shortcode/issues/109
 *
 * @param array $attr, image attributes
 * @param object $attachment, post object for the image
 * @param string $size, image size
 * @return array $attr
 */
function be_add_title_to_images( $attr, $attachment, $size ) {
    $title='';
    if( ! isset( $attr['title'] ) ) {
        $title = get_the_title( $attachment->post_parent );
        /*
        if ($title){
            $title='DEBUG: POST PARENT TITLE ' . $title;
        }
        else{
            $title=$title . '  NOT FOUND';
        }
        */
      $attr['title'] = $title;
    }
    /*
    else{
        $title=$title . '  ALREADY SET ' . $attr['title'];
    }
    */
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'be_add_title_to_images', 10, 3 );
/*TODO:  use wp_get_attachment_image to get full img element with title and other attributes  */




/*another example
$post_id = $post['ID'];
$meta = wp_get_attachment_metadata( $post_id );
$meta['image_meta']['Homepage'] = $attachment['homepage'];
wp_update_attachment_metadata( $post_id,  $meta );
*/

/*
add_action('woocommerce_archive_description', 'woocommerce_category_image', 20);
function woocommerce_category_image()
{
    global $product;
    if (is_product_category()) {
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        $thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
        $image = wp_get_attachment_url($thumbnail_id);
        if ($image) {
            echo '<img src="' . esc_url($image) . '" alt="" />';
        }
    }
}
*/
if ( is_woocommerce_activated() ) {

/*To ajaxify your cart viewer so it updates when an item is added (via ajax) use:*/
// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );
function woocommerce_header_add_to_cart_fragment( $fragments ) {
    if ( ! is_cart() ) {
        ob_start();
	inkston_cart_link();
    $fragments['ul.cart-contents'] = ob_get_clean();
    }
    return $fragments;
}
}
// Make sure Polylang copies the title when creating a translation
function hy_editor_title( $title ) {
    // Polylang sets the 'from_post' parameter
    if ( isset( $_GET['from_post'] ) ) {
        $my_post = get_post( $_GET['from_post'] );
        if ( $my_post )
            return $my_post->post_title;
    }

    return $title;
}
add_filter( 'default_title', 'hy_editor_title' );

// Make sure Polylang copies the content when creating a translation
function hy_editor_content( $content ) {
    // Polylang sets the 'from_post' parameter
    if ( isset( $_GET['from_post'] ) ) {
        $my_post = get_post( $_GET['from_post'] );
        if ( $my_post )
            return $my_post->post_content;
    }

    return $content;
}
add_filter( 'default_content', 'hy_editor_content' );

// Make sure Polylang copies the excerpt [woocommerce short description] when creating a translation
function hy_editor_excerpt( $excerpt ) {
    // Polylang sets the 'from_post' parameter
    if ( isset( $_GET['from_post'] ) ) {
        $my_post = get_post( $_GET['from_post'] );
        if ( $my_post )
            return $my_post->post_excerpt;
    }

    return $excerpt;
}
add_filter( 'default_excerpt', 'hy_editor_excerpt' );

if ( is_woocommerce_activated() ) {

// Ship to a different address closed by default
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
}

/*stop Polylang filtering comments*/
function polylang_remove_comments_filter() {
    global $polylang;
    remove_filter('comments_clauses', array(&$polylang->filters, 'comments_clauses'));
}
add_action('wp','polylang_remove_comments_filter');


/* stop filtering tag/category to allow html description in tags */
foreach ( array( 'pre_term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_filter_kses' );
}
foreach ( array( 'term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_kses_data' );
}

/*turn off wordpress html filter so special home page code isn't munged*/
/*
    remove_filter( 'the_content', 'wpautop' );
if (is_home() || is_front_page()){
    remove_filter( 'the_content', 'wpautop' );
  }
 * 
 */
/* more complex filtering if needed here for a shortcode
function the_content_filter($content) {
    $block = join("|",array("one_third", "team_member"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
return $rep;
}
add_filter("the_content", "the_content_filter");
 * 
 */
if ( is_woocommerce_activated() ) {

// define the woocommerce_after_single_product callback 
function inkston_woocommerce_after_single_product(  ) { 
  $other_language=(pll_current_language()=='en') ? "es" : "en";
  $term_names='';  
  $term_slugs='';  
  $other_language_term='';
  $other_language_term_slugs='';
  global $post;
  $terms = get_the_terms( $post->ID, 'product_cat' );
  foreach ($terms as $term) {
      if (strlen($term_names)>0){$term_names.=', ';$term_slugs.=',';}
      $term_names .= $term->name;
      $term_slugs .= $term->slug;
      $termid=$term->term_id;
      $other_language_termid=pll_get_term($termid, $other_language);
      //echo('<h1>other language term id : ' . $other_language_termid . '</h1>');
      if ($other_language_termid){
        $other_language_term=get_term_by('id', $other_language_termid, 'product_cat');
        //echo('<h1>other language term : ' . $other_language_term->name . '</h1>');
        if ($other_language_term){
          if (strlen($other_language_term_slugs)>0){$other_language_term_slugs.=',';}
          $other_language_term_slugs.=$other_language_term->slug;
        }
      }
  }
  
  ?><h2 class="woocommerce-Reviews-title category-reviews"><?php echo(__( 'Recent discussions in: ', 'photoline-inkston' ) . $term_names ); ?></h2><?php
    // make action magic happen here... 
  		echo do_shortcode('[decent_comments number="25" taxonomy="product_cat" terms="' . $term_slugs . '" ]');
  		echo do_shortcode('[decent_comments number="25" taxonomy="product_cat" terms="' . $other_language_term_slugs . '" ]');
      comments_template();

}; 
         
// add the action 
//add_action( 'woocommerce_after_single_product', 'inkston_woocommerce_after_single_product', 10, 0 ); 
}

/* remove wooCommerce reviews tab since reviews are now shown at end of page*/
add_filter( 'woocommerce_product_tabs', 'inkston_remove_reviews_tab', 98 );
function inkston_remove_reviews_tab($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}


add_filter('post_limits', 'postsperpage');
function postsperpage($limits) {
	if (is_search()) {
		global $wp_query;
		$wp_query->query_vars['posts_per_page'] = 15;
	}
	return $limits;
}


function sort_merged_comments($a, $b) { 
	return $a->comment_ID - $b->comment_ID;
}

function merge_comments($comments, $post_ID) {
  $merge_commments=true;
  
  /*temporarily remove comment merging on wooCommerce product reviews, since this isn't compatible with 
   *how wooCommerce ratings and totals are calculated as yet*/
  if (is_woocommerce_activated()) {
    if (is_woocommerce() && is_product()) {
      $merge_commments=false;
    }
  }
  if ($merge_commments){
    
	// get all the languages for which this post exists
	$languages = icl_get_languages('skip_missing=1');
  
	$post = get_post( $post_ID );
	$type = $post->post_type;
	foreach($languages as $code => $l) {
		// in $comments are already the comments from the current language
		if(!$l['active']) {
			$otherID = icl_object_id($post_ID, $type, false, $l['language_code']);
			$othercomments = get_comments( array('post_id' => $otherID, 'status' => 'approve', 'type' => 'comment', 'order' => 'ASC') );
			$comments = array_merge($comments, $othercomments);
		}
	}
	if ($languages) {
		// if we merged some comments in we need to reestablish an order
		usort($comments, 'sort_merged_comments');
	}
  }  
	return $comments;
}

//note: this isn't called at all for wooCommerce products..
function merge_comment_count($count, $post_ID) {
  /*temporarily remove comment merging on wooCommerce product reviews, since this isn't compatible with 
   *how wooCommerce ratings and totals are calculated as yet*/
  if (is_woocommerce_activated()) {
    if (is_woocommerce() && is_product()) {
      return $count;
    }
  }
  
	// get all the languages for which this post exists
	$languages = icl_get_languages('skip_missing=1');
	$post = get_post( $post_ID );
	$type = $post->post_type;

	foreach($languages as $l) {
			// in $count is already the count from the current language
		if(!$l['active']) {
      
			$otherID = icl_object_id($post_ID, $type, false, $l['language_code']);
      
      $othercomments = get_comments( array('post_id' => $otherID, 'status' => 'approve', 'type' => 'comment') );
      $count = $count + count($othercomments);
		}
	}
	return $count;
}

add_filter('comments_array', 'merge_comments', 100, 2);
add_filter('get_comments_number', 'merge_comment_count', 100, 2);

/*uhhh this works but the link returned is then escaped and not treated as html - doh!
function add_link_to_woo_reviews($author, $comment_id, $comment){
    $url     = get_comment_author_url( $comment );
    if ( empty( $url ) || 'http://' == $url )
      return $author;
    else
      return '<a href="' . $url . '" target="inkstonlink" class="url">' . $author . '</a>';
  
}
add_filter('get_comment_author', 'add_link_to_woo_reviews', 100, 3);
*/

/*
  //code supplied for Ajax LoadMore extension didn't work and too low priority to fix
   functions.php
   Use alm_query_args filter to pass data to relevanssi_do_query() then back to ALM.
   https://connekthq.com/plugins/ajax-load-more/docs/filter-hooks/#alm_query_args
 * https://connekthq.com/plugins/ajax-load-more/extensions/relevanssi/
*/
//function my_alm_query_args_relevanssi($args){
//   $args = apply_filters('alm_relevanssi', $args);
//   return $args;
//}
//add_filter( 'alm_query_args_relevanssi', 'my_alm_query_args_relevanssi');

function inkston_product_meta(){
  if ( is_woocommerce_activated() ) {
    if ( is_product()){
      global $product; 
      echo ('<meta property="og:type" content="product" />' .  "\r\n");
      echo ('<meta property="og:brand" content="Inkston" />' .  "\r\n");
      echo ('<meta property="product:price:amount" content="' . esc_attr( $product->get_price() ) . '"/>' .  "\r\n");
      echo ('<meta property="product:price:currency" content="USD" />' .  "\r\n");
      if ( $product->is_in_stock() ) {
        echo ('<meta property="product:availability" content="instock" />' .  "\r\n");
      }      
    }
  }
}
//add_action( 'wp_head', 'inkston_product_meta' );
add_action( 'wpseo_opengraph', 'inkston_product_meta' , 40 );

function inkston_type_product($type){
  if ( is_woocommerce_activated() ) {
    if ( is_product()){
      return "product";
    }
		else{
			return "article";
		}
  }
  return $type;
}
add_filter( 'wpseo_opengraph_type', 'inkston_type_product' );

function my_login_logo() { ?>
<style type="text/css">#login h1 a, .login h1 a {
background-image: url(https://www.inkston.com/wp-content/uploads/2016/07/inkston-logo-tr.png);
height:122px;
width:322px;
background-size: 322px 122px;
background-repeat: no-repeat;
}
#wp-submit{background-color:#39aa39;}
#wp-submit:hover, #wp-submit:active{background-color:#4d914d;}
@media (min-width: 450px){
#site-main {
width: 400px;
margin: 0 auto;
background-color: white;
box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
vertical-align: middle;
padding: 15px;
margin-top: 100px;
}				
}
</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return 'https://www.inkston.com/';
    //return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Inkston.com';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function my_login_header() {
	echo ('<div id="site-main">');
}
add_filter( 'login_header', 'my_login_header' );
function my_login_footer() {
    echo ( '</div>');
}
add_filter( 'login_footer', 'my_login_footer' );