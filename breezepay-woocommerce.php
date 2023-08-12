<?php

/*
Plugin Name:            Breezepay WooCommerce
Plugin URI:             https://github.com/breezepay/breezepay-woocommerce/
Description:            Allows customers to pay with their Crypto Wallet via Breezpay Gateway
Version:                1.0
Requires PHP:           7.4
Requires at least:      6.0
Tested up to:           6.2.2
WC requires at least:   7.4
WC tested up to:        7.8.2
Author:                 Breezepay
Author URI:             https://breezepay.com.au/
License:                GPLv3+
License URI:            https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:            breezepay
Domain Path:            /languages

Breezepay WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Breezepay WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Breezepay WooCommerce. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize plugins if WooCommerce is avaiable
 *
 * @return void
 */
function bp_init_gateway()
{
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        require_once 'class-wc-gateway-breezepay.php';
        add_filter('woocommerce_payment_gateways', 'bp_wc_add_breezepay_class');
    }
}
add_action('plugins_loaded', 'bp_init_gateway');

function bp_wc_add_breezepay_class($methods)
{
    $methods[] = 'WC_Gateway_Breezepay';
    return $methods;
}