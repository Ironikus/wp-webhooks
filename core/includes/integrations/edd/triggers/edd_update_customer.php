<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_update_customer' ) ) :

 /**
  * Load the edd_update_customer trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_update_customer {

	public function is_active(){

		$is_active = true;

		//Backwards compatibility
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
				'hook' => 'edd_customer_post_update',
				'callback' => array( $this, 'wpwh_trigger_edd_update_customer' ),
				'priority' => 10,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-edd_update_customer-description";

		$parameter = array(
			'first_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer.', 'trigger-edd_update_customer-content' ) ),
			'last_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer.', 'trigger-edd_update_customer-content' ) ),
			'id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique id of the customer. (This is not the user id)', 'trigger-edd_update_customer-content' ) ),
			'purchase_count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of purchases of the customer.', 'trigger-edd_update_customer-content' ) ),
			'purchase_value' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The value of all purchases of the customer.', 'trigger-edd_update_customer-content' ) ),
			'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The main email of the customer.', 'trigger-edd_update_customer-content' ) ),
			'emails' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Additional emails of the customer.', 'trigger-edd_update_customer-content' ) ),
			'name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full name of the customer.', 'trigger-edd_update_customer-content' ) ),
			'date_created' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date and time of the user creation in SQL format.', 'trigger-edd_update_customer-content' ) ),
			'payment_ids' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comme-separated list of payment ids.', 'trigger-edd_update_customer-content' ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The user id of the customer.', 'trigger-edd_update_customer-content' ) ),
			'notes' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Additional ntes given by the customer.', 'trigger-edd_update_customer-content' ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Customer updated',
			'webhook_slug' => 'edd_update_customer',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 'hook' => 'edd_customer_post_update' ),
			)
		) );

		$settings = array();

		return array(
			'trigger'		   => 'edd_update_customer',
			'name'			  => WPWHPRO()->helpers->translate( 'Customer updated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a customer was updated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a customer is updated within Easy Digital Downloads.', $translation_ident ),
			'description'	   => $description,
			'callback'		  => 'test_edd_update_customer',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD customer was created
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_update_customer( $updated = false, $customer_id = 0, $args = array() ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_update_customer' );

		if( ! class_exists( 'EDD_Customer' ) || ! $updated ){
			return;
		}
		
		$customer = new EDD_Customer( $customer_id );

		//Properly calculate names as given by the Zapier extension
		$first_name = '';
		$last_name = '';
		if( isset( $customer->name ) ){
			$separated_names = explode( ' ', $customer->name );

			$first_name = ( ! empty( $separated_names[0] ) ) ? $separated_names[0] : '';

			if( ! empty( $separated_names[1] ) ) {
				unset( $separated_names[0] );
				$last_name = implode( ' ', $separated_names );
			}
		}
		$customer->first_name = $first_name;
		$customer->last_name  = $last_name;

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_edd_update_customer', $customer_id, $customer, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'user_id'		=> 1234,
			'name'		   => 'John Doe',
			'first_name'	 => 'John',
			'last_name'	  => 'Doe',
			'email'		  => 'johndoe123@test.com',
			'payment_ids'	=> 2345,
			'purchase_value' => '23.5',
			'date_created'   => date( 'Y-m-d h:i:s' ),
			'purchase_count' => 1,
			'notes'		  => null,
		);

		return $data;
	}

  }

endif; // End if class_exists check.