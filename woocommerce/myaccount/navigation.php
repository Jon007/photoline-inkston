<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) { ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
        <?php } 
        $profileurl = false;
        if (function_exists('bbp_get_user_profile_url')) {
            $profileurl = esc_url( bbp_get_user_profile_url(get_current_user_id()));
        } else {
            $user     = get_userdata( get_current_user_id() );
            $nicename = $user->user_nicename;
            $profileurl = '/community/forums/users/' . $nicename;
        }
        if ($profileurl){
        ?><li class="">
<a href="<?php echo($profileurl); ?>"><?php echo(esc_html( __('My Profile', 'photoline-inkston') ));?></a>
			</li>
<?php } 
global $wp_subscribe_reloaded;
if ($wp_subscribe_reloaded ){
    $page_id = inkGetPageID('comment-subscriptions');  // get Comments page in the current language
    if ($page_id){
        $manager_link = get_page_link($page_id); 
        $current_user = wp_get_current_user();
        $current_user_email = $current_user->data->user_email;
        $subscriber_salt = $wp_subscribe_reloaded->stcr->utils->generate_temp_key( $current_user_email );

        $manager_link .= "?srek=" . $wp_subscribe_reloaded->stcr->utils->get_subscriber_key($current_user_email) . "&srk=$subscriber_salt&amp;srsrc=e&post_permalink=";
        if ($manager_link){
        ?><li class=""><a href="<?php echo($manager_link); ?>"><?php echo(esc_html( 
            __('Comment subscriptions', 'photoline-inkston') ));?></a></li><?php 
        }
    }
}
if (function_exists('wysija_get_subscriptions_edit_link')){
    $mailpoet_link = wysija_get_subscriptions_edit_link();
    if ($mailpoet_link) {
        /**
         * show mailpoet link
         */
        ?><li class=""><a href="<?php echo($mailpoet_link); ?>"><?php echo(esc_html( 
            __('Newsletter subscriptions', 'photoline-inkston') ));?></a></li><?php 
    } 
}
$page_id = inkGetPageID('comments');  // get Comments page in the current language
if ($page_id){
  $comment_link = get_page_link($page_id) . '?u=' . get_current_user_id(); 
  $comment_title = get_the_title( $page_id );    
?><li class="">
    <span class="vcard user-comments">
        <a class="url fn n" href="<?php echo($comment_link);?>" title="<?php 
            esc_attr__( "My comments and reviews", 'photoline-inkston'); 
            ?>" rel="me"><?php _e("My Comments", 'photoline-inkston'); ?></a>
    </span>
</li>
        <?php } ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>