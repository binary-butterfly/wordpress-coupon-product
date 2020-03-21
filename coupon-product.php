<?php
/*
Plugin Name: Coupon Product for WooCommerce
Plugin URI: https://github.com/binary-butterfly/wordpress-coupon-product
Description: Fügt ein Produkt hinzu, was in ein Coupon umgewandelt wird. Mit QR-Code Support!
Version: 0.2.0
WC requires at least: 3.0.0
WC tested up to: 4.0.1
Author: binary butterfly GmbH
Author URI: https://binary-butterfly.de
Text Domain: coupon-product
License: MIT
Domain Path: /languages
*/

// Thanks to https://github.com/MarieComet/wcs-sell-coupons for some great inspirations


defined('ABSPATH') or die('No script kiddies please!');

define( 'COUPON_PRODUCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'COUPON_PRODUCT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


require 'vendor/autoload.php';
require 'inc/checkout.php';
require 'inc/product.php';
require 'inc/coupon-api.php';


register_activation_hook( __FILE__, function() {
    if ( ! get_term_by( 'slug', 'coupon_product', 'product_type' ) ) {
        wp_insert_term( 'coupon_product', 'product_type' );
    }
});
