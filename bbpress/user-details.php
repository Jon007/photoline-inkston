<?php

/**
 * User Details
 *
 * @package bbPress
 * @subpackage Theme
 */
$is_current_user = (bbp_get_displayed_user_id() == get_current_user_id());
$my_caption = ($is_current_user) ? __( 'My', 'photoline-inkston') . ' ' : '';

do_action( 'bbp_template_before_user_details' ); ?>

	<div id="bbp-single-user-details">

		<div id="bbp-user-navigation">
			<ul>
<?php
//if own user profile, include all the woocommerce profile links
if ($is_current_user){
?>
    <li><span class="bbp-user-edit-link"><a href="<?php 
        _e( '/my-account/', 'photoline-inkston');?>" title="<?php 
        _e( "Shop Account", 'photoline-inkston' ); ?>"><?php 
        _e( "Dashboard", 'woocommerce' ); ?></a></span></li>  
    <li><span class="bbp-user-edit-link"><a href="<?php 
        _e( '/my-account/orders/', 'photoline-inkston');?>" title="<?php 
        _e( "Check your order status and shipment tracking codes,", 'photoline-inkston' ); ?>"><?php 
        _e( "Orders", 'woocommerce' ); ?></a></span></li>              
    <li><span class="bbp-user-edit-link"><a href="<?php 
        _e( '/my-account/edit-address/', 'photoline-inkston');?>" title="<?php 
        _e( "Edit shipping/billing addresses for inkston shop", 'photoline-inkston' ); ?>"><?php 
        _e( "Addresses", 'woocommerce' ); ?></a></span></li>  
    <li><span class="bbp-user-edit-link"><a href="<?php 
        _e( '/my-account/edit-account/', 'photoline-inkston');?>" title="<?php 
        _e( "Update account name/email address/password", 'photoline-inkston' ); ?>"><?php 
        _e( "Account details", 'woocommerce' ); ?></a></span></li>  
    <li><span class="bbp-user-edit-link"><a href="<?php 
        _e( '/my-account/customer-logout/', 'photoline-inkston');?>" title="<?php 
        _e( "Log out from inkston.com", 'photoline-inkston' ); ?>"><?php 
        _e( "Logout", 'woocommerce' ); ?></a></span></li>  
<?php
} ?><li class="<?php if ( bbp_is_single_user_profile() ) :?>current<?php endif; ?>"><span class="vcard bbp-user-profile-link">
        <a class="url fn n <?php 
        if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) { echo( ' inline');}?>" href="<?php 
            bbp_user_profile_url(); ?>" title="<?php 
            if ($is_current_user){
                _e( 'My Profile', 'photoline-inkston');
            } else {
                printf( esc_attr__( "%s's Profile", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); 
            } ?>" rel="me"><?php 
            _e( 'Profile', 'bbpress' ); ?></a></span>
<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) { ?>
        <span class="bbp-user-edit-link"> (<a class="inline" href="<?php 
            bbp_user_profile_edit_url(); ?>" title="<?php 
            printf( esc_attr__( "Edit %s's Profile", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php 
            _e( 'Edit', 'bbpress' ); ?></a>)</span>    
    </li>
    <li><span class="bbp-user-edit-link"></span><a href="/community/my-awards/" title="<?php 
    _e( "Check your points in inkston rewards scheme", 'photoline-inkston' ); ?>"><?php 
    _e( "My Rewards", 'photoline-inkston' ); ?></a></span></li>  
<?php }
$listings_link = ($is_current_user) ? ( network_site_url() . 'community/my-listings' )
    : get_author_posts_url(bbp_get_displayed_user_id()) ;
?><li class=""><span class="vcard user-posts"><a class="url fn n" href="<?php 
    echo(esc_url( $listings_link )); ?>" title="<?php 
    printf( esc_attr__( "%s's Posts", 'photoline-inkston' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>" rel="me"><?php 
    echo $my_caption;
    _e( 'Artist Listings', 'photoline-inkston' ); ?></a></span></li>

<?php 
  $page_id = inkGetPageID( 'comments');  // get Comments page in the current language
  if ($page_id){
    $comment_link = get_page_link($page_id) . '?u=' . bbp_get_displayed_user_id(); 
    $comment_title = get_the_title( $page_id );    
 ?>
<li class=""><span class="vcard user-comments"><a class="url fn n" href="<?php 
    echo($comment_link);?>" title="<?php 
    printf( esc_attr__( "%s's Comments", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>" rel="me"><?php 
    echo $my_caption;
    _e( 'Commments'); ?></a></span></li>
<?php } ?>
                
                
<li class="<?php if ( bbp_is_single_user_topics() ) :?>current<?php endif; ?>"><span class='bbp-user-topics-created-link'><a href="<?php 
    bbp_user_topics_created_url(); ?>" title="<?php 
    printf( esc_attr__( "%s's Topics Started", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php 
    echo $my_caption;
    _e( 'Forum Topics', 'photoline-inkston' ); ?></a></span></li>

<li class="<?php if ( bbp_is_single_user_replies() ) :?>current<?php endif; ?>"><span class='bbp-user-replies-created-link'><a href="<?php 
    bbp_user_replies_created_url(); ?>" title="<?php 
    printf( esc_attr__( "%s's Replies Created", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php 
    echo $my_caption;
    _e( 'Forum Replies', 'photoline-inkston' ); ?></a>
    </span>
</li>

<?php if ( bbp_is_favorites_active() ) : ?><li class="<?php 
    if ( bbp_is_favorites() ) :?>current<?php endif; ?>"><span class="bbp-user-favorites-link"><a href="<?php 
            bbp_favorites_permalink(); ?>" title="<?php 
            printf( esc_attr__( "%s's Favorites", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php 
            echo $my_caption;
            _e( 'Favorite Forum Posts', 'photoline-inkston' ); ?></a></span></li>
<?php endif; ?>

<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>
    <?php if ( bbp_is_subscriptions_active() ) : ?><li class="<?php 
        if ( bbp_is_subscriptions() ) :?>current<?php endif; ?>"><span class="bbp-user-subscriptions-link"><a href="<?php 
        bbp_subscriptions_permalink(); ?>" title="<?php 
        printf( esc_attr__( "%s's Subscriptions", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php 
        _e( 'My Forum Subscriptions', 'photoline-inkston' ); ?></a></span></li>
    <?php endif; ?>
<?php endif; ?>

			</ul>
		</div><!-- #bbp-user-navigation -->
	</div><!-- #bbp-single-user-details -->
    
	<?php do_action( 'bbp_template_after_user_details' ); 
    