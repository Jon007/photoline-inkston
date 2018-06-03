<?php
if (is_woocommerce_activated()) {

/*
 * Return inkston no image - currently uses theme directory so is theme specific..
 * 
 * @param string $noimage   image passed by woocommerce
 */
function inkston_noimage($noimage)
{
    return get_template_directory_uri() . '/img/no-image.png';
}
add_filter( 'woocommerce_placeholder_img_src', 'inkston_noimage');

}
