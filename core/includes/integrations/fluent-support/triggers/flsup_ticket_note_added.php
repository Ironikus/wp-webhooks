<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_note_added' ) ) :

 /**
  * Load the flsup_ticket_note_added trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_support_Triggers_flsup_ticket_note_added {

	public function get_details(){

		$translation_ident = "action-flsup_ticket_note_added-description";

		$parameter = array(
			'note' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further information on the added agent note.', $translation_ident ) ),
			'ticket' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All ticket related information, including the customer details.', $translation_ident ) ),
			'person' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the agent (or customer) that was adding a note to this ticket.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Ticket note added',
			'webhook_slug' => 'flsup_ticket_note_added',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluent_support/note_added_by_agent',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'Please note: Only agents can add notes to the ticket. Therefore, this trigger will only fire on agent notes.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'flsup_ticket_note_added',
			'name'			  => WPWHPRO()->helpers->translate( 'Ticket note added', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a note was added to a ticket', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an agent note was added to a ticket within Fluent Support.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-support',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'note' => 
				array (
					'person_id' => 1,
					'ticket_id' => 1,
					'conversation_type' => 'note',
					'content' => '<p>This is an internal note added by an agent.</p>
				',
					'source' => 'web',
					'content_hash' => '11a049a07c089aae57c0e32286a4d872',
					'updated_at' => '2022-01-22 10:45:56',
					'created_at' => '2022-01-22 10:45:56',
					'id' => 16,
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
					'photo' => 'https://www.gravatar.com/avatar/ab43f84bxxxxxxxxxx90c9778?s=128',
					),
			),
			'ticket' => 
			array (
			  'id' => 1,
			  'customer_id' => '2',
			  'agent_id' => NULL,
			  'mailbox_id' => '1',
			  'product_id' => '0',
			  'product_source' => NULL,
			  'privacy' => 'private',
			  'priority' => 'normal',
			  'client_priority' => 'medium',
			  'status' => 'closed',
			  'title' => 'This is a demo ticket subject',
			  'slug' => 'this-is-a-demo-ticket-subject',
			  'hash' => '5207cf2073',
			  'content_hash' => '28e42a075dd070101be323505083aec6',
			  'message_id' => NULL,
			  'source' => NULL,
			  'content' => '<p>Those are the details about the ticket. </p>',
			  'secret_content' => NULL,
			  'last_agent_response' => NULL,
			  'last_customer_response' => '2022-01-22 08:15:18',
			  'waiting_since' => '2022-01-22 08:15:18',
			  'response_count' => '0',
			  'first_response_time' => NULL,
			  'total_close_time' => 37,
			  'resolved_at' => '2022-01-22 08:15:55',
			  'closed_by' => 1,
			  'created_at' => '2022-01-22 08:15:18',
			  'updated_at' => '2022-01-22 08:15:55',
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
			  'customer' => 
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
				'hash' => '384ec0f98dbc0axxxx2eb73d2fcde8f',
				'user_id' => NULL,
				'description' => NULL,
				'remote_uid' => NULL,
				'last_response_at' => '2022-01-22 08:15:20',
				'created_at' => '2022-01-22 08:15:18',
				'updated_at' => '2022-01-22 08:15:20',
				'full_name' => 'Jon Doe',
				'photo' => 'https://www.gravatar.com/avatar/ab43f84ba713a84c93axxxxxxxxx?s=128',
			  ),
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
			  'hash' => '8ea9a292d79281xxxxe8e18625d7d30',
			  'user_id' => '1',
			  'description' => NULL,
			  'remote_uid' => NULL,
			  'last_response_at' => NULL,
			  'created_at' => '2022-01-22 07:53:23',
			  'updated_at' => '2022-01-22 07:53:23',
			  'full_name' => 'Agent Demo',
			  'photo' => 'https://www.gravatar.com/avatar/ab43f84ba713a84c93axxxxxxxxx?s=128',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.