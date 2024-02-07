=== Breezepay Payment Gateway For WooCommerce ===
Contributors: SushantNyachhyon
Tags: woocommerce, breezepay, payments, ecommerce, checkout, cryptocurrency, bitcoin, solana, xlm, xrp
Requires at least: 6.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

== Description ==
Breezepay allows website to integrate cryptocurrency payment where users can connect their defi wallet and process payment.
**Breezepay Woocommerce plugin** integrates pay with Breezepay option to their woocommerce website without any hassle integration steps during the checkout process.

== Supported Chain ==
Breezepay currently supports Solana USDC/USDT, Ethereum USDT and XRP. We regularly update our program to bring wide support for major chain and tokens.

== Requirements ==

To install and configure WooCommerce Breezepay, you will need:

* WordPress Version 6.0 or newer
* WooCommerce Version 7.4 or newer (installed and activated)
* PHP Version 7.4 or newer
* Breezepay merchant account

== Use of Third Party API ==
The WooCommerce Breezepay plugin depends on Breezepay API (https://api.paywithbreeze.co/api/v1) to generate unique, one-time, short lived payment url to which users are redirected to complete the payment process.

Breezepay's api uses OAuth 2.0 merchants client ID and client secret for validation.
It then takes the 'total amount' and 'reference ID' of customer's WooCommerce order, to generate the unique, one-time, short lived payment URL

Refer to our terms and condition (https://www.breezepay.com.au/termsandconditions) and privacy policy (https://www.breezepay.com.au/privacy) for any further conditions regarding or data collection or privacy aspects.