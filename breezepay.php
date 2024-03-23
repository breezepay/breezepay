<?php

/*
Plugin Name:            Breezepay
Plugin URI:             https://github.com/breezepay/breezepay-woocommerce/
Description:            Allows customers to pay with their Crypto Wallet via Breezpay Gateway
Version:                1.0.0
Requires PHP:           7.4
Requires at least:      6.0
Tested up to:           6.4
WC requires at least:   7.4
WC tested up to:        8.7
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
function breezepay_init_gateway()
{
  if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    require_once 'class-wc-gateway-breezepay.php';
    add_action('init', 'breezepay_wc_register_blockchain_status');
    add_filter('wc_order_statuses', 'breezepay_wc_add_status');
    add_filter('woocommerce_payment_gateways', 'breezepay_wc_add_breezepay_class');
  }
}

// check for HPOS compatibility
add_action('before_woocommerce_init', function () {
  if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
  }
});

add_action('plugins_loaded', 'breezepay_init_gateway');

/**
 * Initialize Breezepay class
 */
function breezepay_wc_add_breezepay_class($methods)
{
  $methods[] = 'Breezepay_Gateway';
  return $methods;
}

/**
 * Register new status for Blockchain Pending
 */
function breezepay_wc_register_blockchain_status()
{
  register_post_status('breezepay-wc-blockchainpending', array(
    'label' => __('Blockchain Pending', 'breezepay'),
    'public' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop('Blockchain pending <span class="count">(%s)</span>', 'Blockchain pending <span class="count">(%s)</span>'),
  ));
}

/**
 * Add registered status to list of WC Order statuses
 * 
 * @param array $wc_statuses_arr
 */
function breezepay_wc_add_status($wc_statuses_arr)
{
  $new_statuses_arr = array();
  // Add new order status after payment pending.
  foreach ($wc_statuses_arr as $id => $label) {
    $new_statuses_arr[$id] = $label;

    if ('wc-pending' === $id) {  // after "Payment Pending" status.
      $new_statuses_arr['breezepay-wc-blockchainpending'] = __('Blockchain Pending', 'breezepay');
    }
  }
  return $new_statuses_arr;
}
