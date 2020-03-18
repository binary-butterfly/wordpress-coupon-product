<?php

defined('ABSPATH') or die('No script kiddies please!');

/*
 * include discount product class after woocommerce loaded
 */
add_action( 'woocommerce_loaded', function() {
    require_once 'product.class.php';
});

/**
 * add coupon product to product type select
 */
add_filter( 'product_type_selector', function($types) {
    $types[ 'coupon_product' ] = __( 'Coupon', 'coupon-product');
    return $types;
}, 10, 1);


/**
 * hide attributes
 */
add_filter( 'woocommerce_product_data_tabs', function($tabs) {
    $tabs['attribute']['class'][] = 'hide_if_coupon_product';
    $tabs['shipping']['class'][]  = 'hide_if_coupon_product';
    $tabs['inventory']['class'][] = 'show_if_coupon_product';
    return $tabs;
}, 10, 1);


/*
 * use simple product template for discount product
 */
add_action('woocommerce_coupon_product_add_to_cart', function () {
    wc_get_template( 'single-product/add-to-cart/simple.php' );
});

/**
 * show pricing fields for coupon_product product.
 */
add_action( 'admin_footer', function() {
    if ( 'product' !== get_post_type() )
        return;
    ?>
    <script type='text/javascript'>
        jQuery( '.options_group.pricing' ).addClass( 'show_if_coupon_product' );
        jQuery( '.show_if_simple' ).addClass( 'show_if_coupon_product' );
    </script>
    <?php
});
