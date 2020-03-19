<?php

defined('ABSPATH') or die('No script kiddies please!');

/*
 * generate coupon after completing order
 */
add_action( 'woocommerce_order_status_completed', function ($order_id, $order) {
    foreach( $order->get_items() as $order_item_id => $order_item ) {
        $product = $order_item->get_product();
        if ($product->get_type() !== 'coupon_product')
            continue;
        $amount = $product->get_regular_price( 'edit' );

        $coupon_id = wc_get_order_item_meta( $order_item_id, '_coupon_id' );
        if ($coupon_id && get_post($coupon_id))
            continue;

        $coupon_code = strtolower( 'cp-' . wp_generate_password( 15, false ));

        $new_coupon_id = wp_insert_post(array(
            'post_title'   => $coupon_code,
            'post_content' => '',
            'post_excerpt' => 'Gutschein',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'shop_coupon',
        ));
        $coupon_meta = array(
            'discount_type' => 'fixed_cart',
            'coupon_amount' => $amount ,
            'individual_use' => 'yes' ,
            'usage_limit' => '1' ,
            'usage_limit_per_user' => '0',
            'limit_usage_to_x_items' => '0',
            'usage_count' => '0',
            'free_shipping' => 'no',
            'exclude_sale_items' => 'no',
            'is_voucher' => 'yes'
        );

        foreach ( $coupon_meta as $meta_key => $meta_value ) {
            update_post_meta( $new_coupon_id, $meta_key, $meta_value );
        }

        wc_add_order_item_meta( $order_item_id, '_coupon_id', $new_coupon_id, true );
        wc_add_order_item_meta( $order_item_id, '_coupon_code', $coupon_code, true );
    }
}, 1, 2);


/*
 * show coupons before mail table
 */
add_action('woocommerce_email_before_order_table', function ($order, $sent_to_admin, $plain_text, $email) {
    if ($sent_to_admin)
        return;
    if ($email->id !== 'customer_completed_order')
        return;
    $order_items =  $order->get_items();
    $codes = array();
    foreach( $order_items as $order_item_id => $order_item ) {
        if ($order_item->get_product()->get_type() !== 'coupon_product')
            continue;
        $codes[] = wc_get_order_item_meta($order_item_id, '_coupon_code', true);
    }
    if (!count($codes))
        return;
    ?>
    <p>Du hast in Deiner E-Mail Gutscheine gekauft. Danke! Hier sind die Gutscheincodes dazu:</p>
    <ul>
        <?php foreach ($codes as $code): ?>
        <li><?php echo($code); ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Du kannst die Gutscheincodes später hier im Online-Shop oder vor Ort einlösen.</p>
    <?php
}, 10, 4);


/*
 * show coupon
 */
add_filter('woocommerce_order_item_get_formatted_meta_data', function($formatted_meta) {
    $coupon_code = null;
    foreach ( $formatted_meta as $key => $meta ) {
        if ($meta->key === '_coupon_code') {
            $coupon_code = $meta->value;
            unset($formatted_meta[$key]);
        }
    }
    if (!$coupon_code)
        return $formatted_meta;
    foreach ( $formatted_meta as $key => $meta ) {
        if ($meta->key === '_coupon_id') {
            $formatted_meta[$key]->display_key = 'Coupon';
            $formatted_meta[$key]->display_value = '<a href="' . get_edit_post_link($meta->value) . '">' . $coupon_code . '</a>';
        }
    }
    return $formatted_meta;
}, 10, 1);