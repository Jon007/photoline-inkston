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
        <span class="search-label"><?php
            $query = get_search_query();
            if (have_posts()){
                _e('Showing results for:', 'photoline-inkston');
            } else {
                _e('Search for:', 'photoline-inkston');                
            }
        ?></span>
		<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'photoline-inkston' ); ?></span>
		<input type="search" class="search-field" id="search-field" placeholder="<?php _e( 'Search', 'photoline-inkston' ); ?>" value="<?php echo esc_attr( $query ); ?>" name="s"><?php
            if (is_multisite() ){
                if (is_main_site()){
                    ?><span class="search-label"><?php
                        printf( 
                            __(" on inkston.com.  You can also <a href='https://www.inkston.com/community/?s=%s'>repeat the search on community site</a>.", 
                                'photoline-inkston'),
                            $query);
                    ?></span><?php
                } else {
                    ?><span class="search-label"><?php
                        printf( 
                            __("on inkston community.  You can also <a href='https://www.inkston.com/?s=%s'>repeat the search on the main site</a>.", 
                                'photoline-inkston'),
                            $query);
                    ?></span><?php
                }
            }
        ?>
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'photoline-inkston' ); ?>">
</form>

