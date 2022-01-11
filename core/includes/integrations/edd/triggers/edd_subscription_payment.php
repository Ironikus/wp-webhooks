<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_subscription_payment' ) ) :

 /**
  * Load the edd_subscription_payment trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_subscription_payment {

	public function is_active(){

		$is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

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
				'hook' => 'edd_recurring_add_subscription_payment',
				'callback' => array( $this, 'wpwh_trigger_edd_subscription_payment_init' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-edd_subscription_payment-description";

		$parameter = array(
			'payment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The payment data. For further details, please refer to the example down below.', 'trigger-edd_subscription_payment-content' ) ),
			'subscription' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The subscription data. For further details, please refer to the example down below.', 'trigger-edd_subscription_payment-content' ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'New subscription payment',
			'webhook_slug' => 'edd_subscription_payment',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'edd_recurring_add_subscription_payment',
				),
			)
		) );

		$settings = array();

		return array(
			'trigger'		   => 'edd_subscription_payment',
			'name'			  => WPWHPRO()->helpers->translate( 'Subscription payment created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a new subscription payment was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a new subscription payment is created within Easy Digital Downloads.', $translation_ident ),
			'description'	   => $description,
			'callback'		  => 'test_edd_subscription_payment',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD customer was created
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_subscription_payment( EDD_Payment $payment, EDD_Subscription $subscription ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_subscription_payment' );

		$response_data_array = array();
		$data = array( 
			'payment' => $payment,
			'subscription' => $subscription
		);

		foreach( $webhooks as $webhook ){
			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			}
		}

		do_action( 'wpwhpro/webhooks/trigger_edd_subscription_payment', $data, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'payment' => array(
				'ID' => 123,
				'key' => 'c36bc5d3315cde89ce18a19bb6a1d559',
				'subtotal' => 39,
				'tax' => '0',
				'fees' => 
				array (
				),
				'total' => 39,
				'gateway' => 'manual',
				'email' => 'johndoe123@test.com',
				'date' => '2020-04-23 09:16:00',
				'products' => 
				array (
				  array (
					'Product' => 'Demo Download',
					'Subtotal' => 39,
					'Tax' => '0.00',
					'Discount' => 0,
					'Price' => 39,
					'PriceName' => 'Single Site',
					'Quantity' => 1,
				  ),
				),
				'discount_codes' => 'none',
				'first_name' => 'Jon',
				'last_name' => 'Doe',
				'transaction_id' => 123,
				'billing_address' => array( 'line1' => 'Street 1', 'line2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
				'shipping_address' => array( 'address' => 'Street 1', 'Address2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
				'metadata' => 
				array (
				  '_edd_payment_tax_rate' => 
				  array (
					0 => '0',
				  ),
				  '_edd_complete_actions_run' => 
				  array (
					0 => '8763342154',
				  ),
				),
				'new_status' => 'publish',
				'old_status' => 'pending',
			),
			'subscription' => array(
				'id'				=> '183',
				'customer_id'	   => '36',
				'period'			=> 'month',
				'initial_amount'	=> '16.47',
				'recurring_amount'  => '10.98',
				'bill_times'		=> '0',
				'transaction_id'	=> '',
				'parent_payment_id' => '845',
				'product_id'		=> '8',
				'created'		   => '2016-06-13 13:47:24',
				'expiration'		=> '2016-07-13 23:59:59',
				'status'			=> 'pending',
				'profile_id'		=> 'ppe-4e3ca7d1c017e0ea8b24ff72d1d23022-8',
				'gateway'		   => 'paypalexpress',
				'customer'		  => array(
					'id'			 => '36',
					'purchase_count' => '2',
					'purchase_value' => '32.93',
					'email'		  => 'jane@test.com',
					'emails'		 => array(
						'jane@test.com',
					),
					'name'		   => 'Jane Doe',
					'date_created'   => '2016-06-13 13:19:50',
					'payment_ids'	=> '842,845,846',
					'user_id'		=> '1',
					'notes'		  => array(
						  'These are notes about the customer',
					),
				),
				'user_id' => '24',
			)
		);

		return $data;
	}

  }

endif; // End if class_exists check.