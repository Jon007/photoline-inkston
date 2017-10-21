<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms_page_id = wc_get_page_id( 'terms' );

if ( $terms_page_id > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) :
	$terms         = get_post( $terms_page_id );
	$terms_content = has_shortcode( $terms->post_content, 'woocommerce_checkout' ) ? '' : wc_format_content( $terms->post_content );

	if ( $terms_content ) {
		do_action( 'woocommerce_checkout_before_terms_and_conditions' );
		echo '<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . $terms_content . '</div>';
	}
	?>
	<p class="form-row terms wc-terms-and-conditions">
		<label class="woocommerce-form__label">
			<span><?php printf( __( 'By placing your order, you agree to the inkston <a href="%s" target="_blank" class="woocommerce-terms-and-conditions-link">terms &amp; conditions</a>', 'photoline-inkston' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></span>  
		</label>
		<input type="hidden" name="terms" value="1" />
		<input type="hidden" name="terms-field" value="0" />
	</p>
	<?php do_action( 'woocommerce_checkout_after_terms_and_conditions' ); ?>
<?php endif; ?>