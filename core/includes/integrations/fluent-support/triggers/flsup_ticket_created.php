<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_created' ) ) :

 /**
  * Load the flsup_ticket_created trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_created {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'fluent_support/ticket_created',
				'callback' => array( $this, 'fluentcrm_flsup_ticket_created_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-flsup_ticket_created-description";
		$validated_types = array();

		if( defined( 'FLUENT_SUPPORT_VERSION' ) ){
			$flsup_helpers = WPWHPRO()->integrations->get_helper( 'fluent-support', 'flsup_helpers' );
		
			$validated_types = $flsup_helpers->get_person_types();
		}
		

		$parameter = array(
			'ticket' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All ticket related information, including the customer details.', $translation_ident ) ),
			'person' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the agent (or customer) that created this ticket.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Ticket created',
			'webhook_slug' => 'flsup_ticket_created',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluent_support/ticket_created',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific person types only (E.g. when an Agent or Customer created ticket). To do that, select specific person_type from the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_fluent_support_trigger_on_person_type' => array(
					'id'		  => 'wpwhpro_fluent_support_trigger_on_person_type',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_types,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected person types', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Trigger this webhook only when a specific type of person created the ticket. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'flsup_ticket_created',
			'name'			  => WPWHPRO()->helpers->translate( 'Ticket created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a ticket was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a ticket was created within Fluent Support.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-support',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a ticket was created within Fluent Support
	 *
	 * @param object $ticket The ticket object
	 * @param object $person The person object
	 */
	public function fluentcrm_flsup_ticket_created_callback( $ticket, $person ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'flsup_ticket_created' );

		$person_type = ( is_object( $person ) && isset( $person->person_type  ) ) ? $person->person_type : '';

		$payload = array(
			'ticket' => $ticket,
			'person' => $person,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_fluent_support_trigger_on_person_type'] ) && ! empty( $webhook['settings']['wpwhpro_fluent_support_trigger_on_person_type'] ) ){
					if( ! in_array( $person_type, $webhook['settings']['wpwhpro_fluent_support_trigger_on_person_type'] ) ){
						$is_valid = false;
					}
				}

			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_flsup_ticket_created', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'ticket' => 
			array (
			  'customer_id' => '2',
			  'mailbox_id' => 1,
			  'title' => 'Demo Ticket',
			  'content' => '<p>This is a newly created demo ticket.</p>',
			  'product_id' => '',
			  'client_priority' => 'medium',
			  'slug' => 'demo-ticket',
			  'hash' => 'fe43943458',
			  'last_customer_response' => '2022-01-22 11:05:26',
			  'content_hash' => '70e80d0590a300a2def4cb8a50016f25',
			  'created_at' => '2022-01-22 11:05:26',
			  'updated_at' => '2022-01-22 11:05:26',
			  'waiting_since' => '2022-01-22 11:05:26',
			  'id' => 2,
			  'mailbox' => 
			  array (
				'id' => 1,
				'name' => 'Demo Business',
				'slug' => 'demo-business',
				'box_type' => 'web',
				'email' => 'demo@business.test',
				'mapped_email' => NULL,
				'email_footer' => NULL,
				'settings' => 
				array (
				  'admin_email_address' => 'demo@business.test',
				),
				'avatar' => NULL,
				'created_by' => '1',
				'is_default' => 'yes',
				'created_at' => '2022-01-22 08:14:06',
				'updated_at' => '2022-01-22 08:14:06',
			  ),
			),
			'person' => 
			array (
			  'id' => 2,
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'email' => 'jondoe@democustomer.test',
			  'title' => NULL,
			  'avatar' => NULL,
			  'person_type' => 'customer',
			  'status' => 'active',
			  'ip_address' => NULL,
			  'last_ip_address' => NULL,
			  'address_line_1' => NULL,
			  'address_line_2' => NULL,
			  'city' => NULL,
			  'zip' => NULL,
			  'state' => NULL,
			  'country' => NULL,
			  'note' => NULL,
			  'hash' => '384ec0f98dbc0a277702eb73d2fcde8f',
			  'user_id' => NULL,
			  'description' => NULL,
			  'remote_uid' => NULL,
			  'last_response_at' => '2022-01-22 11:05:27',
			  'created_at' => '2022-01-22 08:15:18',
			  'updated_at' => '2022-01-22 11:05:27',
			  'full_name' => 'Jon Doe',
			  'photo' => 'https://www.gravatar.com/avatar/586e6c37d7exxxxxxxx77ea0c0?s=128',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.