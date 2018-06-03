<?php
/**
Template Name: commentspage
*
* @package Inkston
*/

get_header();
// currently ignoring if ( !is_front_page() ) :  treat as the same, to get special front page use full width or posts
?>
	<div id="primary" class="content-area<?php if ( !is_active_sidebar( 'sidebar-2' ) ) { ?> no-sidebar<?php } ?>">
		<main id="main" class="site-main" role="main"><?php
      $user_id = (isset($_GET['u'])) ? $_GET['u'] : '';
      if (!is_numeric ($user_id)){
        if ( is_user_logged_in() ) {
          $user_id = get_current_user_id();
        }
      }
      $args = array(
          'status' => 'approve',
          'order' =>  'DESC',
          'user_id' => $user_id
      );
      $comments = get_comments($args);
      wp_list_comments(array( 'short_ping'        => true,), $comments);
      /*
      foreach($comments as $comment) {
          echo '<p>'; 
          echo($comment->comment_author . '<br />' . $comment->comment_content);
          echo '</p>';
      }
       * 
       */

		?></main><!-- #main -->
	</div><!-- #primary -->

	<?php get_sidebar();
	/* NOTE: this theme is not currently tested for re-enabling sidebars ( is_active_sidebar( 'sidebar-2' ) ) { get_sidebar(); } */
	?>

<?php get_footer(); ?>
