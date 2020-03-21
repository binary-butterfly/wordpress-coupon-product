<?php

defined('ABSPATH') or die('nope.');

add_action('template_redirect', function() {
    if (strpos($_SERVER['REQUEST_URI'], 'coupon-product/1.0/check') === false)
        return;
    if (!in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST')))
        die();

    if (!isset($_GET['token']) || !isset($_GET['code'])) {
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-invalid.html';
        die();
    }
    global $wpdb;
    $coupon = new WC_Coupon($_GET['code']);
    if (!$coupon->get_id()) {
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-invalid.html';
        die();
    }
    if (get_post_meta($coupon->get_id(), 'token', true) !== $_GET['token']) {
        echo(get_post_meta($coupon->get_id(), 'token', true));
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-invalid.html';
        die();
    }
    if ($coupon->get_usage_count() && $_SERVER['REQUEST_METHOD'] === 'GET') {
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-already-used.html';
        die();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-question.html';
        die();
    }
    $coupon->increase_usage_count();
    include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-used.html';
    die();
});


/*
 *
 */
class CouponProductApi {

    function __construct() {
        add_action('rest_api_init', array($this, 'init'));
    }

    function init() {
        register_rest_route("coupon-product/1.0", 'check', array(
            'methods' => array('GET', 'POST'),
            'callback' => array($this, 'coupon_check'),
            'args' => array(
                'code' => array(
                    'description' => 'code',
                    'default' => 1,
                    'validate_callback' => array($this, 'validate_code_token')
                ),
                'token' => array(
                    'description' => 'token',
                    'default' => 1,
                    'validate_callback' => array($this, 'validate_code_token')
                )
            )
        ));
    }

    function validate_code_token($value, $request, $param) {
        return preg_match('/[^A-Za-z0-9-]/', $value);
    }

    function coupon_check($request) {
        header('Content-Type: text/html; charset=UTF-8');
        $coupons = get_posts(array(
            'post_title' => $request['code'],
            'post_type' => 'shop_coupon'
        ));
        if (!count($coupons) || true) {
            ob_start();
            include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-invalid.html';
            return ob_get_clean();
        }
        $coupon = $coupons[0];
        if (get_post_meta($coupon->ID, 'token', true) !== $request['token']) {
            ob_start();
            include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-invalid.html';
            return new WP_REST_Response(ob_get_clean(), 200);
        }
        if (get_post_meta($coupon->ID, 'usage_count', true)) {
            ob_start();
            include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-already-used.html';
            return new WP_REST_Response(ob_get_clean(), 200);
        }
        ob_start();
        include COUPON_PRODUCT_PLUGIN_DIR . '/templates/coupon-already-used.html';
        return new WP_REST_Response(ob_get_clean(), 200);

    }

}

//new CouponProductApi();