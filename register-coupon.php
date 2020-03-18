<?php
/*
Plugin Name: Coupon Product
Plugin URI: https://binary-butterfly.de
Description: Fügt ein Produkt hinzu, was in ein Coupon umgewandelt wird.
Version: 0.1.0
WC requires at least: 3.0.0
WC tested up to: 3.6.2
Author: binary butterfly GmbH
Author URI: https://binary-butterfly.de
Text Domain: coupon-product
License: GPLv2 or later
Domain Path: /languages
*/

// Thanks to https://github.com/MarieComet/wcs-sell-coupons for some great inspirations


defined('ABSPATH') or die('No script kiddies please!');

define( 'COUPON_PRODUCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require 'inc/checkout.php';
require 'inc/product.php';

register_activation_hook( __FILE__, function() {
    if ( ! get_term_by( 'slug', 'coupon_product', 'product_type' ) ) {
        wp_insert_term( 'coupon_product', 'product_type' );
    }
});
