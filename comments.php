<?php
/**
 * The template for displaying Comments.
 * @package Inkston
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
<?php _e('Comments: ', 'photoline-inkston'); ?> <?php comments_number(__('0', 'photoline-inkston'), __('1', 'photoline-inkston'), __('%', 'photoline-inkston') );?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'photoline-inkston' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'photoline-inkston' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'photoline-inkston' ) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				wp_list_comments(array( 'avatar_size' => 80));
				//wp_list_comments( array( 'callback' => 'inkston_comment' ) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="comment-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'photoline-inkston' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'photoline-inkston' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'photoline-inkston' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments Closed', 'photoline-inkston' ); ?></p>
	<?php endif; ?>
<?php
  $page_id = inkGetPageID(2756);  // get Contact page in the current language
    if ($page_id){
      $contact_link = get_page_link($page_id); 
      $contact_title = get_the_title( $page_id );
    } elseif (get_locale()=='de_DE') {
        $contact_link = __( 'https://www.inkston.com/deu/kontaktieren-sie-uns/', 'photoline-inkston' ); 
        $contact_title = __( 'Contact', 'photoline-inkston' ); 
    } else {
        $contact_link = __( 'https://www.inkston.com/inkston/contact-us/', 'photoline-inkston' ); 
        $contact_title = __( 'Contact', 'photoline-inkston' ); 
    }
    $comment_title = '<a href="#comment" ' . 
        //    'id="comment" ' . 
        'name="comment">' . 
        __( 'Leave a Comment', 'photoline-inkston' ) . '</a> ' . 
           __(' or ', 'photoline-inkston' ) . 
          ' <a href="' . $contact_link . '" onclick="javascript:return true;">' . $contact_title . '</a>';
	$args = array(
		//'title_reply' => __( 'Leave a Comment or <a href="https://www.inkston.com/inkston/contact-us/">Contact Us</a>', 'photoline-inkston' ),
    'title_reply' => $comment_title, 
		'label_submit' => __( 'Post Comment', 'photoline-inkston' )
	);
	comment_form( $args );
?>

</div><!-- #comments -->
