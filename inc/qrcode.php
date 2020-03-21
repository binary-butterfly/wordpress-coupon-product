<?php

use Endroid\QrCode\QrCode;

function generate_coupon_qr_code_image_src($coupon_code, $token) {
    $url = get_site_url() . '/coupon-product/1.0/check?code=' . $coupon_code . '&token=' . $token;
    $qrCode = new QrCode($url);
    return 'data:image/gif;base64,' . base64_encode($qrCode->writeString());
}
