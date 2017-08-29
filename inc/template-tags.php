<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Inkston
 */

if ( ! function_exists( 'inkston_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function inkston_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'photoline-inkston' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '<i class="fa fa-arrow-left"></i>', 'Previous post link', 'photoline-inkston' ) . '</span>' ); // %title ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '<span class="meta-nav">' . _x( '<i class="fa fa-arrow-right"></i>', 'Next post link', 'photoline-inkston' ) . '</span>' ); // %title ?>

<?php elseif ( $wp_query->max_num_pages > 1 ) : // && ( is_home() || is_category() || is_archive() || is_search() )  ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-previous"><?php previous_posts_link( __( 'Back', 'photoline-inkston' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-next"><?php next_posts_link( __( 'More', 'photoline-inkston' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // inkston_content_nav


if ( ! function_exists( 'inkston_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function inkston_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'inkston_attachment_size', array( 1200, 9999 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" rel="attachment">%2$s</a>',
		esc_url( $next_attachment_url ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

if ( ! function_exists( 'inkston_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function inkston_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
		$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

    $author_url = getAuthorURL(get_the_author_meta( 'ID' ));
    /*
    if (  function_exists( 'bbp_get_user_profile_url' ) ){
        $author_url = bbp_get_user_profile_url( get_the_author_meta( 'ID' ) );
    } else {
        $author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
    }
    */

	printf( __( '<span class="posted-on">%1$s</span><span class="byline"> <i>by</i> %2$s</span>', 'photoline-inkston' ),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			$author_url,
			esc_html( get_the_author() )
		)
	);
}
endif;

if ( ! function_exists( 'inkston_posted_extra' ) ) :
/**
 * Prints HTML with meta info cat/tag
 */
function inkston_posted_extra() {

	$categories_list = get_the_category_list( __( ', ', 'photoline-inkston' ) );
	$tags_list = get_the_tag_list( '', __( ', ', 'photoline-inkston' ) );

	if ( $categories_list && inkston_categorized_blog() ) {

	echo '<span class="cat-links">' . __( 'Category: ', 'photoline-inkston' ), $categories_list . '.</span>';

	}
	if ( $tags_list ) {

	echo '<span class="tags-links">' . __( 'Tag: ', 'photoline-inkston' ), $tags_list . '</span>';

	}
}
endif;

/**
 * Returns true if a blog has more than 1 category
 */
function inkston_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so inkston_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so inkston_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in inkston_categorized_blog
 */
function inkston_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'inkston_category_transient_flusher' );
add_action( 'save_post',     'inkston_category_transient_flusher' );

/**
 * Featured image as the background for some formats posts
 */
function inkston_bgimage_postformats() {
$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
echo 'style="background:';
echo esc_attr( get_theme_mod( 'inkston_link_color', '#2d2d2d' ) );
	if  ( $thumbnail ) {
		echo ' url(';
		echo $thumbnail[0];
		echo ' ) no-repeat; background-position: 50%; background-size: cover';
	}
echo ';"';
}

/**
 * The page submenu into sidebar
 * @author Oleg Murashov
 */
if ( ! function_exists( 'inkston_get_submenu' ) ) {
  function inkston_get_submenu($args) {
    $defaults = array(
    	'container_id' => '',
    	'container' => 'div',
    	'container_class' => 'submenu',
    	'submenu_id' => 'sidemenu',
    	'submenu_class' => '',
    	'theme_location' => 'primary',
    	'xpath' => "./li[contains(@class,'current-menu-item') or contains(@class,'current-menu-ancestor')]/ul",
    	'echo' => true
    );

    $args = wp_parse_args( $args, $defaults );
    $args = (object) $args;
 
    $menu = wp_nav_menu(
        array(
            'theme_location' => $args->theme_location,
            'container' => '',
            'echo' => false
        )
    );

    $menu = simplexml_load_string($menu);
    $submenu = $menu->xpath($args->xpath);

    if (empty($submenu)) {
        return;
    }

    // Set value of class attribute
    $submenu[0]['class'] = $args->submenu_class;

    // Add "id" attribute
    if ($args->submenu_id) {
        $submenu[0]->addAttribute('id', $args->submenu_id);
    }

    if ($args->container) {
        $submenu_sxe = simplexml_load_string($submenu[0]->saveXML());
        $sdm = dom_import_simplexml($submenu_sxe);

        if ($sdm) {
            $xmlDoc = new DOMDocument('1.0', 'utf-8');
            $container = $xmlDoc->createElement($args->container);

            // Add "class" attribute for container
            if ($args->container_class) {
                $container->setAttribute('class', $args->container_class);
            }

            // Add "id" attribute for container
            if ($args->container_id) {
                $container->setAttribute('id', $args->container_id);
            }
    
            $smsx = $xmlDoc->importNode($sdm, true);
            $container->appendChild($smsx);
            $xmlDoc->appendChild($container);
        }
    }

    if (isset($xmlDoc)) {
        $output = $xmlDoc->saveXML();
    } else {
        $output = $submenu[0]->saveXML();
    }

    if (!$args->echo) {
        return $output;
    }

    echo $output;
  }
}

/**
 * Footer credits.
 */
function inkston_txt_credits() {
	//$text = sprintf( __( 'Powered by %s', 'photoline-inkston' ), '<a href="http://www.chipster.co.uk/themes/photoline-inkston/">Chipster</a>' );
	//$text .= '<span class="sep"> &middot; </span>';
	$text = sprintf( __( 'design %s', 'photoline-inkston' ), '<a href="https://jonmoblog.wordpress.com/">J.Moore</a>' );
	echo apply_filters( 'inkston_txt_credits', $text );
}


/**
 * Theme Hooks
 */
if ( ! function_exists( 'inkston_before_content' ) ) {
	function inkston_before_content() {
		if ( has_post_thumbnail() || has_excerpt() ) {
?>
<header class="entry-header">

	<?php if ( has_post_thumbnail() && !has_post_format() ) : ?>
		<?php the_post_thumbnail( 'large' ); ?>
	<?php endif; //has_post_thumbnail ?>

	<?php if ( has_excerpt() ) : ?>
		<?php the_excerpt(); ?>
	<?php endif; //has_excerpt() ?>

</header>
<!-- .entry-header -->
<?php
		}
	}
}

if ( ! function_exists( 'inkston_before_loop_posts' ) ) {
	function inkston_before_loop_posts() {

if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
	<div class="grid2 clearfix">
<?php
}
if ( ! is_active_sidebar( 'sidebar-1' ) ) { ?>
	<div id="primary" class="content-area" style="float: none; width: 100%;">
		<main id="main" class="site-main" role="main">
	<div class="clearfix">
<?php
}

	}
}

if ( ! function_exists( 'inkston_after_main_posts' ) ) {
	function inkston_after_main_posts() {

		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			// silence is gold
		} else {
			inkston_paging_nav();
		}

	}
}

/**
 * WooCommerce Cart Link
 */
if ( ! function_exists( 'inkston_cart_link' ) ) {
	function inkston_cart_link( $wrapper_class = 'header-cart' ) {
//		if ( ! is_woocommerce_activated() ) {return false;}
        
      
		//update: turn off wrapper class override since this is called by ajax 
        //with no parameters for all cart buttons:
		$wrapper_class = 'header-cart';
		/**
		 * is_cart - Returns true when viewing the cart page so hide the cart button.
		 **/
		$button_title=__( 'View Cart', 'photoline-inkston' );
		$button_text='';
		$button_class = 'menu-item';

		if ( is_woocommerce_activated() ) {
            $button_url=esc_url(wc_get_cart_url());
            
            if ( (is_cart()) && (sizeof(WC()->cart->cart_contents) > 0) ) {
                $button_url=esc_url(wc_get_checkout_url());
                $button_title=__( 'Checkout', 'photoline-inkston' );
                $button_text=$button_title;
            }  /* otherwise show, but only if there are items.. . */
            elseif (sizeof(WC()->cart->cart_contents) > 0) {
              $button_class = 'menu-item';
              $button_text='<span class="cart-total">' . WC()->cart->get_cart_contents_count() . '</span> ' . wp_kses_data( WC()->cart->get_cart_total() );
              //TWEAK:  if there are cart items, then don't cache the page, we don't want cached version of page to have cart...
              //note also/instead pre-loading could also be used to ensure non-cart page versions are cached
              if ( ! defined( 'DONOTCACHEPAGE' ) ) {
                define( "DONOTCACHEPAGE", true );
              }
            }
            else {
              //doesn't work, even just initializing by default
              //$button_text=__( 'Cart', 'photoline-inkston' );
              $button_text='<span class="cart-total"> </span> &nbsp; &nbsp; ';
              //$button_class = 'hidden';
              /* turn button into add-to-cart button if nothing there currently: doesn't work yet..
              if (is_product()){
                $button_title=__( 'Add to Cart', 'photoline-inkston' );
                $button_text=$button_title;
                global $product;
                $button_url='?add-to-cart=' . $product->id;
              }
               */
            }
            if ( is_cart() ){
              $wrapper_class = 'checkout ' . $wrapper_class;
            }else{
              $wrapper_class = 'cart-contents ' . $wrapper_class;
            }
        } else {
    		$button_url='https://www.inkston.com/cart/';
            if (! isset( $_COOKIE['woocommerce_cart_hash'] ) ) {
                $wrapper_class .=' hidden';
            } else {
              $button_text='<span class="cart-total"> </span>' . __( 'Cart', 'photoline-inkston' );
            }
        }
		echo ('<ul class="' . $wrapper_class . '">');
        echo ('<li class="' . $button_class . '"><a href="' . $button_url . '" title="'.
    		$button_title . '">' . $button_text . '</a>');
        echo('</li></ul>');
	}
}

/*
 * Improved number format according to standards for locale and currency
 * 
 * @param number    $value current cart value
 *
 * @return string  formatted number
 */
function format_number($value, $ccy){
    
    $retval=WC()->cart->get_cart_total();
    $formatter = new NumberFormatter(pll_current_language('locale'),  NumberFormatter::CURRENCY);
    if ($formatter){
        $retval=$formatter->formatCurrency($amount, $ccy);
    }
    return $retval;
}


/**
 * WooCommerce Layout hooks
 */
if ( is_woocommerce_activated() ) {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
  /*
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
   * 
   */
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

	add_action( 'woocommerce_after_shop_loop', 'inkston_paging_nav', 10 );
}

/**
 * WooCommerce Page Title
 */
if ( ! function_exists( 'inkston_remove_wc_page_title' ) ) {
function inkston_remove_wc_page_title() {
	remove_filter( 'woocommerce_page_title', 15 );
}
add_action( 'woocommerce_show_page_title', 'inkston_remove_wc_page_title');
}
