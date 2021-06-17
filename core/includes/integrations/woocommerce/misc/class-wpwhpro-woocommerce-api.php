<?php

if( ! class_exists( 'Automattic\\WooCommerce\\Client' ) ){
    require __DIR__ . '/rest-api/vendor/autoload.php';
}

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class WPWHPRO_Woocommerce_Load_API{

    protected $wc_api;

    public function load_woocommerce_api( $args ){   

        $this->wc_api = new Client(
            $args['store_url'], // Your store URL
            $args['consumer_key'], // Your consumer key
            $args['consumer_secret'], // Your consumer secret
            $args['options'] // Your custom options
        );

    }

    public function wc_call( $method, $api_base = '', $validated_data = null ){

        $return = array(
            'success' => false
        );
        
        try {  

            if( $validated_data !== null ){
                $api_response = $this->wc_api->{$method}( $api_base, $validated_data );
            } else {
                $api_response = $this->wc_api->{$method}( $api_base );
            }

            $lastRequest = $this->wc_api->http->getRequest();
            $lastResponse = $this->wc_api->http->getResponse();

            $return['success'] = true;
            $return['data'] = array(
                'api_data' => $api_response,
                'last_request' => array(
                    'url' => $lastRequest->getUrl(),
                    'method' => $lastRequest->getMethod(),
                    'parameters' => $lastRequest->getParameters(),
                    'headers' => $lastRequest->getHeaders(),
                    'body' => $lastRequest->getBody()
                ),
                'last_response' => array(
                    'code' => $lastResponse->getCode(),
                    'headers' => $lastResponse->getHeaders(),
                    'body' => $lastResponse->getBody()
                )
            );
        
        } catch ( HttpClientException $e ) {
            
            $return['error'] = array(
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->getResponse(),
            );

        }

        return $return;

    }

}