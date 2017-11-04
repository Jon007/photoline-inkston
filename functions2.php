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
            if ($AWCCF && $AWCCF->is_visible(1) ) {
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
            if ($AWCCF && ( $AWCCF->is_visible(1)) ) {
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

    $final_posts = [];
    $tKey = 'inkfeat';
    $final_posts = get_transient($tKey);
    if(! $final_posts){
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
            set_transient($tKey, $final_posts, 24 * 60 * 60);
        } else {
            $final_posts = $recent_list->posts;
        }
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
        switch ($query->query_vars['post_type']) {
            case 'topic':
            case 'reply':
                break;
            default:
                $query->set( 'post_type', array('wpbdp_listing', 'post') );
                remove_action( 'pre_get_posts', 'custom_post_author_archive' );
        }
    }
}
add_action('pre_get_posts', 'custom_post_author_archive', 1, 1);


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


function ink_bbp_mail_alert( $message, $reply_id, $topic_id ){
	$topic_title  = strip_tags( bbp_get_topic_title( $topic_id ) );
    $forum_title = bbp_get_topic_forum_title($topic_id);

	$messageheader = sprintf( __( 'New post in Forum "%1$s", Topic "%2$s".', 'photoline-inkston' ),
		$forum_title,
		$topic_title
	);
    $profilelink = network_site_url() . 'community/my-profile/';
	$messagefooter = sprintf( __( 'Or visit your profile page to review all your subscriptions: %1$s', 'photoline-inkston' ),
		$profilelink
	);
    $messagefooter .= "\r\n" . "\r\n" . __('Thankyou for participating in Inkston Community', 'photoline-inkston' );
    
    return $messageheader . "\r\n" . "\r\n" . $message . "\r\n" . $messagefooter;
}
add_filter( 'bbp_subscription_mail_message', 'ink_bbp_mail_alert', 10, 3);


function ink_bbp_distributionaddress($address){
    return __('forum-subscribers@inkston.com', 'photoline-inkston');
}
add_filter( 'bbp_get_do_not_reply_address', 'ink_bbp_distributionaddress', 10, 1);

function get_user_points($atts = array()){
    $a = shortcode_atts( array(
        'user_id' => ink_user_id(),
    ), $atts );
    return badgeos_get_users_points($a['user_id']);
}
add_shortcode('inkpoints', 'get_user_points');

function get_user_level($atts = array()){
    $a = shortcode_atts( array(
        'user_id' => ink_user_id(),
		'achievement_type' => 'badge', // A specific achievement type
        'size' => 'badgeos-achievement',  //thumbnail image size        
		'style' => 'html', // formatted output with image and text
		//'style' => 'full', // complete output including congratulation text
		//'style' => 'img', // image tag for current badge only
		//'style' => 'imglink', // image tag for current badge wrapped in link
		//'style' => 'imgurl', // image url for current badge only
		//'style' => 'text', // text name for current badge only
		//'style' => 'url', // url for current badge only
		//'style' => 'textlink', // text name and link for current badge only
		//'style' => 'int', // level number (menu order +1) for current badge only        
		//'style' => 'score', // highest badge and score details
        ), $atts );
    $output = '';
    if (function_exists('badgeos_get_user_achievements')){
    $user_achievements = badgeos_get_user_achievements($a);
    if (! $user_achievements || sizeof($user_achievements)==0){
        return __('No badges yet', 'photoline-inkston');
    }
    
    $user_achievement_ids = wp_list_pluck($user_achievements, 'ID');
    
    $achievements = badgeos_get_achievements(array(
        'post_type' => 'badge',
		'suppress_filters' => true,
		'numberposts' => 1,
		'orderby' => 'menu_order',
		'order' => 'DESC', 
        'include' => $user_achievement_ids,
        ));   

    $post = $achievements[0];
    switch ($a['style']) {
        case 'brushes':
            $level = intval($post->menu_order);
            $output = '<span title="'. $post->post_title . 
                ' (' . __('level', 'photoline-inkston') . $level . ')' .
                '" class="brushlevel">';
            for ($x = 1; $x <= $level; $x++) {
                $output .= '<i class="fa fa-paint-brush"></i>';
            }
            $output .= '</span>';
            break;
        case 'int':
            $output = intval($post->menu_order);
            break;
        case 'img':
            $output = get_the_post_thumbnail($post, $a['size']);
            break;
        case 'imgurl':
            $thumb_id = get_post_thumbnail_id($post);
            $thumb_url_array = wp_get_attachment_image_src($thumb_id, $a['size'], true);
            $output = $thumb_url_array[0];
            break;
        case 'text':
            $output = $post->post_title;
            break;
        case 'url':
            $output = get_permalink($post);
            break;
        case 'imglink':            
            $output = '<div class="badgeos-item-image"><a href="' . get_permalink($post) . '">' . get_the_post_thumbnail($post, $a['size']) . '</a></div>';
            break;
        case 'textlink':
            $output = '<div class="badgeos-item-description"><h2 class="badgeos-item-title"><a href="' . 
                get_permalink($post) . '">' . $post->post_title . '</a></h2></div>';
            break;
        case 'score':
            $output = '<div class="inkpoints"><div class="badgeos-item-image"><a href="' . get_permalink($post) . '">' . get_the_post_thumbnail($post, $a['size']) . '</a></div>';
            $output .= '<div class="badgeos-item-description"><p>' . 
                __('Current level: ', 'photoline-inkston') .
                    ' <a href="' . get_permalink($post) . '">' . $post->post_title . 
                    ' (' . __('level ', 'photoline-inkston') . (intval($post->menu_order)) .  ')</a>' .
                '<br />' .
                sprintf( __( 'Current score: %1$s points.', 'photoline-inkston' ), get_user_points() ) . 
                '</p></div></div>';
            break;
        case 'html':  //one block with badge and badge description (not award message)
            $user_id = $a['user_id'];
            $output = badgeos_render_achievement($post->ID, $user_id);            
            break;
        case 'full':
        default:  //html
/*            $output = '<div class="badgeos-item-image"><a href="' . get_permalink($post) . '">' . get_the_post_thumbnail($post) . '</a></div>';
            $output .= '<div class="badgeos-item-description"><h2 class="badgeos-item-title"><a href="' . 
                get_permalink($post) . '">' . $post->post_title . '</a></h2>' . 
                '<div class="badgeos-item-excerpt">' . $post->excerpt . '</div>' . 
                '</div>';
*/
            $user_id = $a['user_id'];
            $output = badgeos_render_achievement($post->ID, $user_id);
            $output .= ' <br/>' . badgeos_render_earned_achievement_text($post->ID, $user_id);
            
    }
    }
    return $output;
}
add_shortcode('inklevel', 'get_user_level');

function ink_user_id(){
    if (function_exists('bbp_get_displayed_user_id')){
        return bbp_get_displayed_user_id();
    } else {
        return get_current_user_id();
    }
}

/*
 * allow shortcodes in forum posts
 */
function pw_bbp_shortcodes( $content, $reply_id ) {
	if ( (! is_feed() ) && ( stripos($_SERVER['REQUEST_URI'], '/feed')===FALSE ) ) 
	{
        //$reply_author = bbp_get_reply_author_id( $reply_id );
        //if( user_can( $reply_author, pw_bbp_parse_capability() ) ){
              return do_shortcode( $content );
        //}
    } 
    return strip_shortcodes($content);
}
add_filter('bbp_get_reply_content', 'pw_bbp_shortcodes', 10, 2);
add_filter('bbp_get_topic_content', 'pw_bbp_shortcodes', 10, 2);

function pw_bbp_parse_capability() {
	return apply_filters( 'pw_bbp_parse_shortcodes_cap', 'publish_forums' );
}

/* didn't quite seem to work..
function ink_badge_triggers($triggers){
    $triggers['badgeos_new_wpbdp_listing'] = __( 'Publish a new directory listing', 'photoline-inkston' );
    return $triggers;
}
add_filter( 'badgeos_activity_triggers', 'ink_badge_triggers', 10, 1);
*/

/**
 * Displays a members achievements
 *
 * @since 1.0.0
 */
function ink_bp_member_achievements_content() {

    $userid = ink_user_id();
    if (! $userid){return;}
    if (! function_exists('badgeos_get_network_achievement_types_for_user')){return;}
	$achievement_types = badgeos_get_network_achievement_types_for_user( $userid );
	// Eliminate step cpt from array
	if ( ( $key = array_search( 'step', $achievement_types ) ) !== false ) {
		unset( $achievement_types[$key] );
		$achievement_types = array_values( $achievement_types );
	}

	$type = '';

	if ( is_array( $achievement_types ) && !empty( $achievement_types ) ) {
		foreach ( $achievement_types as $achievement_type ) {
			$name = get_post_type_object( $achievement_type )->labels->name;
			$slug = str_replace( ' ', '-', strtolower( $name ) );
			if ( $slug && strpos( $_SERVER['REQUEST_URI'], $slug ) ) {
				$type = $achievement_type;
			}
		}
		if ( empty( $type ) )
			$type = $achievement_types[0];
	}

	$atts = array(
//		'type'        => $type,
		'type'        => 'badge,point',
		'limit'       => '10',
		'show_filter' => 'false',
		'show_search' => 'false',
		'group_id'    => '0',
		'user_id'     => $userid,
        'orderby'     => 'menu_order', 
        'order'       => 'ASC',
		'wpms'        => badgeos_ms_show_all_achievements(),
	);
	echo badgeos_achievements_list_shortcode( $atts );
}

//function for highest badge: badgeos_achievements_list_shortcode limit to 1, return by last sort order..


/*
 * set achievements pending notification (so they are available to show to user after redirect)
 * @param int    $user_id    User ID.
 * @param int    $achievement_id new achievement awarded to this user
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function ink_set_achievements_to_notify($user_id, $achievement_id){
    if ($achievement_id){
        //return update_user_meta($user_id, '_ink_badge_pending', $achievement_id);

        $achievements = ink_get_achievements_to_notify($user_id);
        if ($achievements){
            if (! is_array($achievements)){
                $achievements = explode(',', '' . $achievements);
            }
            if(! in_array($achievement_id, $achievements)){
                array_push($achievements, $achievement_id);
            }
        } else {
            $achievements[] = $achievement_id;
        }
        return update_user_meta($user_id, '_ink_badge_pending', $achievements);
    } else {
        return false;
    }
}
function ink_get_achievements_to_notify($user_id){
    return get_user_meta($user_id, '_ink_badge_pending', true);    
}
function ink_clear_achievements_to_notify($user_id){
    update_user_meta($user_id, '_ink_badge_pending', '');
}
//can't actually print the messages as we are redirected on successful post ....
function ink_print_achievement_messages(){
    $user_id = get_current_user_id();
    if ($user_id){
        $achievements = ink_get_achievements_to_notify($user_id);
    	if(is_array($achievements) && count($achievements) > 0){ 
            ?><div class="bbp-template-notice"><p><?php 
                _e('Congratulations you have been awarded:','photoline-inkston') ?></p><?php 
            for($i = 0, $size = count($achievements); $i < $size; ++$i) {
                if (is_numeric($achievements[$i])){
                    $achievement_type = get_post_type( $achievements[$i] );
                    switch($achievement_type){
                        case 'badge':
                        case 'point':
                    echo(badgeos_render_achievement($achievements[$i], $user_id));
                    echo(badgeos_render_earned_achievement_text($achievements[$i], $user_id));
                            break;
                        default:
                    }
                }
            }
            //TODO: how do we know to clear, maybe there is a redirect and this is never shown??
            //maybe need acknowledgement button
            ink_clear_achievements_to_notify($user_id)
            ?></div><?php 
        }
    }
}
add_action( 'bbp_template_notices', 'ink_print_achievement_messages');


function ink_add_achievement_messages($user_id, $achievement_id, $this_trigger, $site_id, $args){
    //for now only process these alerts on community site since the post ids from main site are not synced to child
    if ($site_id!=2){
        //potential to add woocommerce alerts for site 1...
        //or key by site id for later retrieval
        return false;        
    }
    $achievement_type = get_post_type( $achievement_id );
    switch($achievement_type){
        case 'badge':
        case 'point':
        ink_set_achievements_to_notify($user_id, $achievement_id);
            break;
        default:
            return false;
    }

    //users profile is set to allow badgeos notification emails
    if (badgeos_can_notify_user($user_id)){
        if ('badge' == $achievement_type){
            $to_email = get_userdata( $user_id )->user_email;
            
            $blog_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
            $subject = $blog_name . ' ' . __('your contribution has unlocked an award', 'photoline-inkston');

            $profilelink = network_site_url() . 'community/my-profile/';
            $messagefooter = sprintf( __( 'Visit <a href="%1$s">your profile page</a> to see details of your awards and turn these notifications on or off.', 'photoline-inkston' ),
                $profilelink
            );
            
            $message = __('You were awarded this badge for making contributions to ', 'photoline-inkston');
            $message .= ' ' . __('Inkston Oriental Art Comunity','photoline-inkston');
            $message .= ' <br/>' . badgeos_render_achievement($achievement_id, $user_id);
            $message .= ' <br/>' . badgeos_render_earned_achievement_text($achievement_id, $user_id);
            $message .= ' <br/><br/>' . $messagefooter;
            $message .=  ' <br/><br/>' . __('Thankyou for participating in Inkston Community', 'photoline-inkston');
    
            // Setup "From" email address
            $from_email =  __('rewards@inkston.com', 'photoline-inkston');
            // Setup the From header
            $headers = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>',
                              'Content-Type: text/html; charset=UTF-8');

            // Send notification email
            wp_mail( $to_email, $subject, $message, $headers );
        }
    }
}
add_action( 'badgeos_award_achievement', 'ink_add_achievement_messages', 10, 5);


/* disable shortcodes problematic for relevannsi */
function ink_nosearch_shortcodes($arr){
    $problem_shortcodes = array('inkpoints', 'inklevel', 'badgeos_achievements_list', 'robo-gallery', 'maxmegamenu');
    if (is_array($arr)){
        return array_merge($arr, $problem_shortcodes);
    } else {
        return $problem_shortcodes;
    }
}
add_filter('relevanssi_disable_shortcodes_excerpt', 'ink_nosearch_shortcodes', 10, 1);
add_filter('pre_option_relevanssi_expand_shortcodes', 'ink_nosearch_shortcodes', 10, 1);


/**
 * Filters the avatar to retrieve.
 *
 * @since 2.5.0
 * @since 4.2.0 The `$args` parameter was added.
 *
 * @param string $avatar      &lt;img&gt; tag for the user's avatar.
 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param int    $size        Square avatar width and height in pixels to retrieve.
 * @param string $default     URL for the default image or a default type. Accepts '404', 'retro', 'monsterid',
 *                            'wavatar', 'indenticon','mystery' (or 'mm', or 'mysteryman'), 'blank', or 'gravatar_default'.
 *                            Default is the value of the 'avatar_default' option, with a fallback of 'mystery'.
 * @param string $alt         Alternative text to use in the avatar image tag. Default empty.
 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
 */
function ink_filter_avatar($avatar, $id_or_email, $size, $default, $alt, $args )
{
    //first, if user already has an avatar, which is not cat-generator avatar, return it
    if (strpos($avatar, 'cat-generator-avatars') === false) {
        return $avatar;  //TODO: it could be nice to filter the title and add the user level
    }
    //similarly return if there is no user info to look up 
    if (! $id_or_email){return $avatar;}
    
    //for now only badge avatar on community site
    if (get_current_blog_id()!=2){return $avatar;}
    
    $title = '';
    $user_Id = 0;
    $user = false;
    if (is_numeric($id_or_email)){
        $user_Id = intval($id_or_email);
        if ($user_Id){
            $user = get_user_by('ID', $user_Id);
            $title = $user->display_name;
        }
    } elseif (is_string( $id_or_email )) {
        $user = get_user_by( 'email', $id_or_email );
    } elseif ( $id_or_email instanceof WP_User ){
        $user = $id_or_email;
    } elseif ( $id_or_email instanceof WP_Comment ) {
        if ( 0 < $id_or_email->user_id ){
            $user = get_user_by('ID', $id_or_email->user_id);
        } else {
            $title = $id_or_email->comment_author_email;
        }
    } elseif ( $id_or_email instanceof WP_Post ) {
        $user = get_user_by( 'ID', $id_or_email->post_author );
    }
    if ($user){
        $user_Id = $user->ID;
            $title = $user->display_name;
    }

    if (is_numeric($user_Id)){
        
        $badge = get_user_level( array(
            'user_id' => $user_Id,
            'style' => 'imgurl') );

        if ($badge != __('No badges yet', 'photoline-inkston')){
            $levelname = get_user_level( array(
            'user_id' => $user_Id,
                'style' => 'text') );
            return '<span class="avatar-container">' . 
                '<img alt="' . esc_attr($levelname) . '" src="' . $badge . 
                '" title="' . $title . "\n(" . esc_attr($levelname) . ')' .
                '" class="avatar avatar-' . $size . 
                ' " height="' . $size . '" width="' . $size . 
                '" style="height:'. $size .'px;width:'. $size .'px" />' . 
                //'<div class="avatar"' .
                get_user_level( array(
                'user_id' => $user_Id,
                'style' => 'brushes') ) 
                . '</span>';
        }

    }
	return $avatar;
    
}
add_filter( 'get_avatar', 'ink_filter_avatar', 200, 6 );

function ink_min_avatar_size($args)
{
    if (isset($args['size']) ){
        $size = $args['size'];
        if (is_numeric($size)){
            if ($size < 88){
                $args['size'] = 88;
            }
        }
    }
    return $args;
}
add_filter( 'bbp_after_get_author_link_parse_args', 'ink_min_avatar_size', 10, 1 );
add_filter( 'bbp_after_get_topic_author_link_parse_args', 'ink_min_avatar_size', 10, 1 );

function ink_login_form_shortcode() {
	if ( is_user_logged_in() )
    {
		return '';
    }
	return wp_login_form( array( 'echo' => false ) );
}
add_shortcode( 'ink-login', 'ink_login_form_shortcode' );


function ink_author_link($link, $author_id, $author_nicename){
    if (strpos($link, 'community')==0){
        $link = network_site_url() . 'community/forums/users/' . $author_nicename . '/';
    }
    return $link;
}
//add with higher filter than polylang (20)
add_filter( 'author_link', 'ink_author_link', 30, 3 );


/*
 * Filters the author's display name: this needs care as to where it could be applied 
 * as is also called for the name in the middle of a phrase, and any html is escaped
 *
 * @param string   $value            The value of the metadata.
 * @param int      $user_id          The user ID for the value.
 * @param int|bool $original_user_id The original user ID, as passed to the function.
 */
/*  
function ink_display_user_level($value, $user_id, $original_user_id)
//function ink_display_user_level($author_link, $r)
{
    $ink_user_level = get_user_level( array(
            'user_id' => $user_id,
            'style' => 'text') );    
//            'style' => 'brushes') );  //would return brushes as genericon or fontawesome however result here is htmlencoded, needs to be applied elsewhere
    
    if ($ink_user_level){
        $value .= ' (' . $ink_user_level . ')';
    }
    
    return $value;
}
add_filter('get_the_author_display_name', 'ink_display_user_level', 10, 3);
//add_filter( 'bbp_get_author_link', 'ink_display_user_level', 10, 2);    // $author_link, $r 
*/

function ink_default_wishlist_name($wl){
    if ( array_key_exists( 'author', $wl ) && array_key_exists( 'title', $wl ) ) {
        $user     = get_userdata( $wl['author'] );
        if ($user && $user->user_nicename ){
          $wl['title'] = $user->user_nicename . ' ' .  $wl['title'];
        }
    }
    return $wl;
}
add_filter( 'tinvwl_wishlist_get', 'ink_default_wishlist_name', 10, 1);

function ink_sharing(){
    if (is_admin()|| is_feed() ){
        return;
    }
    if ( function_exists('bbp_is_single_user') && bbp_is_single_user() ){
        return;
    }
    if (is_woocommerce_activated()) {
        if (is_cart() || is_checkout() || is_account_page() || is_ajax() ){
            return;
        }
    }
    ?><div class="entry-content saleflash menu-share-container" style="text-align:center;"><?php 
    _e('If you like this, please share: ', 'photoline-inkston');
    $current_url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $encoded_url=urlencode($current_url);
?><ul id="menu-share" class="menu-social">
<li><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo($encoded_url); ?>"></a></li>
<li><a target="_blank" href="https://twitter.com/home?status=<?php echo($encoded_url); ?>"></a></li>
<li><a target="_blank" href="https://plus.google.com/share?url=<?php echo($encoded_url); ?>"></a></li>
<li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo($encoded_url); //&title=great%20stuff%20from%20inkston&summary=Summary%20excerpt&source=inkston.com?>"></a></li>
<li><a href="mailto:?&subject=<?php echo($encoded_url); ?>&body=<?php echo($encoded_url); ?>"></a></li>
</ul> 
</div><!-- .entry-content -->
<?php
}
add_action('woocommerce_share', 'ink_sharing');
//add_action('bbp_template_after_single_topic', 'ink_sharing', 50);

/**
 * Filter coupon metadata (rules) for gift coupons created by woo-sell-coupons
 * 
 * @param array       $coupon_meta default coupon meta data
 * 
 * @return array      filtered meta data.
 */
function ink_gift_coupon_rules($coupon_meta, $id, $coupon_code){
    
    $coupon_meta['exclude_product_categories'] = array(5278, 5273);
    $coupon_val = $coupon_meta['coupon_amount'];
    /*  too clever, doesn't quite work..
    if (isWoocs()) {
        global $WOOCS;        
        $level = $WOOCS->woocs_exchange_value($level);
    }
    $coupon_val = wc_price($coupon_val);
     */
    $coupon_val .= 'USD';
    $coupon_meta['_wjecf_enqueue_message'] = sprintf(
        __('The coupon %s will give you a discount of %s when your basket value reaches %s or more.', 'photoline-inkston')
        , $coupon_code, $coupon_val, $coupon_val);
    return $coupon_meta;
}
add_filter('wcs_gift_coupon_meta', 'ink_gift_coupon_rules', 10, 3);


/**
 * apply special formatting to gift coupon including link to auto-add-coupon to basket
 * ( ?apply_coupon=coupon_code requires plugin, not implemented in woocommerce core)
 * could also add fancy formatting / additional message and QR codes
 *
 * @param string $formatted_coupon_code default formatting
 * @param string $coupon_code           raw coupon code
 * @param string $coupon_amount         raw coupon amount
 * @param string $formatted_price       formatted coupon amount
 */
function ink_format_gift_coupon($formatted_coupon_code, $coupon_code, $coupon_amount, $formatted_price){
    global $woocommerce;
    if ($woocommerce){
//        $cart_url = wc_get_cart_url();

  //      $formatted_coupon_code = sprintf(__('Click to add %s saving to your shopping basket.', 'photoline-inkston'), $formatted_price );
        $formatted_coupon_code = '<h2 style="text-align:center;"><a class="saleflash" href="' . wc_get_cart_url() . 
            '?apply_coupon=' . $coupon_code . '">'. $coupon_code . '</a></h2>';
    }
    return $formatted_coupon_code;
}
add_filter('wcs_format_gift_coupon', 'ink_format_gift_coupon', 10, 4);

/**
 * Filters the dashboard URL for a user.
 *
 * @return string magic url for mailpoet, like: 
 * ?mailpoet_page=subscriptions&mailpoet_router&endpoint=subscription&action=manage
 * &data=eyJ0b2tlbiI6IjE3ZmI2MCIsImVtYWlsIjoiaW5nbGVub0BpY2xvdWQuY29tIn0
 */
use MailPoet\Subscription\Url;
use MailPoet\Models\Subscriber;
function ink_get_newsletter_subscribe_url(){
    $managelink = '';
    $thisuser = wp_get_current_user();
    if ($thisuser) {
        global $mailpoet_plugin;
        if ($mailpoet_plugin){
            $managelink = Url::getManageUrl(Subscriber::getCurrentWPUser());
        }
    }
    return $managelink;
}

function ink_get_newsletter_subscribe_link(){
    if (shortcode_exists('mailpoet_manage_subscription')){
        echo(do_shortcode('[mailpoet_manage_subscription]'));
    } else {
        if (get_current_user_id()){
            $manageurl = ink_get_newsletter_subscribe_url();
            if ($manageurl){
                echo('<a href="' . $manageurl . '" class="manageurl">');
                _e('click here to manage your subscription', 'photoline-inkston');
                echo('</a>');
            }
        }
    }
}
add_shortcode( 'ink_get_newsletter_subscribe_link', 'ink_get_newsletter_subscribe_link');
/**
 * Filters the dashboard URL for a user.
 *
 * @since 3.1.0
 *
 * @param string $url     The complete URL including scheme and path.
 * @param int    $user_id The user ID.
 * @param string $path    Path relative to the URL. Blank string if no path is specified.
 * @param string $scheme  Scheme to give the URL context. Accepts 'http', 'https', 'login',
 *                        'login_post', 'admin', 'relative' or null.
 */
function ink_user_dashboard_url($url, $user_id, $path, $scheme){
    //woocommerce_account_edit_account();
    if ( is_woocommerce_activated() ){
        return wc_get_account_endpoint_url('edit-account');
    } elseif (function_exists('bbp_user_profile_url') ){
        return bbp_get_user_profile_url($user_id);
    }
    return $url;
}
add_filter( 'user_dashboard_url', 'ink_user_dashboard_url', 10, 4);
