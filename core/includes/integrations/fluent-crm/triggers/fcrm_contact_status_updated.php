<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_status_updated' ) ) :

 /**
  * Load the fcrm_contact_status_updated trigger
  *
  * @since 4.3.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_status_updated {

	public function get_details(){

		$translation_ident = "action-fcrm_contact_status_updated-description";
		$validated_statuses = array();

		if( defined( 'FLUENTCRM' ) ){
			$fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
		
			$validated_statuses = $fcrm_helpers->get_statuses();
		}

		$parameter = array(
			'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The old status for your given contact.', $translation_ident ) ),
			'contact' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the contact.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All user related details (In case a user exists for the given email).', $translation_ident ) ),
			'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full user meta (in case a user was given).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Contact status updated',
			'webhook_slug' => 'fcrm_contact_status_updated',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluentcrm_subscriber_status_to_subscribed',
					'url' => 'https://fluentcrm.com/docs/action-hooks/',
				),
				array( 
					'hook' => 'fluentcrm_subscriber_status_to_unsubscribed',
					'url' => 'https://fluentcrm.com/docs/action-hooks/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'In case you only want to fire this trigger on specific statuses, you can select specific ones within the webbhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'If you want to fire this trigger only for contacts that are assigned to a user, check "Trigger on users only" within the webbhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_fluent_crm_trigger_on_statuses' => array(
					'id'		  => 'wpwhpro_fluent_crm_trigger_on_statuses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_statuses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected statuses', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the statuses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
				'wpwhpro_fluent_crm_trigger_on_user_only' => array(
					'id'		  => 'wpwhpro_fluent_crm_trigger_on_user_only',
					'type'		=> 'checkbox',
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on users only', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Check this if you only want to fire this trigger when a WordPress user is connected to the contact email.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'fcrm_contact_status_updated',
			'name'			  => WPWHPRO()->helpers->translate( 'Contact status updated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a contact status was updated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a contact status gets updated within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'old_status' => 'bounced',
			'contact' => 
			array (
			  'id' => '1',
			  'user_id' => NULL,
			  'hash' => 'c152149c03dXXXXXX036edba08XXXXXX',
			  'contact_owner' => NULL,
			  'company_id' => NULL,
			  'prefix' => 'Mr',
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'email' => 'jon.doe@testdomain.test',
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
			  'date_of_birth' => '1999-11-11',
			  'created_at' => '2021-11-30 20:40:50',
			  'last_activity' => NULL,
			  'updated_at' => '2021-11-30 20:48:20',
			  'photo' => 'https://www.gravatar.com/avatar/c152149c03d10e23c036edba08f95775?s=128',
			  'full_name' => 'Jon Doe',
			),
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '72',
				'user_login' => 'jondoe',
				'user_pass' => 'XXXXXXXX/EfodvGzsU/OF3EhPoXXXXX/',
				'user_nicename' => 'jondoe',
				'user_email' => 'jon.doe@testdomain.test',
				'user_url' => '',
				'user_registered' => '2019-05-11 22:57:07',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 72,
			  'caps' => 
			  array (
				'leco_client' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				'read' => true,
				'level_0' => true,
			  ),
			  'filter' => NULL,
			),
			'user_meta' => 
			array (
			  'nickname' => 
			  array (
				0 => 'test',
			  ),
			  'first_name' => 
			  array (
				0 => '',
			  ),
			  'last_name' => 
			  array (
				0 => '',
			  ),
			  'description' => 
			  array (
				0 => '',
			  ),
			  'rich_editing' => 
			  array (
				0 => 'true',
			  ),
			  'syntax_highlighting' => 
			  array (
				0 => 'true',
			  ),
			  'comment_shortcuts' => 
			  array (
				0 => 'false',
			  ),
			  'admin_color' => 
			  array (
				0 => 'fresh',
			  ),
			),
		  );

		return $data;
	}

  }

endif; // End if class_exists check.