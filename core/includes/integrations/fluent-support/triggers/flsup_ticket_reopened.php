<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_reopened' ) ) :

 /**
  * Load the flsup_ticket_reopened trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_reopened {

	public function get_details(){

		$translation_ident = "action-flsup_ticket_reopened-description";
		$validated_types = array();

		if( defined( 'FLUENT_SUPPORT_VERSION' ) ){
			$flsup_helpers = WPWHPRO()->integrations->get_helper( 'fluent-support', 'flsup_helpers' );
		
			$validated_types = $flsup_helpers->get_person_types();
		}
		

		$parameter = array(
			'ticket' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All ticket related information, including the customer details.', $translation_ident ) ),
			'person' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the agent (or customer) that was reopening this ticket.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Ticket reopened',
			'webhook_slug' => 'flsup_ticket_reopened',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluent_support/ticket_reopen',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific person types only (E.g. when an Agent or Customer reopens the ticket). To do that, select specific person_type from the webhook URL settings.', $translation_ident ),
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
					'description' => WPWHPRO()->helpers->translate( 'Trigger this webhook only when a specific type of person reopened the ticket. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'flsup_ticket_reopened',
			'name'			  => WPWHPRO()->helpers->translate( 'Ticket reopened', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a ticket was reopened', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a ticket was reopened within Fluent Support.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-support',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'ticket' => 
			array (
			  'id' => 1,
			  'customer_id' => '2',
			  'agent_id' => '1',
			  'mailbox_id' => '1',
			  'product_id' => '0',
			  'product_source' => NULL,
			  'privacy' => 'private',
			  'priority' => 'normal',
			  'client_priority' => 'medium',
			  'status' => 'active',
			  'title' => 'This is a demo ticket subject',
			  'slug' => 'this-is-a-demo-ticket-subject',
			  'hash' => '5207cf2073',
			  'content_hash' => '28e42a075dd070101be323505083aec6',
			  'message_id' => NULL,
			  'source' => NULL,
			  'content' => '<p>Those are the details about the ticket. </p>',
			  'secret_content' => NULL,
			  'last_agent_response' => '2022-01-22 10:30:32',
			  'last_customer_response' => '2022-01-22 08:15:18',
			  'waiting_since' => '2022-01-22 10:55:10',
			  'response_count' => '2',
			  'first_response_time' => NULL,
			  'total_close_time' => '9586',
			  'resolved_at' => '2022-01-22 10:55:04',
			  'closed_by' => '1',
			  'created_at' => '2022-01-22 08:15:18',
			  'updated_at' => '2022-01-22 10:55:10',
			),
			'person' => 
			array (
			  'id' => 1,
			  'first_name' => 'Agent',
			  'last_name' => 'Demo',
			  'email' => 'agent@demo.test',
			  'title' => NULL,
			  'avatar' => NULL,
			  'person_type' => 'agent',
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
			  'hash' => '8ea9a292d792815fa56e8e18625d7d30',
			  'user_id' => '1',
			  'description' => NULL,
			  'remote_uid' => NULL,
			  'last_response_at' => NULL,
			  'created_at' => '2022-01-22 07:53:23',
			  'updated_at' => '2022-01-22 07:53:23',
			  'full_name' => 'Agent Demo',
			  'photo' => 'https://www.gravatar.com/avatar/ab43f84ba7xxxxxxxxxb5c90c9778?s=128',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.