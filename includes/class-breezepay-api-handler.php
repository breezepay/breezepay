<?php

if (!defined('ABSPATH')) {
    exit;
}


class BreezepayAPIHandler
{
    private string $_clientID;
    private string $_clientSecret;
    private string $_baseURL = 'https://api.paywithbreeze.co/api/v1/';

    public function __construct(string $clientID, string $clientSecret)
    {
        $this->_clientID = $clientID;
        $this->_clientSecret = $clientSecret;
    }

    private function _getEncodedSecret()
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
            'method'  => $method,
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

    public function createPayment(float $amount, string $redirect_url, array $additional_data)
    {
        $encodedSecret = $this->_getEncodedSecret();
        $accessToken = $this->_sendRequest(
            'oauth/token/',
            ["token" => $encodedSecret],
            'POST',
            ['Content-Type' => 'application/json']
        );
        $headers = array(
            'Authorization' => 'Bearer ' . $accessToken['access_token'],
            'Content-Type' => 'application/json'
        );
        $args = [
            'amount' => $amount,
            'redirect_url' => $redirect_url,
            'additional_data' => json_encode($additional_data)
        ];
        $result = $this->_sendRequest('payments/', $args, 'POST', $headers);
        return $result;
    }
}
