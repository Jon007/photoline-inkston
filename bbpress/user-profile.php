<?php

/**
 * User Profile
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_user_profile' ); ?>

<div id="bbp-user-profile" class="bbp-user-profile">
    <div class="bbp-user-section">
        <h2 class="section-title"><?php printf( esc_attr__( "%s's Profile", 'photoline-inkston' ), bbp_get_displayed_user_field( 'display_name' ) ); ?></h2>
<?php
    $user_url = bbp_get_displayed_user_field( 'user_url' );
    if ($user_url){
        echo ('<div class="ink-profile-link">');
        echo ( '<a href="' . esc_attr($user_url) . '">' . $user_url . '</a>');
        echo ("</p>");
    }
?>
<div class="site-main">
<div class="bbp-user-description"><?php 
if ( bbp_get_displayed_user_field( 'description' ) ){
    bbp_displayed_user_field( 'description' ); 
} else {
    if ( bbp_get_displayed_user_id() != get_current_user_id() ){
        _e('This user has not yet edited their profile.', 'photoline-inkston');
    } else {
        ?><a href="<?php bbp_user_profile_edit_url(); ?>"><?php _e( 'Edit your profile', 'photoline-inkston' ); ?></a> <?php
        _e('to add content to this section.', 'photoline-inkston');
    }
}
?></div>
</div>
&nbsp;    
<h2 class="section-title"><?php _e( 'Forum Contributions', 'photoline-inkston' ); ?></h2>
<div class="user-stats">
<span class="bbp-user-topic-count"><a href="<?php bbp_user_topics_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Topics Started", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php  printf( __( 'Topics Started: %s',  'bbpress' ), bbp_get_user_topic_count_raw() ); ?></a></span><br />
<span class="bbp-user-reply-count"><a href="<?php bbp_user_replies_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Replies Created", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php printf( __( 'Replies Created: %s', 'bbpress' ), bbp_get_user_reply_count_raw() ); ?></a></span>
</div>


<?php 
if ( (bbp_get_displayed_user_id() == get_current_user_id() ) || 
     ( function_exists('badgeos_get_manager_capability') && current_user_can( badgeos_get_manager_capability() ) ) ) {    
?>
&nbsp;
<hr />
<h2 class="section-title"><?php _e( 'Contributor badges', 'photoline-inkston' ); ?></h2>
<p><?php 
    echo (get_user_level(array('style'=>'score')));
//printf( __( 'Current score: %1$s points.', 'photoline-inkston' ), get_user_points() );
?></p>
<p><?php _e( 'Points are awarded for making community contributions, as you earn more points you can win badges which get you prize coupons redeemable at the shop.  <a href="https://www.inkston.com/community/my-awards/">Click here</a> to see the levels and points scheme.', 'photoline-inkston' ); ?></p>
<?php  
ink_bp_member_achievements_content(); 
     }
?>
    </div>
</div><!-- #bbp-author-topics-started -->
<?php 
do_action( 'bbp_template_after_user_profile' ); 
?>
