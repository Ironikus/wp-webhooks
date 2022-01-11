<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_deleted' ) ) :

 /**
  * Load the fcrm_contact_deleted trigger
  *
  * @since 4.3.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_deleted {

	public function get_details(){

		$translation_ident = "action-fcrm_contact_deleted-description";

		$parameter = array(
			'contact_ids' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All contact ids that have been deleted with this request.', $translation_ident ) ),
			'contacts' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the deleted contacts.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Contact deleted',
			'webhook_slug' => 'fcrm_contact_deleted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluentcrm_after_subscribers_deleted',
					'url' => 'https://fluentcrm.com/docs/action-hooks/',
				),
			),
		) );

		$settings = array();

		return array(
			'trigger'		   => 'fcrm_contact_deleted',
			'name'			  => WPWHPRO()->helpers->translate( 'Contact deleted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a contact was deleted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a contact was deleted within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
            'contact_ids' => 
            array (
              0 => '2',
            ),
            'contacts' => 
            array (
              0 => 
              array (
                'id' => '2',
                'user_id' => NULL,
                'hash' => 'bffa0c582c9c84bb4d42b8XXXXXXXX',
                'contact_owner' => NULL,
                'company_id' => NULL,
                'prefix' => NULL,
                'first_name' => 'Jon',
                'last_name' => 'Doe',
                'email' => 'jon@doe.test',
                'timezone' => NULL,
                'address_line_1' => '',
                'address_line_2' => '',
                'postal_code' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'ip' => NULL,
                'latitude' => NULL,
                'longitude' => NULL,
                'total_points' => '0',
                'life_time_value' => '0',
                'phone' => '123456789',
                'status' => 'subscribed',
                'contact_type' => 'lead',
                'source' => NULL,
                'avatar' => NULL,
                'date_of_birth' => '0000-00-00',
                'created_at' => '2021-12-01 14:39:51',
                'last_activity' => NULL,
                'updated_at' => '2021-12-01 14:39:51',
                'photo' => 'https://www.gravatar.com/avatar/bffa0c582c9c84bb4d42b8d99ad46cf3?s=128',
                'full_name' => 'Jon Doe',
              ),
            ),
        );

		return $data;
	}

  }

endif; // End if class_exists check.