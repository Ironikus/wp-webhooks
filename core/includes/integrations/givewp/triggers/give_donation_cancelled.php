<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Triggers_give_donation_cancelled' ) ) :

 /**
  * Load the give_donation_cancelled trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_givewp_Triggers_give_donation_cancelled {

	public function get_details(){

		$translation_ident = "action-give_donation_cancelled-description";
		$validated_forms = array();

		if( function_exists( 'give_get_payment_statuses' ) ){
			$give_helpers = WPWHPRO()->integrations->get_helper( 'givewp', 'give_helpers' );
		
			$validated_forms = $give_helpers->get_payment_forms();
		}

		$parameter = array(
			'payment_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the current payment.', $translation_ident ) ),
			'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The previous status of the payment.', $translation_ident ) ),
			'payment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All related data to the payment itself.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Donation cancelled',
			'webhook_slug' => 'give_donation_cancelled',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'give_update_payment_status',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific statuses only. To do that, simply specify the status slugs within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_givewp_trigger_on_selected_forms' => array(
					'id'		  => 'wpwhpro_givewp_trigger_on_selected_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_forms,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the give forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'give_donation_cancelled',
			'name'			  => WPWHPRO()->helpers->translate( 'Donation cancelled', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a donation was cancelled', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a donation was cancelled within GiveWP.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'givewp',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'payment_id' => 9068,
			'old_status' => 'failed',
			'payment' => 
			array (
			  'id' => 9068,
			  'new' => false,
			  'number' => '4',
			  'mode' => 'test',
			  'import' => false,
			  'key' => 'cef202ecf786c63c85db6c8abea45767',
			  'form_title' => 'Donation Form',
			  'form_id' => '9063',
			  'price_id' => '3',
			  'total' => 100,
			  'subtotal' => 100,
			  'date' => '2022-01-24 04:36:00',
			  'post_date' => '2022-01-24 04:36:00',
			  'completed_date' => '2022-01-24 04:36:00',
			  'status' => 'cancelled',
			  'status_nicename' => 'Cancelled',
			  'customer_id' => '1',
			  'donor_id' => '1',
			  'user_id' => 1,
			  'title_prefix' => '',
			  'first_name' => 'Demo',
			  'last_name' => 'User',
			  'email' => 'demo@user.test',
			  'address' => 
			  array (
				'line1' => '',
				'line2' => '',
				'city' => '',
				'state' => '',
				'zip' => '',
				'country' => 'US',
			  ),
			  'transaction_id' => 9068,
			  'ip' => '127.0.0.1',
			  'gateway' => 'manual',
			  'currency' => 'USD',
			  'parent_payment' => 0,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.