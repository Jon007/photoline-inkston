<?php
/**
 * The template for displaying search forms
 *
 * @package Inkston
 * note, could use pll function for home_url but polylang already hooks filter 'home_url' 
 */
//$home_page = '';
//if function_exists(){
//  
//}else{
//  $home_page
//}
?>
<form role="search" method="get" id="search-form" class="search-form" action="<?php echo esc_url( home_url() ); ?>/index.php">
	<label>
		<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'photoline-inkston' ); ?></span>
		<input type="search" class="search-field" id="search-field" placeholder="<?php _e( 'Search', 'photoline-inkston' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'photoline-inkston' ); ?>">
</form>

