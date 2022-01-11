<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_gravityforms_Triggers_gf_payment_complete' ) ) :

	/**
	 * Load the gf_payment_complete trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_gravityforms_Triggers_gf_payment_complete {

		public function get_details(){

			$translation_ident = "trigger-gf_payment_complete-description";

			$parameter = array(
				'entry_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the currently present payment form submission.', $translation_ident ) ),
				'entry' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full entry data of the currently present form submission.', $translation_ident ) ),
				'action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The action data, containing further details about the payment.', $translation_ident ) ),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Payment completed',
				'webhook_slug' => 'gf_payment_complete',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
						'hook' => 'gform_post_payment_completed',
						'url' => 'https://docs.gravityforms.com/gform_post_payment_completed/',
					),
				)
			) );

			$settings = array();

			return array(
				'trigger'		   => 'gf_payment_complete',
				'name'			  => WPWHPRO()->helpers->translate( 'Payment completed', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'a payment was completed', $translation_ident ),
				'parameter'		 => $parameter,
				'settings'		  => $settings,
				'returns_code'	  => $this->get_demo( array() ),
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a Gravity Form Payment is completed.', $translation_ident ),
				'description'	   => $description,
				'callback'		  => 'test_gf_payment_complete',
				'integration'	   => 'gravityforms',
				'premium' => true,
			);

		}

		/*
		* Register the demo post delete trigger callback
		*
		* @since 1.2
		*/
		public function get_demo( $options = array() ) {

			$data = array (
				'entry_id' => '1',
				'entry' => 
				array (
				  'id' => '1',
				  'status' => 'active',
				  'form_id' => '1',
				  'ip' => '94.206.15.238',
				  'source_url' => 'https://your-domain.com/your-custom-path',
				  'currency' => 'USD',
				  'post_id' => NULL,
				  'date_created' => '2021-05-30 13:32:34',
				  'date_updated' => '2021-05-30 13:32:34',
				  'is_starred' => 0,
				  'is_read' => 0,
				  'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
				  'payment_status' => 'Paid',
				  'payment_date' => '2021-05-30 13:32:36',
				  'payment_amount' => 20,
				  'payment_method' => 'visa',
				  'transaction_id' => 'pi_1IwokmEOQk4ommW6eYx7F222',
				  'is_fulfilled' => '1',
				  'created_by' => '1',
				  'transaction_type' => '1',
				  '2.1' => 'Product Name',
				  '2.2' => '$10.00',
				  '2.3' => '2',
				  '1.1' => 'XXXXXXXXXXXX5556',
				  '1.4' => 'Visa',
				),
				'action' => 
				array (
				  'is_success' => true,
				  'transaction_id' => 'pi_1IwokmEOQk4ommW6eYx7F222',
				  'amount' => 20,
				  'payment_method' => 'visa',
				  'payment_status' => 'Paid',
				  'payment_date' => '2021-05-30 13:32:36',
				  'type' => 'complete_payment',
				  'transaction_type' => 'payment',
				  'amount_formatted' => '$20.00',
				  'note' => 'Payment has been completed. Amount: $20.00. Transaction Id: pi_1IwokmEOQk4ommW6eYx7F222.',
				),
			  );

			return $data;
		}

	}

endif; // End if class_exists check.