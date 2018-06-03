<?php if ( $images->main ): ?>
    <div class="main-image"><?php echo $images->main->html; ?></div>
<?php endif; ?>

<div class="listing-details cf">
    <?php foreach ( $fields->not( 'social' ) as $field ): ?>
        <?php echo $field->html; ?>
    <?php endforeach; ?>

    <?php $social_fields = $fields->filter( 'social' ); ?>
    <?php if ( $social_fields ): ?>
    <div class="social-fields cf"><?php echo $social_fields->html; ?></div>
    <?php endif; ?>
</div>

<?php if ( $images->extra ) { ?>
<div class="extra-images">
<?php
    $imageids = Array();
    foreach ( $images->extra as $img ){
       $imageids[] = $img->id;
    }
    echo(do_shortcode( '[gallery link="file" size="medium_large" ids="' . implode( ',', $imageids) . '"]'));
}
?>
</div>
