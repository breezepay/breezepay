<?php

class Breezepay_Gateway extends WC_Payment_Gateway
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
            'breezepay'
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
            'breezepay'
          ),
          esc_url('https://merchant.paywithbreeze.com.au')
        ),
      ),
      'webhook_secret' => array(
        'title' => __('Webhook Secret', 'breezepay'),
        'type' => 'text',
        'default' => '',
        'description' => sprintf(
          // translators: Description field for API on settings page. Includes external link.
          __(
            'You can manage your Webhook keys within the Breezepay Settings page, available here: %s',
            'breezppay'
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
   * Process payments
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
   * Update the status of an order from a payload.
   * 
   * @param WC_Order $order
   * @param array $status
   */
  public function breezepay_update_order_status($order, $status)
  {
    switch ($status) {
      case 'cancelled':
        $order->update_status('cancelled', __('Breezepay payment cancelled.', 'breezepay'));
        break;
      case 'pending':
        $order->update_status('pending', __('Breezepay payment pending.', 'breezepay'));
        break;
      case 'pending-chain':
        $order->update_status('blockchainpending', __('Breezepay payment detected but awaiting blockchain confirmation.', 'breezepay'));
        break;
      case 'completed':
        $order->update_status('processing', __('Breezepay payment was successfully processed.', 'breezepay'));
        $order->payment_complete();
        break;
      default:
        exit;
    }
  }

  /**
   * Handle requests sent to webhook.
   */
  public function handle_webhook()
  {
    $payload = file_get_contents('php://input');

    if (!empty($payload) && $this->validate_webhook()) {
      $data = json_decode($payload, true);

      if (!isset($data['order_id'])) {
        // Probably a charge not created by us.
        exit;
      }

      $order_id = $data['order_id'];

      $this->breezepay_update_order_status(wc_get_order($order_id), $data['status']);

      exit;  // 200 response for acknowledgement.
    }

    wp_die('Breezepay Webhook Request Failure', 'Breezepay Webhook', array('response' => 500));
  }

  /**
   * Check if webhook request is valid.
   * 
   * @param string $payload
   */
  public function validate_webhook()
  {
    if (!isset($_SERVER['HTTP_X_WEBHOOK_SIGNATURE']) && !isset($_SERVER['HTTP_X_WEBHOOK_MSG'])) {
      return false;
    }

    $hook_signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
    $message = $_SERVER['HTTP_X_WEBHOOK_MSG'];

    $secret = $this->get_option('webhook_secret');

    $wc_signature = hash_hmac('sha256', $message, $secret);

    if ($hook_signature === $wc_signature) {
      return true;
    }

    return false;
  }
}
