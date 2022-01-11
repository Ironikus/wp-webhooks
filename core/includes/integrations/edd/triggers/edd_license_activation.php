<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_license_activation' ) ) :

 /**
  * Load the edd_license_activation trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_license_activation {

	public function is_active(){

		$is_active = class_exists( 'EDD_Software_Licensing' );

		//Backwards compatibility for the "Easy Digital Downloads" integration
		if( defined( 'WPWH_EDD_NAME' ) ){
			$is_active = false;
		}

		return $is_active;
	}

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'edd_sl_activate_license',
				'callback' => array( $this, 'wpwh_trigger_edd_license_activation' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-edd_license_activation-description";

		$parameter = array(
			'ID' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The license id.', $translation_ident ) ),
			'key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The license key.', $translation_ident ) ),
			'customer_email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer.', $translation_ident ) ),
			'customer_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full customer name.', $translation_ident ) ),
			'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The id of the product.', $translation_ident ) ),
			'product_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full product name.', $translation_ident ) ),
			'activation_limit' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The activation limit.', $translation_ident ) ),
			'activation_count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of total activations.', $translation_ident ) ),
			'activated_urls' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A list of activated URLs.', $translation_ident ) ),
			'expiration' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The expiration date in SQL format.', $translation_ident ) ),
			'is_lifetime' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number 1 or 0 if it is a lifetime.', $translation_ident ) ),
			'status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The current license status.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'License activated',
			'webhook_slug' => 'edd_license_activation',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'edd_sl_activate_license',
					'url' => 'https://docs.easydigitaldownloads.com/article/1723-software-licensing-developer-customizations',
				),
			)
		) );

		$settings = array();

		return array(
			'trigger'		   => 'edd_license_activation',
			'name'			  => WPWHPRO()->helpers->translate( 'License activated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a license was activated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires on activation of a license within Easy Digital Downloads.', $translation_ident ),
			'description'	   => $description,
			'callback'		  => 'test_edd_license_activation',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD payment was changed
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_license_activation( $license_id = 0, $download_id = 0 ){
		$edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_license_activation' );
		$response_data_array = array();

		foreach( $webhooks as $webhook ){
			$license_data = $edd_helpers->edd_get_license_data( $license_id, $download_id );

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
			}

			do_action( 'wpwhpro/webhooks/trigger_edd_license_activation', $license_id, $download_id, $license_data, $response_data_array );
		}
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'ID'			   => 1234,
			'key'			  => '736b31fec1ecb01c28b51a577bb9c2b3',
			'customer_name'	=> 'Jane Doe',
			'customer_email'   => 'jane@test.com',
			'product_id'	   => 4321,
			'product_name'	 => 'Sample Product',
			'activation_limit' => 1,
			'activation_count' => 1,
			'activated_urls'   => 'sample.com',
			'expiration'	   => date( 'Y-n-d H:i:s', current_time( 'timestamp' ) ),
			'is_lifetime'	  => 0,
			'status'		   => 'active',
		);

	  return $data;
	}

  }

endif; // End if class_exists check.