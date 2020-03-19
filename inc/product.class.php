<?php

defined('ABSPATH') or die('No script kiddies please!');

class WC_Product_Coupon_Product extends WC_Product_Simple {

    public function __construct( $product ) {
        $this->product_type = 'coupon_product';
        $this->supports[]   = 'ajax_add_to_cart';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'coupon_product';
    }
}