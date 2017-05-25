<?php
/**
 * Theme functions and definitions
 *
 * @package Inkston
 */
/**
 * Set the content width for theme design
 */
if (!isset($content_width)) {
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
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 6);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 7);
add_action('woocommerce_after_single_product_summary', 'inkston_woocommerce_after_single_product', 90);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);


/* customize cart buttons on archive screens _template_loop_add_to_cart */
function custom_woocommerce_product_add_to_cart_text()
{
    global $product;
    $product_type = $product->get_type();
    switch ($product_type) {
        case 'external':
            return __('Buy', 'photoline-inkston');
            break;
        case 'grouped':
            return __('View', 'photoline-inkston');
            break;
        case 'simple':
            return __('Add', 'photoline-inkston');
            break;
        case 'variable':
            return __('Choose', 'photoline-inkston');
            break;
        default:
            return __('Read', 'photoline-inkston');
    }
}
add_filter('woocommerce_product_add_to_cart_text', 'custom_woocommerce_product_add_to_cart_text');/** * custom_woocommerce


  /*disable contact form 7 scripts */
add_filter('wpcf7_load_css', '__return_false');
add_filter('wpcf7_load_js', '__return_false');

if (!function_exists('inkston_setup')) :

    function inkston_setup()
    {
        /** Markup for search form, comment form, and comments valid HTML5. */
        add_theme_support('html5', array('comment-form', 'comment-list', 'gallery', 'caption'));

        /* Make theme available for translation */
        load_theme_textdomain('photoline-inkston', get_template_directory() . '/languages');

        //wooCommerce3 gallery features
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        /* Add default posts and comments RSS feed links to head */
        add_theme_support('automatic-feed-links');

        /* Enable support for Excerpt on Pages. See http://codex.wordpress.org/Excerpt */
        add_post_type_support('page', 'excerpt');

        /* Enable support WooCommerce */
        add_theme_support('woocommerce');

        /*
         * Let WordPress 4.1+ manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /**
         * Enable support for Post Thumbnails on posts and pages
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(300, 300, true);

        /**
         * This theme uses wp_nav_menu() in one location.
         */
        register_nav_menus(array(
            'top' => __('Top Menu', 'photoline-inkston'),
            'primary' => __('Primary Menu', 'photoline-inkston'),
            'social' => __('Social Menu', 'photoline-inkston'),
        ));


        /**
         * Setup the WordPress core custom header image.
         */
        add_theme_support('custom-header', apply_filters('inkston_custom_header_args', array(
            'header-text' => true,
            'default-text-color' => '1a1919',
            'width' => 1020,
            'height' => 450,
            'flex-height' => true,
            'flex-width' => true,
            'wp-head-callback' => 'inkston_header_style',
            'admin-head-callback' => 'inkston_admin_header_style',
            'admin-preview-callback' => 'inkston_admin_header_image',
        )));

        /**
         * Setup the WordPress core custom background feature.
         */
        add_theme_support('custom-background', apply_filters('inkston_custom_background_args', array(
            'default-color' => 'ffffff',
        )));
    }
endif; // inkston_setup
add_action('after_setup_theme', 'inkston_setup');

/**
 * Register widgetized area and update sidebar with default widgets
 */
function inkston_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar Posts', 'photoline-inkston'),
        'id' => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ));
    register_sidebar(array(
        'name' => __('Sidebar Pages', 'photoline-inkston'),
        'id' => 'sidebar-2',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ));
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
add_action('widgets_init', 'inkston_widgets_init');

/**
 * =Enqueue scripts
 */
function inkston_scripts()
{
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $template_uri = get_template_directory_uri();
    $scriptname = '/inkston' . $suffix . '.css';
    wp_enqueue_style('inkston-style', $template_uri . $scriptname, array(), filemtime(get_stylesheet_directory() . $scriptname));
    //font-genericons currently enables cart symbol
    wp_enqueue_style('font-genericons', $template_uri . '/genericons/genericons.css?v=3.4');
    //includes the navigation arrows
    wp_enqueue_style('font-awesome', $template_uri . '/font-awesome/css/font-awesome.min.css?v=4.4');

    wp_enqueue_script('skip-link-focus-fix', $template_uri . '/js/skip-link-focus-fix' . $suffix . '.js', array(), '25062015', true);

    $scriptname = '/js/main' . $suffix . '.js';
    wp_enqueue_script('inkston-main', $template_uri . $scriptname, array('jquery'), filemtime(get_stylesheet_directory() . $scriptname), true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    if (is_singular() && wp_attachment_is_image()) {
        wp_enqueue_script('keyboard-image-navigation', $template_uri . '/js/keyboard-image-navigation.js', array('jquery'), '25062015');
    }

    ?><script type="text/javascript">window.loginurl = '<?php echo(wp_login_url()) ?>';</script><?php
}
add_action('wp_enqueue_scripts', 'inkston_scripts');

/* TODO: test and remove stripe from pages where not needed */

function i_suppress_scripts()
{
    wp_dequeue_script('wpla_product_matcher');
}
add_action('wp_print_scripts', 'i_suppress_scripts');

/**
 * Extracting the first's image of post
 */
if (!function_exists('inkston_catch_image')) :

    function inkston_catch_image()
    {
        global $post, $posts;
        ob_start();
        ob_end_clean();
        $first_img = '';
        if (is_single()) {
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            if (0 != $output) {
                /*
                 * NOTE: this gets the image sized as on the page, size not guaranteed,
                 * may also get an external image so no guarantee thumbnail is available  */
                $first_img = $matches [1][0];
            }
        }

        if (empty($first_img)) {
            $first_img = get_template_directory_uri() . '/img/no-image.png';
        }

        return $first_img;
    }
endif;


/**
 * Add body class
 */
function inkston_body_class_filter($classes)
{

    /* psgal class enables photoswipe gallery */
    //if ((is_single()) || is_product_category()  || is_category() )
    $classes[] = sanitize_html_class('psgal');

    if (is_page_template('template-fullpage.php'))
        $classes[] = sanitize_html_class('fullpage');

    if (is_page_template('template-posttiles.php'))
        $classes[] = sanitize_html_class('fullpage');

    if (!is_page() && !is_single() && !is_search())
        $classes[] = sanitize_html_class('colgrid');

    return $classes;
}
add_filter('body_class', 'inkston_body_class_filter');

/**
 * Shorten excerpt length
 */
function inkston_excerpt_length($length)
{
    if (is_sticky() && is_front_page() && !is_home()) {
        $length = 90;
    } elseif (is_sticky() && is_home() || is_sticky() && !is_home() && !is_front_page()) {
        $length = 28;
    } elseif (is_home()) {
        $length = 35;
    } elseif (is_page()) {
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
  add_filter('excerpt_more', 'inkston_excerpt_more'); */
/**
 * Custom excerpt
 */
require_once( get_template_directory() . '/inc/excerpts.php' );

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
function inkston_remove_url($arg)
{
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
add_action('display_submenu_sidebar', 'inkston_get_submenu');
add_action('inkston_credits', 'inkston_txt_credits');


/**
 * HOOKs
 * see page.php, single.php and sidebar.php
 */
add_action('inkston_after_main_content', 'page_hook_example');

function page_hook_example()
{
    echo '<!-- HOOK-Page -->';
}
add_action('inkston_after_post_content', 'post_hook_example');

function post_hook_example()
{
    echo '<!-- HOOK-Post -->';
}
add_action('before_sidebar', 'sidebar_hook_example');

function sidebar_hook_example()
{
    echo '<!-- HOOK-Sidebar -->';
}

/**
 * Add metabox Excerpt for Page.
 */
function inkston_add_excerpt_to_pages()
{
    add_post_type_support('page', 'excerpt');
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

if (is_woocommerce_activated()) {

    function inkston_woocommerce_css()
    {
        wp_enqueue_style('woocommerce-custom-style', get_template_directory_uri() . '/css/woocommerce.css');
    }
    add_action('wp_enqueue_scripts', 'inkston_woocommerce_css');

    function inkston_woocommerce_widgets_init()
    {
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
    add_action('widgets_init', 'inkston_woocommerce_widgets_init');
}


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
function be_add_title_to_images($attr, $attachment, $size)
{
    $title = '';
    if (!isset($attr['title'])) {
        $title = get_the_title($attachment->post_parent);
        $attr['title'] = $title;
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'be_add_title_to_images', 10, 3);

if (is_woocommerce_activated()) {

    /* To ajaxify your cart viewer so it updates when an item is added (via ajax) use: */
// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
    add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

    function woocommerce_header_add_to_cart_fragment($fragments)
    {
        if (!is_cart()) {
            ob_start();
            inkston_cart_link();
            $fragments['ul.cart-contents'] = ob_get_clean();
        }
        return $fragments;
    }
}

// Make sure Polylang copies the title when creating a translation
function hy_editor_title($title)
{
    // Polylang sets the 'from_post' parameter
    if (isset($_GET['from_post'])) {
        $my_post = get_post($_GET['from_post']);
        if ($my_post)
            return $my_post->post_title;
    }

    return $title;
}
add_filter('default_title', 'hy_editor_title');

// Make sure Polylang copies the content when creating a translation
function hy_editor_content($content)
{
    // Polylang sets the 'from_post' parameter
    if (isset($_GET['from_post'])) {
        $my_post = get_post($_GET['from_post']);
        if ($my_post)
            return $my_post->post_content;
    }

    return $content;
}
add_filter('default_content', 'hy_editor_content');

// Make sure Polylang copies the excerpt [woocommerce short description] when creating a translation
function hy_editor_excerpt($excerpt)
{
    // Polylang sets the 'from_post' parameter
    if (isset($_GET['from_post'])) {
        $my_post = get_post($_GET['from_post']);
        if ($my_post)
            return $my_post->post_excerpt;
    }

    return $excerpt;
}
add_filter('default_excerpt', 'hy_editor_excerpt');

if (is_woocommerce_activated()) {

// Ship to a different address closed by default
    add_filter('woocommerce_ship_to_different_address_checked', '__return_false');
}

/* stop Polylang filtering comments */

function polylang_remove_comments_filter()
{
    global $polylang;
    remove_filter('comments_clauses', array(&$polylang->filters, 'comments_clauses'));
}
add_action('wp', 'polylang_remove_comments_filter');


/* stop filtering tag/category to allow html description in tags */
foreach (array('pre_term_description') as $filter) {
    remove_filter($filter, 'wp_filter_kses');
}
foreach (array('term_description') as $filter) {
    remove_filter($filter, 'wp_kses_data');
}

if (is_woocommerce_activated()) {

// define the woocommerce_after_single_product callback 
    function inkston_woocommerce_after_single_product()
    {
        $other_language = (pll_current_language() == 'en') ? "es" : "en";
        $term_names = '';
        $term_slugs = '';
        $other_language_term = '';
        $other_language_term_slugs = '';
        global $post;
        $terms = get_the_terms($post->ID, 'product_cat');
        if (!($terms)) {
            return;
        }   //if there are no terms then omit the comments from term...
        foreach ($terms as $term) {
            if (strlen($term_names) > 0) {
                $term_names .= ', ';
                $term_slugs .= ',';
            }
            $term_names .= $term->name;
            $term_slugs .= $term->slug;
            $termid = $term->term_id;
            $other_language_termid = pll_get_term($termid, $other_language);
            //echo('<h1>other language term id : ' . $other_language_termid . '</h1>');
            if ($other_language_termid) {
                $other_language_term = get_term_by('id', $other_language_termid, 'product_cat');
                //echo('<h1>other language term : ' . $other_language_term->name . '</h1>');
                if ($other_language_term) {
                    if (strlen($other_language_term_slugs) > 0) {
                        $other_language_term_slugs .= ',';
                    }
                    $other_language_term_slugs .= $other_language_term->slug;
                }
            }
        }

        ?><h2 class="woocommerce-Reviews-title category-reviews"><?php echo(__('Recent discussions in: ', 'photoline-inkston') . $term_names ); ?></h2><?php
        // make action magic happen here... 
        echo do_shortcode('[decent_comments number="25" taxonomy="product_cat" terms="' . $term_slugs . '" ]');
        echo do_shortcode('[decent_comments number="25" taxonomy="product_cat" terms="' . $other_language_term_slugs . '" ]');
        comments_template();
    }
;

// add the action 
//add_action( 'woocommerce_after_single_product', 'inkston_woocommerce_after_single_product', 10, 0 ); 
}

/* remove wooCommerce reviews tab since reviews are now shown at end of page */
add_filter('woocommerce_product_tabs', 'inkston_remove_reviews_tab', 98);

function inkston_remove_reviews_tab($tabs)
{
    unset($tabs['reviews']);
    return $tabs;
}
add_filter('post_limits', 'postsperpage');

function postsperpage($limits)
{
    if (is_search()) {
        global $wp_query;
        $wp_query->query_vars['posts_per_page'] = 15;
    }
    return $limits;
}

function sort_merged_comments($a, $b)
{
    return $a->comment_ID - $b->comment_ID;
}

function merge_comments($comments, $post_ID)
{
    $merge_commments = true;

    /* temporarily remove comment merging on wooCommerce product reviews, since this isn't compatible with 
     * how wooCommerce ratings and totals are calculated as yet */
    if (is_woocommerce_activated()) {
        if (is_woocommerce() && is_product()) {
            $merge_commments = false;
        }
    }
    if ($merge_commments) {


        $post = get_post($post_ID);
        $type = $post->post_type;
        if (function_exists('icl_get_languages')){
            // get all the languages for which this post exists
            $languages = icl_get_languages('skip_missing=1');
            foreach ($languages as $code => $l) {
                // in $comments are already the comments from the current language
                if (!$l['active']) {
                    $otherID = icl_object_id($post_ID, $type, false, $l['language_code']);
                    $othercomments = get_comments(array('post_id' => $otherID, 'status' => 'approve', 'type' => 'comment', 'order' => 'ASC'));
                    $comments = array_merge($comments, $othercomments);
                }
            }
            if ($languages) {
                // if we merged some comments in we need to reestablish an order
                usort($comments, 'sort_merged_comments');
            }
        }
    }
    return $comments;
}

//note: this isn't called at all for wooCommerce products..
function merge_comment_count($count, $post_ID)
{
    /* temporarily remove comment merging on wooCommerce product reviews, since this isn't compatible with 
     * how wooCommerce ratings and totals are calculated as yet */
    if (is_woocommerce_activated()) {
        if (is_woocommerce() && is_product()) {
            return $count;
        }
    }

    $post = get_post($post_ID);
    $type = $post->post_type;

    if (function_exists('icl_get_languages')){
        // get all the languages for which this post exists
        $languages = icl_get_languages('skip_missing=1');
        foreach ($languages as $l) {
            // in $count is already the count from the current language
            if (!$l['active']) {

                $otherID = icl_object_id($post_ID, $type, false, $l['language_code']);

                $othercomments = get_comments(array('post_id' => $otherID, 'status' => 'approve', 'type' => 'comment'));
                $count = $count + count($othercomments);
            }
        }
    }
    return $count;
}
add_filter('comments_array', 'merge_comments', 100, 2);
add_filter('get_comments_number', 'merge_comment_count', 100, 2);


function inkston_product_meta()
{
    if (is_woocommerce_activated()) {
        if (is_product()) {
            global $product;
            echo ('<meta property="og:type" content="product" />' . "\r\n");
            echo ('<meta property="og:brand" content="Inkston" />' . "\r\n");
            echo ('<meta property="product:price:amount" content="' . esc_attr($product->get_price()) . '"/>' . "\r\n");
            echo ('<meta property="product:price:currency" content="USD" />' . "\r\n");
            if ($product->is_in_stock()) {
                echo ('<meta property="product:availability" content="instock" />' . "\r\n");
            }
        }
    }
}
//add_action( 'wp_head', 'inkston_product_meta' );
add_action('wpseo_opengraph', 'inkston_product_meta', 40);

function inkston_type_product($type)
{
    if (is_woocommerce_activated()) {
        if (is_product()) {
            return "product";
        } else {
            return "article";
        }
    }
    return $type;
}
add_filter('wpseo_opengraph_type', 'inkston_type_product');

function my_login_logo()
{

    ?>
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
<?php
}
add_action('login_enqueue_scripts', 'my_login_logo');

function my_login_logo_url()
{
    return 'https://www.inkston.com/';
    //return home_url();
}
add_filter('login_headerurl', 'my_login_logo_url');

function my_login_logo_url_title()
{
    return 'Inkston.com';
}
add_filter('login_headertitle', 'my_login_logo_url_title');

function my_login_header()
{
    echo ('<div id="site-main">');
}
add_filter('login_header', 'my_login_header');

function my_login_footer()
{
    echo ( '</div>');
}
add_filter('login_footer', 'my_login_footer');

//this adds the login and register links underneath on a single topic so someone can leave a reply. It uses the same logic as form-reply
function ink_new_reply_login()
{
    if (!bbp_current_user_can_access_create_reply_form() && !bbp_is_topic_closed() && !bbp_is_forum_closed(bbp_get_topic_forum_id())) {

        ?>
        <div style="line-height:3em">
          <a href="<?php echo wp_login_url(get_permalink()); ?>" title="Login">Login</a> - 
          <a href="<?php echo wp_registration_url(); ?>" target="_blank" >Register</a>
        </div>
        <?php
    }
}
add_action('bbp_template_after_single_topic', 'ink_new_reply_login');

//this adds the llogin and register links underneath on a single forum so someone can start a topic. It uses the same logic as form-topic
function ink_new_topic_login()
{
    if (!bbp_current_user_can_access_create_topic_form() && !bbp_is_forum_closed()) {

        ?>
        <div style="line-height:3em">
          <a href="<?php echo wp_login_url(get_permalink()); ?>" title="Login">Login</a> &nbsp; 
          <a href="<?php echo wp_registration_url(); ?>" target="_blank">Register</a>
        </div>
        <?php
    }
}
add_action('bbp_template_after_single_forum', 'ink_new_topic_login');

//adjusts comment form login links to handle better return and auto-expand comment form
function ink_comment_form_defaults($defaults)
{
    $must_login = '<p class="must-log-in">' . sprintf(
            __('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url(apply_filters('the_permalink', get_permalink() . '#comment'))
        ) . ' ' .
        sprintf(__('If you do not have an account <a target="_blank" href="%s">please register</a> (will open in new window)', 'photoline-inkston'), wp_registration_url()) .
        sprintf(__(' and then <a href="#comment" onclick="%s" ">click here</a>.', 'photoline-inkston'), "javascript:window.location.hash = '#comment';window.location.reload(true);") .
        '</p>';

    $defaults['must_log_in'] = $must_login;
    return $defaults;
}
add_filter('comment_form_defaults', 'ink_comment_form_defaults');

/* Forums: visual editor only, allow full screen  */

function bbp_enable_visual_editor($args = array())
{
    $args['tinymce'] = true;
    $args['teeny'] = false;
    $args['quicktags'] = false;
    $args['fullscreen'] = true;
    return $args;
}
add_filter('bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor');

/* only allows simple formatting in pastes */

function bbp_tinymce_paste_plain_text($plugins = array())
{
    $plugins[] = 'paste';
    return $plugins;
}
add_filter('bbp_get_tiny_mce_plugins', 'bbp_tinymce_paste_plain_text');

/* enable fullscreen on forum tinymce editor if wanted */
/*
  function re_enable_mce_full_screen( $plugins = array() ){
  $plugins[] = 'fullscreen';
  return $plugins;
  }
  add_filter( 'bbp_get_tiny_mce_plugins', 're_enable_mce_full_screen' );
 */

/**
 * get appropriate URL for an autho
 * 
 * @param int        $userID The user ID.
 */
function getAuthorURL($userID)
{
    $author_url = '';
    if (function_exists('bbp_get_user_profile_url')) {
        $author_url = bbp_get_user_profile_url($userID);
    } else {
        $author_url = esc_url(get_author_posts_url($userID));
    }
    return $author_url;
}

/**
 * Filters the comment author's URL.
 *
 * @param string     $url        The comment author's URL.
 * @param int        $comment_ID The comment ID.
 * @param WP_Comment $comment    The comment object.
 */
function getCommentUserURL($url, $comment_ID, $comment)
{
    if ($url) {
        return $url;
    }
    if ($comment) {
        return getAuthorURL($comment->user_id);
    }
}
//return apply_filters( 'get_comment_author_url', $url, $id, $comment );
add_filter('get_comment_author_url', 'getCommentUserURL', 10, 3);

/**
 * gets the page id from id or slug, translated if polylang available
 *
 * @param string     $page        id or slug of page to find
 * 
* @return int page id or false if no page found
*/
function inkGetPageID($page)
{
    //get the page it, if $page is not already numeric
    if (!(is_numeric($page))){
        if (is_string($page)){
            $page = get_page($page);

            $args = array(
              'name'        => $page,
              'post_type'   => 'page',
              'post_status' => 'publish',
              'numberposts' => 1
            );
            $my_posts = get_posts($args);
            if ( $my_posts ){
                 $page = $my_posts[0]->ID;
            } else {
                return false;
            }
        } else {
            $page = get_post($page);
            if ($page){
                $page = $page->ID;
            }else{
                return false;
            }
        }
    }
    
    //if polylang enabled, get page in right language
    if (function_exists('pll_get_post')){
        $page = pll_get_post($page); // translate the About page in the current language        
    }
    return $page; // returns the link
}

