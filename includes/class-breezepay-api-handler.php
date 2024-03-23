<?php

if (!defined('ABSPATH')) {
  exit;
}

class BreezepayAPIHandler
{
  /** @var string $_clientID **/
  private string $_clientID;
  /** @var string $_clientSecret **/
  private string $_clientSecret;
  /** @var string $_baseURL **/
  private string $_baseURL = 'https://api.paywithbreeze.co/api/v1';

  /**
   * Constructor
   * 
   * @param string $clientID
   * @param string $clientSecret
   */
  public function __construct(string $clientID, string $clientSecret)
  {
    $this->_clientID = $clientID;
    $this->_clientSecret = $clientSecret;
  }

  /**
   * Get base64 encode client ID
   * 
   * @return string
   */
  private function _getEncodedSecret(): string
  {
    return base64_encode($this->_clientID . ':' . $this->_clientSecret);
  }

  /**
   * Get the response from an API request.
   * 
   * @param  string $endpoint
   * @param  array  $params
   * @param  string $method
   * @return array
   */
  private function _sendRequest($endpoint, $params = array(), $method = 'GET', $headers = [])
  {
    $args = array(
      'method' => $method,
      'headers' => $headers
    );
    $url = $this->_baseURL . $endpoint;
    if (in_array($method, array('POST', 'PUT'))) {
      $args['body'] = json_encode($params);
    } else {
      $url = add_query_arg($params, $url);
    }
    $response = wp_remote_request(esc_url_raw($url), $args);
    if (is_wp_error($response)) {
      return array(false, $response->get_error_message());
    } else {
      return json_decode($response['body'], true);
    }
  }

  /**
   * Create payment
   * 
   * @param float $amount
   * @param string $redirect_url
   * @param string $order_id
   * @return array
   */
  public function createPayment(float $amount, string $redirect_url, string $order_id)
  {
    $encodedSecret = $this->_getEncodedSecret();
    $oauth = $this->_sendRequest(
      '/oauth/token',
      ["client_secret" => $encodedSecret, "grant_type" => "token"],
      'POST',
      ['Content-Type' => 'application/json']
    );
    $headers = array(
      'Authorization' => 'Bearer ' . $oauth['access_token'],
      'Content-Type' => 'application/json'
    );
    $args = [
      "ref_id" => $order_id,
      'currency' => 'AUD',
      // supported currency at the moment
      'amount' => $amount,
      'metadata' => [
        "source" => "woocommerce",
        "redirect_url" => $redirect_url
      ]
    ];
    $result = $this->_sendRequest('/order', $args, 'POST', $headers);
    return $result;
  }
}
