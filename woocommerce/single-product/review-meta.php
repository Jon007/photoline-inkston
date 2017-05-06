<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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
	exit; // Exit if accessed directly
}

global $comment;
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta"><em><?php esc_attr_e( 'Your comment is awaiting approval', 'woocommerce' ); ?></em></p>

<?php } else { ?>

	<p class="meta">
    <strong itemprop="author"><?php 
	$url     = get_comment_author_url( $comment );
	$author  = get_comment_author( $comment );
	if ( empty( $url ) || 'http://' == $url ){
		echo($author);
  }
	else{
    echo("<a href='$url' "); 
    $site_url=get_site_url();
    if (substr($url, 0, strlen($site_url)) != $site_url){
      echo("target='inkstonlink' rel='external nofollow' ");
    }
    echo("class='url'>$author</a>");
  }
  if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && $verified ) {
    echo '<em class="verified">(' . esc_attr__( 'verified owner', 'woocommerce' ) . ')</em> ';
  }
  ?></strong>
    <span class="comment-metadata">
      <a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
        <time datetime="<?php comment_time( 'c' ); ?>">
          <?php
            /* translators: 1: comment date, 2: comment time */
            printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() );
          ?>
        </time>
      </a>
      <?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
    </span><!-- .comment-metadata -->
	</p>

<?php }
