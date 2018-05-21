<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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
            //$page = get_page($page);
            //if (! $page){return false;}
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
    } else {
        $pageobj = get_page($page);
        if (!$pageobj){
            return false;
        }
    }
    return $page; // returns the link
}

function inkGetPageURL($post, $blogid){
    //switch blog if necessary
    $currentblogid = get_current_blog_id();
    if ($currentblogid != $blogid){switch_to_blog($blogid);}

    $post = inkGetPageID($post);
    $url = ($post) ? get_page_link($post) : '';
    
    //restore blog if necessary
    if ($currentblogid != $blogid){restore_current_blog();}    
    
    return $url;
}

/*
 * Add redirect fields to login/registration forms
 */
function ink_redirect_field()
{
    $referer = '';
    if (isset($_POST['redirect'])) {
        $referer = $_POST['redirect'];
    }elseif (isset($_REQUEST['redirect'])) {
        $referer = $_REQUEST['redirect'];
    }elseif (isset($_REQUEST['redirect_to'])) {
        $referer = $_REQUEST['redirect_to'];
    } 
//    if ($referer == ''){
//        $referer = wp_get_raw_referer();
//    }

    ?><input type="hidden" name="redirect" value="<?php
    echo ($referer);

    ?>" /><?php
}
add_action('woocommerce_login_form_end', 'ink_redirect_field');
add_action('woocommerce_register_form_end', 'ink_redirect_field');

/*
 * Allow redirect to previous page after registration
 * @param string $redirect     this is the registration screen itself.
 * @param string $account_page ie My Account.
 * 
 */
function ink_redirect_registration($referer)
{
    if (isset($_POST['redirect'])) {
        $referer = $_POST['redirect'];
    }
    if (isset($_REQUEST['redirect'])) {
        $referer = $_REQUEST['redirect'];
    }
    return $referer;
}
add_filter('woocommerce_registration_redirect', 'ink_redirect_registration', 10, 1);
/*
 * rewrite the standard login url to use the main woo account form..
 * @param string $redirect     Path to redirect to on log in.
 * @param bool   $force_reauth Whether to force reauthorization
 * @return string The login URL. Not HTML-encoded.
 */
function ink_login_url($login_url, $redirect, $force_reauth)
{

    if (isset($_POST['redirect'])) {
        $referer = $_POST['redirect'];
    }    
//    if (! $redirect || $redirect==''){
//        $redirect = wp_get_raw_referer();
//    }
//    if (!$redirect) {
//        $redirect = html_entity_decode("https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
//   }

    //get the link for inkston
    $ink_login_uri = '';
    if (is_woocommerce_activated()) {
        $ink_login_uri = wc_get_page_permalink('myaccount');
    } else {
        $locale = get_locale();
        switch ($locale) {
            case 'fr_FR':
                $ink_login_uri = network_site_url('fr/mon-compte/');
                break;
            case 'es_ES':
                $ink_login_uri = network_site_url('es/mi-cuenta/');
                break;
            default:
                $ink_login_uri = network_site_url('my-account/');
        }
    }
    //if we have a new link, recompose the parameters
    if ($ink_login_uri != '') {
        $login_url = $ink_login_uri;
        if ($redirect){
            $login_url = add_query_arg('redirect', urlencode($redirect), $login_url);
        }
        if ($force_reauth) {
            $login_url = add_query_arg('reauth', '1', $login_url);
        }
    }
    return $login_url;
}
add_filter('login_url', 'ink_login_url', 10, 3);
