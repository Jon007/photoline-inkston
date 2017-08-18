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
        <?php } ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>