<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_payments' ) ) :

 /**
  * Load the edd_payments trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_payments {

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
				'hook' => 'edd_payment_delete',
				'callback' => array( $this, 'wpwh_trigger_edd_payments_delete_prepare' ),
				'priority' => 10,
				'arguments' => 1,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_update_payment_status',
				'callback' => array( $this, 'wpwh_trigger_edd_payments' ),
				'priority' => 10,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-edd_payments-description";

		$choices = array();
		if( function_exists( 'edd_get_payment_statuses' ) ){
			$choices = edd_get_payment_statuses();

			//add our custom delete status
			$choices['wpwh_deleted'] = WPWHPRO()->helpers->translate( 'Deleted', $translation_ident );
		}

		$parameter = array(
			'ID' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The payment id.', $translation_ident ) ),
			'key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique payment key.', $translation_ident ) ),
			'subtotal' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The subtotal of the payment.', $translation_ident ) ),
			'tax' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The tax amount of the payment.', $translation_ident ) ),
			'fees' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Additional payment fees of the payment.', $translation_ident ) ),
			'total' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The total amount of the payment.', $translation_ident ) ),
			'gateway' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The chosen payment gateway of the payment.', $translation_ident ) ),
			'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The customer email that was used for the payment the payment.', $translation_ident ) ),
			'date' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date (in SQL format) of the payment creation.', $translation_ident ) ),
			'products' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of al products that are included within the payment. Please check the example below for further details.', $translation_ident ) ),
			'discount_codes' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma separated list of applied coupon codes.', $translation_ident ) ),
			'first_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer.', $translation_ident ) ),
			'last_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer.', $translation_ident ) ),
			'transaction_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The transaction id of the payment.', $translation_ident ) ),
			'billing_address' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The billing adress with all its values. Please check the example below for further details.', $translation_ident ) ),
			'shipping_address' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The shipping adress with all its values. Please check the example below for further details.', $translation_ident ) ),
			'metadata' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of all available meta fields.', $translation_ident ) ),
			'new_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The new status of the payment.', $translation_ident ) ),
			'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The prrevious status of the payment.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Payments',
			'webhook_slug' => 'edd_payments',
			'post_delay' => true,
			'tipps' => array(
				WPWHPRO()->helpers->translate( "In case you only want to fire the webhook on specific payment statuses, you can select them within the single webhook URL settings. Simply select the payment statuses you want to fire the webhook on and all others are ignored.", $translation_ident ),
			),
			'trigger_hooks' => array(
				array( 
					'hook' => 'edd_update_payment_status',
				),
			)
		) );

		$settings = array(
			'data' => array(
				'wpwhpro_trigger_edd_payments_whitelist_status' => array(
					'id'		  => 'wpwhpro_trigger_edd_payments_whitelist_status',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $choices,
					'label'	   => WPWHPRO()->helpers->translate('Trigger on selected payment status changes', $translation_ident),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate('Select only the payment statuses you want to fire the trigger on. You can choose multiple ones. If none is selected, all are triggered.', $translation_ident)
				),
			)
		);

		return array(
			'trigger'		   => 'edd_payments',
			'name'			  => WPWHPRO()->helpers->translate( 'Payments status changed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a payments status was changed', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires on certain status changes of payments within Easy Digital Downloads.', $translation_ident ),
			'description'	   => $description,
			'callback'		  => 'test_edd_payments',
			'integration'	   => 'edd',
		);

	}

	public function wpwh_trigger_edd_payments_delete_prepare( $payment_id = 0 ){

		if( ! isset( $this->pre_trigger_values['edd_payments'] ) ){
			$this->pre_trigger_values['edd_payments'] = array();
		}

		if( ! isset( $this->pre_trigger_values['edd_payments'][ $payment_id ] ) ){
			$this->pre_trigger_values['edd_payments'][ $payment_id ] = array();
		}

		$edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
		$this->pre_trigger_values['edd_payments'][ $payment_id ] = $edd_helpers->wpwh_get_edd_order_data( $payment_id );
		
		//Init the post delay functions with further default parameters
		$this->wpwh_trigger_edd_payments_init( $payment_id, 'wpwh_deleted', 'wpwh_undeleted' );
		
	}

	/*
	* Register the edd payments post delay trigger logic
	*/
	public function wpwh_trigger_edd_payments_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'wpwh_trigger_edd_payments' ), func_get_args() );
	}

	/**
	 * Triggers once a new EDD payment was changed
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_payments( $payment_id, $new_status, $old_status ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_payments' );
		$edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
		$order_data = array();

		//Only fire on change
		if( $new_status === $old_status ){
			return;
		}

		foreach( $webhooks as $webhook ){

			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_trigger_edd_payments_whitelist_status' && ! empty( $settings_data ) ){
						if( ! in_array( $new_status, $settings_data ) ){
							$is_valid = false;
						}
					}

				}
			}

			if( $is_valid ) {

				if( isset( $this->pre_trigger_values['edd_payments'][ $payment_id ] ) ){
					$order_data = $this->pre_trigger_values['edd_payments'][ $payment_id ];
				} else {
					$order_data = $edd_helpers->wpwh_get_edd_order_data( $payment_id );
				}

				//append status changes
				$order_data['new_status'] = $new_status;
				$order_data['old_status'] = $old_status;

				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $order_data );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $order_data );
				}

				do_action( 'wpwhpro/webhooks/trigger_edd_payments', $payment_id, $new_status, $old_status, $response_data_array );
			}
			
		}
	}

	public function get_demo( $options = array() ) {

		$data = array (
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
		  );

		return $data;
	}

  }

endif; // End if class_exists check.