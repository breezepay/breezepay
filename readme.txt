=== Breezepay ===

Contributors: Breezepay
Tags: Cryptocurrency, WooCommerce, USDC, USDT, Payments
Requires at least: 6.0
Tested up to: 6.4.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Short Description
Make cryptocurrency payments a breeze in your WooCommerce store with the Breezepay plugin.

== Description ==

Make cryptocurrency payments a breeze in your WooCommerce store with the Breezepay plugin.

Breezepay is the fastest and easiest way for your customers to pay with cryptocurrency. By integrating Breezepay into your WooCommerce store, customers can opt to pay using USDC or USDT stablecoins across various blockchains, ensuring you receive the correct value for your goods while accessing a broader market of cryptocurrency users, without the volatility typically associated with cryptocurrency transactions.

== Supported Blockchains ==

- Ethereum
- Solana
- XRPL
- Tron (coming soon)

== Supported Cryptocurrencies ==

- USDT
- USDC
- AUDD (coming soon)

== Supported Wallets ==

- MetaMask
- Phantom
- Xumm/Xaman

== Benefits ==

- **Peer-to-peer:** Customers pay directly into your wallet. Breezepay never holds your funds.
- **Instant payments:** Transactions settle in your wallet immediately.
- **No chargebacks:** Cryptocurrency transactions require the customer to have sufficient funds, reducing fraud.
- **Dedicated Service:** Our team is always ready to assist with your Breezepay integration.
- **Easy payment experience:** Designed for simplicity, Breezepay encourages fewer drop-offs and increased revenue.
- **No upfront fees:** Breezepay is free to set up, with no monthly fees. We charge a modest 2% fee per transaction.

== Requirements ==

To install and configure WooCommerce Breezepay, you will need:

* WordPress Version 6.0 or newer
* WooCommerce Version 7.4.0 or newer (installed and activated)
* PHP Version 7.4 (Recommended version 8.0)
* Breezepay merchant account

== Automatic Installation ==

1. To do an automatic install of Breezepay, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”
2. In the search field type “Breezepay,” then click “Search Plugins.”
3. Once you’ve found our plugin, you can install it by clicking “Install Now,”.
4. Activate the plugin once the installation if finished.
5. Navigate to WooCommerce settings to configure Breezepay as your payment gateway.

== Manual Installation ==

1. Head towards for github repository (https://github.com/breezepay/breezepay-woocommerce) and download our latest code by clicking "Download ZIP" button.
2. Unzip the plugin.
3. Upload the `breezepay-woocommerce` folder to your `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Navigate to WooCommerce settings to configure Breezepay as your payment gateway.

== Screenshots ==

1. Breezepay payment button at the point of checkout
2. The payment process (select wallet)
3. Connect wallet
4. Confirm transaction

== Changelog ==

1.0.0 - Initial release.

== Upgrade Notice ==

1.0.0 - Welcome to Breezepay! Enjoy seamless cryptocurrency transactions in your WooCommerce store.

== Use of Third Party API ==

The WooCommerce Breezepay plugin depends on Breezepay API (https://api.paywithbreeze.co/api/v1) to generate unique, one-time, short lived payment url to which users are redirected to complete the payment process.

Breezepay's api uses OAuth 2.0 merchants client ID and client secret for validation.
It then takes the 'total amount' and 'reference ID' of customer's WooCommerce order, to generate the unique, one-time, short lived payment URL

Refer to our terms and condition (https://www.breezepay.com.au/termsandconditions) and privacy policy (https://www.breezepay.com.au/privacy) for any further conditions regarding or data collection or privacy aspects.