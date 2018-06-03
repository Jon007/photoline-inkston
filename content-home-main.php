<?php
/**
 * @package Inkston
 *
 * content-home-main.php : unused - instead uses template full page
 */
?>
<!--content-home-main-->


<?php if ( '' != get_the_content() ) : ?>

<section id="home-content">
<div class="entry-content">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	if ( false === get_theme_mod( 'inkston_display_pagetitle' ) ) {
?>
		<!-- title now in header <h1 class="page-title"><?php the_title(); ?></h1> -->
<?php
	}
?>
  <h1 class="page-title entry-title"><?php echo(inkston_title()); ?></h1>
    
				<?php the_content(); ?>
				<?php edit_post_link( __( 'Edit', 'photoline-inkston' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
	</article><!-- #post -->
</div><!-- .entry-content -->
</section><!-- #home-content -->

<?php endif; ?>

<?php if ( is_active_sidebar( 'home-page-section' ) ) { ?>
	<section id="home-page-section">
		<div class="entry-content">
			<div class="grid<?php $sidebars_widgets = wp_get_sidebars_widgets(); echo count($sidebars_widgets['home-page-section']); ?> clearfix">
			<?php dynamic_sidebar( 'home-page-section' ); ?>
			</div>
		</div><!-- .entry-content -->
	</section>
<?php } ?>


 <!-- Sticky Post-->
<?php
	global $post;
        		$args = array(
            			'post__in'  => get_option( 'sticky_posts' )
        		);
	$sticky_posts = get_posts($args);
?>
<?php
//sticky posts number for left-right align
if( get_option( 'sticky_posts' ) ) {
	global $count;
?>
<section id="home-sticky">
	<div class="home-section">
<?php
	foreach($sticky_posts as $post) : setup_postdata($post);
	$count ++; // left-right align
?>
		<div class="entry-content">
			<div class="half"<?php if ( $count == 2 ) : echo 'style="float: right;"'; else : echo 'style="margin-left: 0;"'; endif; ?>>
				<div class="post-thumb" style="background: #FFF url(<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' ); echo $thumbnail[0]; ?>) no-repeat; background-position: 50%; background-size: 100%;">
				</div>
			</div><!-- .half -->

        			<div class="half"<?php if ( $count == 2 ) : echo 'style="margin-left: 0;"'; else : echo 'style="float: right;"'; endif; ?>>
           			<h1 class="page-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php the_excerpt(); ?>
        			</div><!-- .half -->
	</div><!-- .entry-content -->

<?php if ( $count == 2 ) : $count = 0; endif; ?>

<div class="clearfix"></div>

<?php
	endforeach;
	wp_reset_postdata();
?>
	</div><!-- .home-section -->
</section><!-- /sticky -->
<?php } // get_option( 'sticky_posts' ) ?>

<?php
	if ( get_theme_mod( 'inkston_category_set') != '' ) {
?>

<section id="home-recent-posts">
	<div class="home-section">
<?php
	$catID = get_theme_mod( 'inkston_category_set');
    	$limit_num = get_theme_mod( 'number_homeposts');
    	$recent_posts_title = get_theme_mod( 'number_homeposts');

$args = array(
	'posts_per_page' => $limit_num,
	'post__not_in' => get_option( 'sticky_posts'),
	'cat' => $catID,
	'post_status' => 'publish'
);
	$q = new WP_Query( $args );

	if( $q->have_posts() ):
?>
<h3 class="recent-posts-title"><?php echo esc_html( get_theme_mod( 'inkston_recentposts_title', 'Recent Posts' ) ); ?></h3>
			<div class="home-recent-posts clearfix">
<?php
	while( $q->have_posts() ): $q->the_post(); ?>

			<div class="col">
				<?php get_template_part( 'content', 'home-post' ); ?>
			</div><!-- .col -->
<?php
	endwhile; ?>
			</div><!-- .recent-posts-->
<?php
		endif;
		wp_reset_postdata();
?>
	</div><!-- .home-section -->
</section><!-- home-recent-posts -->

<?php } // if ( false === get_theme_mod( 'inkston_recent_posts') ) ?>