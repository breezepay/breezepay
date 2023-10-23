<?php

class WC_Gateway_Breezepay extends WC_Payment_Gateway
{
    /**
     * Log_enabled - whether or not logging is enabled
     * 
     * @var bool	Whether or not logging is enabled 
     */
    public static $log_enabled = false;

    /** 
     * WC_Logger Logger instance
     * 
     * @var WC_Logger Logger instance
     * */
    public static $log = false;

    public function __construct()
    {
        $this->id = 'breezepay';
        $this->has_fields = false;
        $this->order_button_text = __('Proceed to Breezepay', 'breezepay');
        $this->method_title = __('Breezepay', 'breezepay');

        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->debug = 'yes' === $this->get_option('debug', 'no');

        self::$log_enabled = $this->debug;

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        // add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, '_custom_query_var' ), 10, 2 );
        add_action('woocommerce_api_wc_gateway_breezepay', array($this, 'handle_webhook'));
    }

    /**
     * Initialize form fields
     *
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable Breezepay Commerce Payment', 'breezepay'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default' => __('Pay With Breezepay', 'breezepay'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'text',
                'desc_tip' => true,
                'description' => __('This controls the description which the user sees during checkout.', 'woocommerce'),
                'default' => __('Pay with Bitcoin, Sol, XLM or other cryptocurrencies.', 'breezepay'),
            ),
            'client_id' => array(
                'title' => __('Client ID', 'breezepay'),
                'type' => 'text',
                'desc_tip' => true,
                'default' => '',
                'description' => sprintf(
                    // translators: Description field for API on settings page. Includes external link.
                    __(
                        'You can manage your API keys within the Breezepay Settings page, available here: %s',
                        'coinbase'
                    ),
                    esc_url('https://merchant.paywithbreeze.com.au')
                ),
            ),
            'client_secret' => array(
                'title' => __('Client Secret', 'breezepay'),
                'type' => 'text',
                'default' => '',
                'description' => sprintf(
                    // translators: Description field for API on settings page. Includes external link.
                    __(
                        'You can manage your API keys within the Breezepay Settings page, available here: %s',
                        'coinbase'
                    ),
                    esc_url('https://merchant.paywithbreeze.com.au')
                ),
            ),
            'debug' => array(
                'title' => __('Debug log', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable logging', 'woocommerce'),
                'default' => 'no',
                // translators: Description for 'Debug log' section of settings page.
                'description' => sprintf(__('Log Breezepay API events inside %s', 'breezepay'), '<code>' . WC_Log_Handler_File::get_log_file_path('breezepay') . '</code>'),
            ),
        );
    }

    /**
     * Init the API class and set the API key etc.
     */
    protected function init_api()
    {
        include_once dirname(__FILE__) . '/includes/class-breezepay-api-handler.php';
    }

    /**
     * Process payment process
     *
     * @param $order_id
     * @return array
     */
    public function process_payment($order_id)
    {

        global $woocommerce;

        // we need it to get any order details
        $order = wc_get_order($order_id);

        $this->init_api();

        $return_url = $this->get_return_url($order);

        $api = new BreezepayAPIHandler(
            $this->get_option('client_id'),
            $this->get_option('client_secret')
        );
        $result = $api->createPayment(
            $order->get_total(),
            $return_url,
            $order_id
        );


        $redirect = $result['payment_url'];

        return array(
            'result' => 'success',
            'redirect' => $redirect
        );
    }

    /**
     * Handle requests sent to webhook.
     */
    public function handle_webhook()
    {
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        $order = wc_get_order($order_id);
        $order->payment_complete();
        wc_reduce_stock_levels($order_id);
    }
}
