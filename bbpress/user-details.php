<?php

/**
 * User Details
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php do_action( 'bbp_template_before_user_details' ); ?>

	<div id="bbp-single-user-details">
		<div id="bbp-user-avatar">

			<span class='vcard'>
				<a class="url fn n" href="<?php bbp_user_profile_url(); ?>" title="<?php bbp_displayed_user_field( 'display_name' ); ?>" rel="me">
					<?php echo get_avatar( bbp_get_displayed_user_field( 'user_email', 'raw' ), apply_filters( 'bbp_single_user_details_avatar_size', 150 ) ); ?>
				</a>
			</span>

		</div><!-- #author-avatar -->

		<div id="bbp-user-navigation">
			<ul>
				<li class="<?php if ( bbp_is_single_user_profile() ) :?>current<?php endif; ?>">
					<span class="vcard bbp-user-profile-link">
						<a class="url fn n" href="<?php bbp_user_profile_url(); ?>" title="<?php printf( esc_attr__( "%s's Profile", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>" rel="me"><?php _e( 'Profile', 'bbpress' ); ?></a>
					</span>
				</li>

                <li class="">
					<span class="vcard user-posts">
						<a class="url fn n" href="<?php echo(esc_url( get_author_posts_url(bbp_get_user_id()))); ?>" title="<?php printf( esc_attr__( "%s's Posts", 'photoline-inkston' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>" rel="me"><?php _e( 'Articles', 'photoline-inkston' ); ?></a>
					</span>
				</li>

<?php 
  $page_id = inkGetPageID('comments');  // get Comments page in the current language
  if ($page_id){
    $comment_link = get_page_link($page_id) . '?u=' . bbp_get_user_id(); 
    $comment_title = get_the_title( $page_id );
    
 ?>
                <li class="">
					<span class="vcard user-comments">
						<a class="url fn n" href="<?php echo($comment_link);?>" title="<?php printf( esc_attr__( "%s's Comments", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>" rel="me"><?php _e( 'Commments'); ?></a>
					</span>
				</li>
<?php } ?>
                
                
				<li class="<?php if ( bbp_is_single_user_topics() ) :?>current<?php endif; ?>">
					<span class='bbp-user-topics-created-link'>
						<a href="<?php bbp_user_topics_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Topics Started", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Topics Started', 'bbpress' ); ?></a>
					</span>
				</li>

				<li class="<?php if ( bbp_is_single_user_replies() ) :?>current<?php endif; ?>">
					<span class='bbp-user-replies-created-link'>
						<a href="<?php bbp_user_replies_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Replies Created", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Replies Created', 'bbpress' ); ?></a>
					</span>
				</li>

				<?php if ( bbp_is_favorites_active() ) : ?>
					<li class="<?php if ( bbp_is_favorites() ) :?>current<?php endif; ?>">
						<span class="bbp-user-favorites-link">
							<a href="<?php bbp_favorites_permalink(); ?>" title="<?php printf( esc_attr__( "%s's Favorites", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Favorites', 'bbpress' ); ?></a>
						</span>
					</li>
				<?php endif; ?>

				<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>

					<?php if ( bbp_is_subscriptions_active() ) : ?>
						<li class="<?php if ( bbp_is_subscriptions() ) :?>current<?php endif; ?>">
							<span class="bbp-user-subscriptions-link">
								<a href="<?php bbp_subscriptions_permalink(); ?>" title="<?php printf( esc_attr__( "%s's Subscriptions", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Subscriptions', 'bbpress' ); ?></a>
							</span>
						</li>
					<?php endif; ?>

					<li class="<?php if ( bbp_is_single_user_edit() ) :?>current<?php endif; ?>">
						<span class="bbp-user-edit-link">
							<a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php printf( esc_attr__( "Edit %s's Profile", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Edit', 'bbpress' ); ?></a>
						</span>
					</li>

					<li>
						<span class="bbp-user-edit-link">
							<a href="<?php 
                            if (is_woocommerce_activated()){
                                echo get_permalink( get_option('woocommerce_myaccount_page_id') ); 
                            } else {
                                echo __('/my-account/', 'photoline-inkston');
                            }
                            ?>" title="<?php _e( "Shop Account", 'woocommerce' ); ?>"><?php _e( "My Account", 'woocommerce' ); ?></a>
						</span>
					</li>
                    
				<?php endif; ?>

			</ul>
		</div><!-- #bbp-user-navigation -->
	</div><!-- #bbp-single-user-details -->

	<?php do_action( 'bbp_template_after_user_details' ); ?>
