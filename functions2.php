<?php
if (is_woocommerce_activated()){
    /**
     * Filter the custom field woosb_ids for WooCommerce Product Bundle 
     * to get the translated products in the bundle
     *  '10330/1,10328/1,6382/1'
     *
     * @param array  $keys list of custom fields names
     * @param bool   $sync true if it is synchronization, false if it is a copy
     * @param int    $from id of the post from which we copy informations
     * @param int    $to   id of the post to which we paste informations
     * @param string $lang language slug
     */

    //$keys = array_unique( apply_filters( 'pll_copy_post_metas', $keys, $sync, $from, $to, $lang ) );


    /**
     * Polylang meta filter, return true to exclude meta item from synchronization.
     * (we translated it later in the pll_save_post action)
     *
     * @param string      $meta_key Meta key
     * @param string|null $meta_type
     * 
     * @return bool True if the key is protected, false otherwise.
     */
    function nosync_woosb_ids($protected, $meta_key, $meta_type)
    {
        if ($meta_key == 'woosb_ids'){
            return true;
        } else {
            return $protected;
        }
    }
    add_filter( 'is_protected_meta', 'nosync_woosb_ids', 10, 3);
    /**
     * translate the custom field woosb_ids for WooCommerce Product Bundle 
     * to get the translated products in the bundle saved in postmeta in the format 
     *  {id}/{quantity},{id}/{quantity}
     * eg:
     *  '10330/1,10328/1,6382/1'
     * [Polylang only supports sync or no sync so we exclude from sync and save here]
     *
     * Hooks pll_save_post Fires after the post language and translations are saved
     *
     * @param int    $post_id      Post id
     * @param object $post         Post object
     * @param array  $translations The list of translations post ids
     */
    function translate_woosb_ids($post_id, $post, $translations){

        //if creating a new translation, we need to reverse the logic and copy from the original
        //the original is not included in the translations array as not linked yet
        //and the new post has no woosb_ids to check
        if ( isset($_GET['new_lang']) && isset($_GET['from_post']) ){
            $post_id= $_GET['from_post'];
        }

        //get woosb_ids and exit if none
        $woosb_ids = get_post_meta( $post_id, 'woosb_ids', true );
        if (! ($woosb_ids) ){
            //adaptation to support Grouped product in the same way as smart bundle
            //$woosb_ids = get_post_meta( $post_id, '_children', true );
        }
        if (! ($woosb_ids) ){return false;}

        //parse $woosb_ids {id}/{quantity},{id}/{quantity} format
        $woosb_items = explode( ',',  $woosb_ids);
        if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {        
            $lang = pll_get_post_language($post_id);
            $translations[$lang]=$post_id;
            //loop through translations
            foreach ($translations as $translation){
                //ignore source item, which should already be in correct lang?
                //or process anyway just to check and add missing upsells?
                if (! $translation) { // || ($post_id == $translation) ){
                    continue;                
                }
                $targetlang = pll_get_post_language($translation);
                $translateditems = array();

                foreach ( $woosb_items as $woosb_item ) {
                    $woosb_item_arr = explode( '/', $woosb_item );
                    $woosb_product  = get_translated_variation( $woosb_item_arr[0], $targetlang);                
                    if ($woosb_product){
                        //item found, make sure it is an upsell on the translation
                        $translateditems[] = $woosb_product . '/' . $woosb_item_arr[1];
                        add_upsell($woosb_product, $translation);
                    } else {
                        //if item not found there was a problem in get_translated_variation()
                        //and item cannot be added
                        //$translateditems[] = $woosb_item_arr[0] . '/' . $woosb_item_arr[1];                    
                    }
                }
                if ($lang!=$targetlang){
                    update_metadata('post', $translation, 'woosb_ids', implode(',', $translateditems)) ;
                }
            }
        }    
    }
    add_action('pll_save_post', 'translate_woosb_ids', 99, 3);

    /*
     * Automatically add bundles as an upsell to the component items
     * 
     * @param int $addto        Product to add upsell to
     * @param int $upselltoadd  the Product to add as the upsell
     * 
     */
    function add_upsell($addto, $upselltoadd)
    {
        //get the parent product if it is a variation (upsells only valid on parent)
        $product = get_product_or_parent($addto);
        $upsells = $product->get_upsell_ids();
        if (!in_array($upselltoadd, $upsells)){
            $upsells[] = $upselltoadd;
            $upsells = array_unique($upsells);
            //set_upsell_ids doesn't save product.. 
            $product->set_upsell_ids($upsells);
            //we don't want to get in event loop saving whole product again to update meta
            update_post_meta($product->get_id(), '_upsell_ids', $upsells);
        }
    }

    /**
     * When getting Upsells, also include Group Children if it is a Grouped product
     *
     * @param array      $related_ids array of product ids
     * @param WC_Product $product current product
     *
     * @return array filtered result
     */
    function addChildrenToUpsells($relatedIds, $product)
    {
        if ($product->get_type()=='grouped'){
            $children = $product->get_children();
            if ($children){
                $relatedIds = array_merge($relatedIds, $children);
            }
        }
        return $relatedIds;
    }
    add_filter('woocommerce_product_get_upsell_ids', 'addChildrenToUpsells', 5, 2);

    function inkston_add_group_excerpt()
    {
        global $product;
        if ( ($product) && ($product->get_type()=='grouped') ){
            woocommerce_template_single_excerpt();
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
        }
    }
    add_action('woocommerce_before_add_to_cart_form', 'inkston_add_group_excerpt', 10);
    /*
     * get the product, if it is a variable product, get the parent
     * 
     * @param int $product_id   Product 
     *
     * @return WC_Product|null the Product 
     */
    function get_product_or_parent($product_id)
    {
        $product = wc_get_product($product_id);
        if ($product && 'variation' === $product->get_type()) {
            //ok, find translated variation
            $product = wc_get_product($product->get_parent_id());
        }
        return $product;
    }
    /*
     * get the translated product, including if it is a variation product, get the translated variation
     * if there is no translation, return the original product
     * 
     * @param int $product_id   Product 
     *
     * @return int    translated product or variation (or original if no translation)
     * 
     */
    function get_translated_variation($product_id, $lang)
    {
        //if input is already in correct language just return it
        $sourcelang = pll_get_post_language($product_id);
        if ($sourcelang == $lang){
            return $product_id;
        }
        //if a translated item is found, return it
        $translated_id = pll_get_post( $product_id, $lang);
        if ( ( $translated_id ) && ( $translated_id != $product_id ) ){
            return $translated_id;
        }
        //ok no linked Polylang translation so maybe it's a variation
        $product = wc_get_product($product_id);
        if ($product && 'variation' === $product->get_type()) {
            //it's a variation, so let's get the parent and translate that
            $parent_id = $product->get_parent_id();
            $translated_id = pll_get_post( $parent_id, $lang);
            //if no translation return the original product variation id
            if ((! $translated_id) || ($translated_id == $parent_id) ) {
                return $product_id;
            }
            //ok, it's a variation and the parent product is translated, so here's what to do:
            //find the master link for this variation using the Hyyan '_point_to_variation' key
            $variationmaster = get_post_meta($product_id, '_point_to_variation');
            if (! $variationmaster){
                return $product_id;
            }
            //and now the related variation for the translation
            $posts = get_posts(array(
                'meta_key' => '_point_to_variation',
                'meta_value' => $variationmaster,
                'post_type' => 'product_variation',
                'post_parent' => $translated_id,
            ));        

            if ( count($posts) ){
                return $posts[0]->ID;
            }
        }
    }

    /* fixes for 5 items per row, 25 items per page etc */
    add_filter( 'loop_shop_per_page', function ( $cols ) {
    return 15;
    }, 20 );
    // Number or products per row ex 4
    add_filter('loop_shop_columns', 'loop_columns');
    if (!function_exists('loop_columns')) {
    function loop_columns() {
        return 5; // 5 products per row
    }
    }
    /*
    add_filter ( 'woocommerce_product_thumbnails_columns', 'xx_thumb_cols' );
     function xx_thumb_cols() {
         return 5; // .last class applied to every 4th thumbnail
     }
    */ 
    /*
     * filter for adding on sale notices for bundles
     * 
     * @param bool  $on_sale     calculated by wooCommerce and overridden with high priority by currency switcher
     * @param WC_Product $product
     * 
     * @return bool     on sale or not
     */ 
    function bundle_is_on_sale($on_sale, $product) 
    {
        if ($product && 'woosb' === $product->get_type()) {
            $woosb_pct = intval(get_post_meta( $product->get_id(), 'woosb_price_percent', true ));
            if ( ($woosb_pct) && ($woosb_pct<100) ){
                return true;
            }
        }
        return $on_sale;
    }
    //
    add_filter( 'woocommerce_product_is_on_sale', 'bundle_is_on_sale', 10000, 2);


    /*
     * filter for adding on sale flash notices 
     * 
     * @param string  $output     WooCommerce output
     * @param Post       $post
     * @param WC_Product $product
     * 
     * @return bool     on sale or not
     */ 
    function custom_product_sale_flash( $output, $post, $product ) {

        if (! $product){
            return $output;
        }

        $woosb_pct=100;
        if ($product && 'woosb' === $product->get_type()) {
            $woosb_pct = intval(get_post_meta( $product->get_id(), 'woosb_price_percent', true ));        
            if ( ($woosb_pct) && ($woosb_pct<100) ){
                //last check for fixed price rather than percent
                $woosb_fixed = intval(get_post_meta( $product->get_id(), '_price', true )); 
                if ( ($woosb_fixed) && ($woosb_fixed==$woosb_pct) ) {return $output;}
                return '<span class="onsale">-' .  round( 100 - $woosb_pct ) . '% ' . '</span>';
            }
        }

        return $output;
    }
    add_filter( 'woocommerce_sale_flash', 'custom_product_sale_flash', 11, 3 );
    /*
    function woocommerce_saved_sales_price( $price, $product ) {
        $percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
        return $price . sprintf( __('-%s', 'woocommerce' ), $percentage . '%' );
    }
    add_filter( 'woocommerce_get_price_html', 'woocommerce_saved_sales_price', 10, 2 );
     * 
     */
}
/*
 * Return inkston no image
 * 
 * @param string $noimage   image passed by woocommerce
 */
function inkston_noimage($noimage)
{
    return get_template_directory_uri() . '/img/no-image.png';
}
add_filter( 'woocommerce_placeholder_img_src', 'inkston_noimage' );


/**
 * Adds a 'wp-post-image' class to post thumbnails. Internal use only.
 *
 * Uses the {@see 'begin_fetch_post_thumbnail_html'} and {@see 'end_fetch_post_thumbnail_html'}
 * action hooks to dynamically add/remove itself so as to only filter post thumbnails.
 *
 * @ignore
 * @since 2.9.0
 *
 * @param array $attr Thumbnail attributes including src, class, alt, title.
 * @return array Modified array of attributes including the new 'wp-post-image' class.
 */
function inkston_thumbnail_add_title( $attr ) {
    //on woocommerce listing pages, extend image title
    //if ( (is_woocommerce()) && (! is_single() )){ 
    if  (is_woocommerce_activated() && is_woocommerce() ){ 
        $attr['title'] = the_title_attribute(array('echo' => false)) . ' &#10;' . inkston_get_excerpt();
    }
    return $attr;
}
//add_filter( 'wp_get_attachment_image_attributes', 'inkston_thumbnail_add_title', 10, 1);

if (is_woocommerce_activated()){
    /**
     * wrap subcategory thumbnails to show tooltips
     *
     * @param mixed $category
     */
    function pre_woocommerce_subcategory_thumbnail($category)
    {
        $title = $category->name;
        $description =  wp_trim_words( strip_shortcodes( $category->description ), 20);
        if ($description){
            $title .= ' &#10;' . $description;
        }
        echo('<span class="tooltip" title="' . $title . '">');
            echo('<span class="tooltiptext">');
                woocommerce_template_loop_category_title($category);
                echo('<span class="imgwrap">');
                    woocommerce_subcategory_thumbnail($category);
                echo('</span>');
                echo(wp_trim_words(strip_shortcodes($category->description), 60));
            echo('</span>');
    }
    function post_woocommerce_subcategory_thumbnail($category)
    {
        //<span class="tooltip" opened in pre_woocommerce_subcategory_thumbnail
        echo('</span>');
    }
    add_action( 'woocommerce_before_subcategory_title', 'pre_woocommerce_subcategory_thumbnail', 9 );
    add_action( 'woocommerce_after_subcategory_title', 'post_woocommerce_subcategory_thumbnail', 11 );

    /*
     * get tooltip for products 
     *
     * @see woocommerce_template_loop_product_title()
     * @see woocommerce_template_loop_product_thumbnail()
     */
    function inkston_product_tooltip(){
        global $post;

        echo('<span class="tooltip" title="' . get_the_title() . '">');
            echo('<span class="tooltiptext">');
                woocommerce_template_loop_product_title();
                echo('<span class="imgwrap">');
                    woocommerce_template_loop_product_thumbnail();
                echo('</span>');
                echo(inkston_get_excerpt(60). '<br/>');

                $product = wc_get_product($post);
                /*
                ob_start();  
                wc_get_template( 'single-product/product-attributes.php' , array(
                    'product'            => $product,
                    'attributes'         => array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' ),
                    'display_dimensions' => apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ),
                ) );
                $detail = ob_get_clean();
                echo(wp_trim_words(strip_shortcodes($detail), 60));
                */
                woocommerce_template_loop_price();
               inkston_product_simple_attributes($product,
                    array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' ),
                    apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() )
                );
                echo('</span>');
    }
    add_action( 'woocommerce_before_shop_loop_item', 'inkston_product_tooltip', 20 );

    /*
     * close tooltip wrapper after product to avoid spacing caused by closing it before...
     */
    function inkston_product_tooltip_close(){
        echo('</span>');    
    }
    add_action( 'woocommerce_after_shop_loop_item', 'inkston_product_tooltip_close', 50 ); 
    /*
     * get formatted value string for attribute
     * 
     * @param WC_Product_Attribute  $attribute
     * @return string   formatted string
     */
    if ( !function_exists( 'getAttrValueString' ) ) {
    function getSimpleAttrValueString($attribute)
    {
        $values = array();
        $valuestring='';
        $hasdescription=false;
        global $product;

        if ( $attribute->is_taxonomy() ) {
            $attribute_taxonomy = $attribute->get_taxonomy_object();
            $attribute_terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), 
                array( 'fields' => 'all' ) );

            foreach ( $attribute_terms as $attribute_term ) {
                $value_name = esc_html( $attribute_term->name );
                $values[] = $value_name;
            }
        } else {
            $values = $attribute->get_options();

            foreach ( $values as $value ) {
                $value = esc_html( $value );
            }
        }
        $valuestring = wptexturize( implode( ', ', $values ) );
        return apply_filters( 'woocommerce_attribute', $valuestring, $attribute, $values );    
    }
    }
    /*
     * output rows for attribute key-value pairs
     * 
     * @param Array     values keyed by display name
     * @return string   formatted string
     */
    if ( !function_exists( 'outputAttributes' ) ) {
    function outputSimpleAttributes($attrKeyValues, $type, $variable)
    {
        global $product;
        foreach ($attrKeyValues as $key => $value ) {
            $cellclass='';
            if ( is_array($value) ){
                $value = implode(', ', $value);
            }
            if ($type=='codes'){ 
                switch ($key){
                    case "_sku":
                        $cellclass = 'woocommerce-variation-custom-' . $key;
                        $key='SKU';
                        break;
                    default:
                        $cellclass = 'woocommerce-variation-custom-' . $key;
                        $key = strtoupper($key);         
                }
            } else {
                switch ($key){
                    case "net_weight":
                        $cellclass = 'woocommerce-variation-custom-' . $key;
                        $key=__('Product Weight', 'photoline-inkston');
                        break;
                    case "net_size":
                        $cellclass = 'woocommerce-variation-custom-' . $key;
                        $key=__('Product Size', 'photoline-inkston');
                        break;
                    case "product_weight":
                        $cellclass = $key;
                        $key = __( 'Weight', 'woocommerce' );
                        if (($value==__( 'N/A', 'woocommerce' )) && ( $product->get_type()=='variable') ){
                            $value=__('[depending on variation]', 'photoline-inkston');
                        }
                        break;
                    case "product_dimensions":
                        $cellclass = $key;
                        $key= __( 'Dimensions', 'woocommerce' );
                        if (($value==__( 'N/A', 'woocommerce' )) && ( $product->get_type()=='variable') ){
                            $value=__('[depending on variation]', 'photoline-inkston');
                        }
                        break;
                }            
            }
            echo($key . ': ' . $value . '<br />');
            //echo('<tr class="'.$type.'"><th>' . $key . '</th> ');
            //echo(' <td class="' . $cellclass .'">' . $value . '</td></tr>');        
        }
    }
    }

    function inkston_product_simple_attributes($product, $attributes, $display_dimensions){ 
    /*Product Attributes data structure:
     * 		'id'        => 0,
     *		'name'      => '',
     *		'options'   => array(), //array of term ids, see class-wc-product-attribute get_terms, get_slugs
     *		'position'  => 0,
     *		'visible'   => false,
     *		'variation' => false,
     *
     */
    global $product;
    $variationattributes=array();
    $archiveattributes=array();
    $dimensionattributes=array();
    $otherattributes=array();
    $variable = ( $product->get_type()=='variable') ? true : false;


    if (! $variable){
        if ( $display_dimensions ) {
            if ( $product->has_weight() ){
                $dimensionattributes['product_weight'] = esc_html( wc_format_weight( $product->get_weight() ) );
            }
            if ( $product->has_dimensions() ){
                $dimensionattributes['product_dimensions'] = esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) );
            }

        }
        $net_weight = get_post_meta($product->get_id(), 'net_weight', false);
        if ($net_weight){
            if ( is_array($net_weight) ){
                $net_weight = recursive_filter_implode(', ', $net_weight);        
                $dimensionattributes['net_weight'] = $net_weight;
            } else {
                $dimensionattributes['net_weight'] = esc_html( wc_format_weight( $net_weight ) );
            }
            //in simple view, if there is a net weight, unset the shipping weight
            unset($dimensionattributes['product_weight']);
        }
        $net_size = get_post_meta($product->get_id(), 'net_size', true);
        if ($net_size){
            $value = esc_html( wc_format_dimensions( $net_size ));
            if ($value==__( 'N/A', 'woocommerce' )){
               if ( $product->get_type()=='variable' ){
                    //$value=__('[depending on variation]', 'photoline-inkston');
                    $dimensionattributes['net_size'] = ''; //$value;
                    unset($dimensionattributes['product_size']);
                } else {
                    $value='';
                }
            } else {
                $dimensionattributes['net_size'] = $value;
                unset($dimensionattributes['product_size']);
            }
        }
    }

    foreach ( $attributes as $attribute ){
        if ($attribute->get_visible()){
            $name = $attribute->get_name();
            $displayname = wc_attribute_label( $attribute->get_name() );
            $displayvalue = getSimpleAttrValueString( $attribute );
            if (strpos(strtolower($name), 'weight')){
                $dimensionattributes[$displayname]=$displayvalue;
            } elseif (strpos(strtolower($name), 'size')) {
                $dimensionattributes[$displayname]=$displayvalue;
            } elseif ($attribute->get_variation()){
                //don't list variation attributes on summary page
                //$variationattributes[$displayname]=$displayvalue;
            } elseif (strpos($displayvalue, '<a href')){
                $archiveattributes[$displayname]=$displayvalue;
            } else{
                $otherattributes[$displayname]=$displayvalue;
            }
        }
    }
    /* don't need ids in simple view
    $idfields = array();
    $idkeys = array('asin', '_sku', 'upc');
    foreach ($idkeys as $key){
        $value = get_post_meta( $product->get_id(), $key, true );
        if ($value || $variable){
            $idfields[$key] = $value;
        }
    }
    */
        ksort($dimensionattributes);
        //ksort($variationattributes);
        ksort($archiveattributes);
        ksort($otherattributes);
        //outputSimpleAttributes($variationattributes, 'variations', false);
        outputSimpleAttributes($archiveattributes, 'archive-attributes', false);
        outputSimpleAttributes($otherattributes, 'attributes', false);
        outputSimpleAttributes($dimensionattributes, 'dimensions', true);
        //outputSimpleAttributes($idfields, 'codes', true);        
    }


    /*
     * show remaining amount necessary to qualify for free shipping
     */
    function inkston_show_free_shipping_qualifier()
    {
        $shippingnote=inkston_get_cart_message(0);
        if ($shippingnote){
            echo('<span class="shipping-note">' . $shippingnote . '</span>');
        }
    }
    add_action('woocommerce_after_shipping_calculator', 'inkston_show_free_shipping_qualifier', 10, 0);

    function inkston_free_shipping_level(){
        $level = apply_filters( 'raw_woocommerce_price', 150);
        if (isWoocs()) {
            global $WOOCS;        
            $level = $WOOCS->woocs_exchange_value($level);
        }
        return $level;
    }
    function inkston_free_shipping_encourage_level(){
        $level = apply_filters( 'raw_woocommerce_price', 100);
        if (isWoocs()) {
            global $WOOCS;        
            $level = $WOOCS->woocs_exchange_value($level);
        }
        return $level;
    }

    /*
     * Calculate free shipping message based on current cart amount and any value added
     * 
     * @param decimal $valueadd  
     * 
     * @return string formatted html message '... has been added..  continue shopping'
     */
    function inkston_get_cart_message($valueadd){
        //cart and barrier levels translated into current currency
        $encouragement_level = inkston_free_shipping_encourage_level();
        $free_level =  inkston_free_shipping_level();
        $carttotal = WC()->cart->cart_contents_total;

        $shippingnote='';
        if ($carttotal>$free_level){
            //if new items have just pushed total into free shipping eligibility
            if ( ($carttotal - $valueadd) < $free_level ){
                $shippingnote = __('Congratulations, your order is now eligible for free shipping!', 
                    'photoline-inkston');
            } else {
                $shippingnote =  __( 'Your order qualifies for free shipping!', 
                    'photoline-inkston' );
            }
        } elseif ($carttotal>$encouragement_level){        
            $shortfall = $free_level - $carttotal;
            $shortfall = wc_price($shortfall);
            $shippingnote = sprintf( __( 'Add %s more to your order to qualify for free shipping!', 
                'photoline-inkston' ), $shortfall );
        }
        return $shippingnote;
    }
    /*
     * Check and add to flash message which appears after adding item to basket
     * 
     * @param string $message   formatted html message '... has been added..  continue shopping'
     * @param array $products   array of product ids and quantities just added to basket 
     */
    function inkston_cart_free_shipping_qualifier($message, $products ){
        //get value just added
        $valueadd=0;
        foreach ( $products as $product_id => $qty ) {
            $product = wc_get_product($product_id);
            $valueadd+=($product->get_price() * $qty);
        }
        $carttotal = WC()->cart->cart_contents_total;
        $shippingnote=inkston_get_cart_message($valueadd);

        if ($shippingnote){
            $message .= '&#010;<br/>' . $shippingnote;
        }
        return $message;
    }
    add_filter( 'wc_add_to_cart_message_html', 'inkston_cart_free_shipping_qualifier', 10, 2);
}

if ( ! function_exists( 'inkston_title' ) ) {
	function inkston_title(){
		static $title;
		if (!isset($title)) {

			/* get default title, overridden by Yoast SEO as appropriate */
			$title = wp_title('&raquo;', false, '');
      if (is_search()){
        if (get_search_query()==''){
          $title = __( 'Search Inkston.', 'photoline-inkston' );;
        }
        else{
          global $wp_query;
          $title .= ' (' . $wp_query->found_posts . ' ' . __('results', 'photoline-inkston' ) . ')';
        }
      }
			/**
			 * Template WooCommerce
			 */
			if (is_woocommerce_activated()) {
				if (is_woocommerce() && !is_product()) {
					$title = woocommerce_page_title(false);
				}
			}    /* if ( is_woocommerce_activated() ) */
		}
    /* remove trailing Inkston if added by Yoast SEO  */
    $title = str_replace ( '- Inkston', '', $title );

		return $title;
	}
}

if ( ! function_exists( 'inkston_output_paging' ) ) {
	/**
	 * Display navigation to next/previous pages when applicable
	 */
	function inkston_output_paging()
	{
		/* all posts pages */
		if (is_single() && !is_attachment()) {
			/**
			 * add navigation for posts pages - works also for custom post types ie wooCommerce product
			 */ ?>
			<nav id="single-nav">
				<?php 
        if (is_woocommerce_activated() && is_woocommerce() && is_product()) {
          previous_post_link('<div id="single-nav-right">%link</div>', '<i class="fa fa-chevron-left"></i>', true, '' , 'product_cat');
          next_post_link('<div id="single-nav-left">%link</div>', '<i class="fa fa-chevron-right"></i>', true, '', 'product_cat'); 
        }
        else {
          previous_post_link('<div id="single-nav-right">%link</div>', '<i class="fa fa-chevron-left"></i>', true);
          next_post_link('<div id="single-nav-left">%link</div>', '<i class="fa fa-chevron-right"></i>', true); 
        }
        ?>
			</nav><!-- /single-nav -->
			<?php
		} /* image media attachment pages - not in fact used currently, disabled by one of the plugins*/
		elseif (is_attachment()) { ?>
			<nav id="single-nav">
				<div
					id="single-nav-right"><?php previous_image_link('%link', '<i class="fa fa-chevron-left"></i>'); ?></div>
				<div
					id="single-nav-left"><?php next_image_link('%link', '<i class="fa fa-chevron-right"></i>'); ?></div>
			</nav><!-- /single-nav -->
			<?php
		}
	}
}

if (is_woocommerce_activated()){
    /*
     * add cart single flash message to explain about customization options
     */
    function inkston_customization_cart_message()
    {
        $class_exists=class_exists( 'Alg_WC_Checkout_Files_Upload_Main');
        if ( ( is_cart() ) && ( $class_exists ) ){ //&& (class_exists( 'Alg_WC_Checkout_Files_Upload_Main') ) ) {
            global $AWCCF;
            //$awccf = new Alg_WC_Checkout_Files_Upload_Main;
            if ( $AWCCF->is_visible(1) ) {
              wc_print_notice( __('Your shopping cart includes customization options, you can tell us about these on the checkout page.' , 'photoline-inkston'), 'notice');
            }
        }
    }
    add_action( 'woocommerce_before_cart', 'inkston_customization_cart_message' );

    /*
     * add checkout single flash message to explain about customization options
     */
    function inkston_customization_checkout_message()
    {
        $class_exists=class_exists( 'Alg_WC_Checkout_Files_Upload_Main');
        if ( ( is_checkout() ) && ( $class_exists ) ){ //&& (class_exists( 'Alg_WC_Checkout_Files_Upload_Main') ) ) {
            //$awccf = new Alg_WC_Checkout_Files_Upload_Main;
            global $AWCCF;
            if ( $AWCCF->is_visible(1) ) {
              wc_print_notice( __('Your order has a custom design option, if you like you can upload a file and/or make comments below. You may also skip this step and confirm details with us later.' , 'photoline-inkston'), 'notice');
            }
        }
    }
    add_action( 'woocommerce_before_checkout_form', 'inkston_customization_checkout_message' );

    /*
     * Cheque or "other payment method" goes straight to on hold (which is better than pending
     * because the emails are issued) and is intended to allow offline payment
     * .. but allow the client to change their mind and pay online...
     * 
     * @param Array $valid_order_statuses   order statuses in which this order can be paid for..
     * @param WC_Order $order       the current order
     * 
     * @return Array                array of valid order status strings
     */
    function inkston_allow_pay_onhold($valid_order_statuses, $order)
    {
        $valid_order_statuses[]='on-hold';
        return $valid_order_statuses;
    }
    add_filter( 'woocommerce_valid_order_statuses_for_payment', 'inkston_allow_pay_onhold', 10, 2);


    /*
     * .. allow the client to change their mind and cancel On Hold orders...
     * 
     * @param Array $valid_order_statuses   order statuses in which this order can be paid for..
     * 
     * @return Array                array of valid order status strings
     */
    function inkston_allow_cancel_onhold($valid_order_statuses)
    {
        $valid_order_statuses[]='on-hold';
        return $valid_order_statuses;
    }
    add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'inkston_allow_cancel_onhold', 10, 1);

    function inkston_suppress_shop_next_link($link)
    {
        //if (is_page( wc_get_page_id( 'shop' ) )){
        if (is_shop()){    
          return '';
        } else {
          return $link;
        }
    }
    add_filter( 'wpseo_next_rel_link', 'inkston_suppress_shop_next_link', 10, 1);

}

/**
 * Recursively implodes an array with optional key inclusion
 * 
 * Example of $include_keys output: key, value, key, value, key, value
 * 
 * @access  public
 * @param   array   $array         multi-dimensional array to recursively implode
 * @param   string  $glue          value that glues elements together	
 * @param   bool    $include_keys  include keys before their values
 * @param   bool    $trim_all      trim ALL whitespace from string
 * @return  string  imploded array
 */ 
function recursive_filter_implode($glue, $array, $include_keys = false, $trim_all = true)
{
    if (! is_array($array)){return $array;}
	$glued_string = '';
    $array = array_filter($array);
	// Recursively iterates array and adds key/value to glued string
	array_walk_recursive($array, 
        function($value, $key) use ($glue, $include_keys, &$glued_string)
        {
            if ($value){
                $include_keys and $glued_string .= $key.$glue;
                $glued_string .= $value.$glue;
            }
        });
	// Removes last $glue from string
	strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));
	// Trim ALL whitespace
	$trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);
	return (string) $glued_string;
}

/**
 * Remove styles on non-bbPress page
 * 
 * @param   array   $styles      styles bbPress wants to add
 * @return  array   styles to queue
 */ 
function remove_bbpress_styles($styles)
{
    if ( (! function_exists('is_bbpress')) || ( (! is_bbpress()) && (! is_front_page())) ) {
        return [];
    } else {
        if ( (! defined('SCRIPT_DEBUG') ) || (SCRIPT_DEBUG==false) ){
            foreach ($styles as $key => $style){
                $style['file'] = str_replace('.css', '.min.css', $style['file']);
                $styles[$key]=$style;
            }
        }
        return $styles;
    }
}
add_filter( 'bbp_default_styles', 'remove_bbpress_styles', 10, 1 );


/**
 * Remove scripts on non-bbPress page
 * 
 * @param   array   $scripts      scripts bbPress wants to add
 * @return  array   scripts to queue
 */ 
function remove_bbpress_scripts($scripts)
{
    if (! is_bbpress()){
        remove_action('wp_print_scripts', 'bbpress_auto_subscription_ajax_load_scripts');
        return [];
    } else {
        /* bbPress doesn't actually supply minified scripts
        foreach ($scripts as $key => $script){
            $script['file'] = str_replace('.js', '.min.js', $script['file']);
            $scripts[$key]=$style;
        }
         */
        return $scripts;
    }
}
add_filter( 'bbp_default_scripts', 'remove_bbpress_scripts', 10, 1 );

/**
 * shuffle input array of post objects
 * 
 * @param   array   $array array of post objects
 * @return  array   array of post objects
 */ 
function shuffle_assoc($array)
{
    // Initialize
    $shuffled_array = array();

    // Get array's keys and shuffle them.
    $shuffled_keys = array_keys($array);
    shuffle($shuffled_keys);


    // Create same array, but in shuffled order.
    foreach ( $shuffled_keys AS $shuffled_key ) {
        $shuffled_array[  $shuffled_key  ] = $array[  $shuffled_key  ];
    } // foreach

    // Return
    return $shuffled_array;
}            
/**
 * Get selection of featured, recent and sale products and posts
 * 
 * @return  array   array of post objects
 */ 
function get_featured_posts()
{

    //RECENT POSTS
    $query_args = array(
        'ignore_sticky_posts' => 0, //sticky posts automatically added by WP
        'post_type' => array( 'post' ),
        'orderby' => 'modified',
        'posts_per_page' => 50,
        'showposts'   =>  50,
        'order' => 'DESC'
    );
    $recent_list = new WP_Query( $query_args );
    
    //FEATURED PRODUCTS 
    $final_posts = [];
    if (is_woocommerce_activated()){
    $query_args = array(
        'posts_per_page' => 100,
        'showposts'   =>  100,
        'post_status' => 'publish',
        'post_type'   => 'product',
        'post__in'    => array_merge( array( 0 ), wc_get_featured_product_ids(), wc_get_product_ids_on_sale()  ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
    );
    $product_list = new WP_Query( $query_args );      
    //SALE PRODUCTS 
    /*
    $query_args = array(
        'posts_per_page' => 25,
        'showposts'   =>  25,
        'post_status' => 'publish',
        'post_type'   => 'product',
        'post__in'    => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
    );
    $sale_list = new WP_Query( $query_args );    
    */

    //RECENT NON-FEATURED PRODUCTS 
    $query_args = array(
        'post_type'   =>  'product',
        'posts_per_page' => 25,
        'showposts'   =>  25,
        'post_status' => 'publish',
        'post__not_in' => array_merge( array( 0 ), wc_get_product_ids_on_sale(), wc_get_featured_product_ids() ),
        'orderby'     =>  'modified',
        'order'       =>  'DESC',
    );    
    $recentproduct_list = new WP_Query( $query_args );      

    //$final_posts = array_merge( $recent_list->posts, $product_list->posts, $sale_list->posts, $recentproduct_list->posts  );
    $final_posts = array_merge( $recent_list->posts, $product_list->posts, $recentproduct_list->posts  );
    } else {
        $final_posts = $recent_list->posts;
    }

    //$final_posts = array_unique ( $final_posts);
    //$final_posts = shuffle_assoc($final_posts);
    shuffle($final_posts);
    return $final_posts;
}

/**
 * Force add super socializer to login form for business directory
 * 
 * @param string $content Content to display. Default empty.
 * @param array  $args    Array of login form arguments.
 * 
 * @return  string Content to display
 */ 
function ink_login_form_add_socializer($content , $args )
{
    if (function_exists('the_champ_login_button')){
        return $content . the_champ_login_shortcode(
            array(
			'title' => __('Login or register with Facebook, LinkedIn, Google', 'photoline-inkston') 
            ) ) . '<div id="ink_login_message">' . 
            __('Or use your Inkston login:', 'photoline-inkston') .
            '</div>';
    } else {
        return $content;
    }
}
add_filter( 'login_form_top', 'ink_login_form_add_socializer', 10, 2 );

/**
 * Add CPTs to author archives
 * 
* @param WP_Query &$this The WP_Query instance (passed by reference).
*/ 
function custom_post_author_archive($query) {
    if ($query->is_author)
    {
        $query->set( 'post_type', array('wpbdp_listing', 'post') );
    }
    remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}
add_action('pre_get_posts', 'custom_post_author_archive');


function get_directory_labels(){
    static $tr_directory_label;
    $tr_directory_label['Name'] = __('Name', 'photoline-inkston');
    $tr_directory_label['Country'] = __('Country', 'photoline-inkston');
    $tr_directory_label['Contact Email'] = __('Contact Email', 'photoline-inkston');
    $tr_directory_label['Location'] = __('Location', 'photoline-inkston');
    $tr_directory_label['About'] = __('About', 'photoline-inkston');
    $tr_directory_label['Summary'] = __('Summary', 'photoline-inkston');
    $tr_directory_label['Website'] = __('Website', 'photoline-inkston');
    return $tr_directory_label;
}
function get_directory_descriptions(){
    $tr_directory_description['Artist or studio name'] = __('Artist or studio name ', 'photoline-inkston');
    $tr_directory_description['Main country'] = __('Main country ', 'photoline-inkston');
    $tr_directory_description['Please enter town or region to help visitors find you'] = __('Please enter town or region to help visitors find you ', 'photoline-inkston');
    $tr_directory_description['To avoid spam, Email address will never be shown, instead a contact form will be provided which is only available to genuine logged-on users.'] = __('To avoid spam, Email address will never be shown, instead a contact form will be provided which is only available to genuine logged-on users. ', 'photoline-inkston');
    $tr_directory_description['How did you start with Oriental arts? What are your favourite techniques?  Do you sell your artwork, do you accept commissions?  Do you teach or can you recommend teachers?'] = __('How did you start with Oriental arts? What are your favourite techniques?  Do you sell your artwork, do you accept commissions?  Do you teach or can you recommend teachers? ', 'photoline-inkston');
    $tr_directory_description['Here you can make a special short summary for search engines and search results.  Leave blank for an automatic summary.'] = __('Here you can make a special short summary for search engines and search results.  Leave blank for an automatic summary. ', 'photoline-inkston');
    $tr_directory_description['Main website (can be any link including Facebook page if that is your main page)'] = __('Main website (can be any link including Facebook page if that is your main page) ', 'photoline-inkston');   
    $tr_directory_description['add optional tags separated by commas, to allow more classifications than available under categories '] = __('add optional tags separated by commas, to allow more classifications than available under categories ', 'photoline-inkston');   
    return $tr_directory_description;
}

function ink_wpbdp_field_label($label){
    $locale = get_locale();
    $tr_directory_label = get_directory_labels();
    switch($locale){
        case 'fr_FR':
        case 'es_ES':
            if (isset($tr_directory_label[$label])){
                $label = $tr_directory_label[$label];
            }
            break;
    }
    return $label;
}
add_filter('wpbdp_render_field_label', 'ink_wpbdp_field_label', 10, 1);

function ink_wpbdp_field_description($description){
    $locale = get_locale();
    $tr_directory_description = get_directory_descriptions();
    switch($locale){
        case 'fr_FR':
        case 'es_ES':
            if (isset($tr_directory_description[ trim($description) ] )){
                $description = $tr_directory_description[trim($description)];
            }
            break;
    }
    return $description;
}
add_filter('wpbdp_render_field_description', 'ink_wpbdp_field_description', 10, 1);

//filter to add description after forums titles on forum index
function rw_singleforum_description() {
  echo '<div class="bbp-forum-content">';
  echo bbp_forum_content();
  echo '</div>';
}
add_action( 'bbp_template_before_single_forum' , 'rw_singleforum_description');


function ink_add_checkout_message($message, $products){
    $checkouturl = esc_url( wc_get_page_permalink( 'checkout' ) );
    $checkoutlabel = esc_html__( 'Checkout', 'woocommerce' );
    $checkoutbutton = sprintf( ' &nbsp; <a href="%s" class="button wc-forward"> &nbsp; %s  &nbsp; </a>', 
        $checkouturl, $checkoutlabel );
//        esc_url( wc_get_page_permalink( 'checkout' ) ), 
//        esc_html__( 'Checkout', 'woocommerce' ) 
    
    return $checkoutbutton . $message;
}
add_filter( 'wc_add_to_cart_message_html', 'ink_add_checkout_message', 10, 2 );
