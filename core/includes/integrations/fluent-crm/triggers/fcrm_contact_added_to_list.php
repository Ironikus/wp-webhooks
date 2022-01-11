<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_added_to_list' ) ) :

 /**
  * Load the fcrm_contact_added_to_list trigger
  *
  * @since 4.3.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_added_to_list {

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
				'hook' => 'fluentcrm_contact_added_to_lists',
				'callback' => array( $this, 'fluentcrm_contact_added_to_lists_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-fcrm_contact_added_to_list-description";
		$validated_statuses = array();
		$validated_lists = array();

		if( defined( 'FLUENTCRM' ) ){
			$fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
		
			$validated_statuses = $fcrm_helpers->get_statuses();
			$validated_lists = $fcrm_helpers->get_lists();
		}
		

		$parameter = array(
			'list_ids' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All list IDs the contact has been assigned to within this request.', $translation_ident ) ),
			'contact' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the contact.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All user related details (In case a user exists for the given email).', $translation_ident ) ),
			'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full user meta (in case a user was given).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Contact added to list',
			'webhook_slug' => 'fcrm_contact_added_to_list',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluentcrm_contact_added_to_lists',
					'url' => 'https://fluentcrm.com/docs/action-hooks/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific lists only. To do that, select specific lists from the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'In case you only want to fire this trigger on specific statuses, you can select specific ones within the webbhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'If you want to fire this trigger only for contacts that are assigned to a user, check "Trigger on users only" within the webbhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_fluent_crm_trigger_on_lists' => array(
					'id'		  => 'wpwhpro_fluent_crm_trigger_on_lists',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_lists,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected lists', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the lists you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
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
			'trigger'		   => 'fcrm_contact_added_to_list',
			'name'			  => WPWHPRO()->helpers->translate( 'Contact added to list', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a contact was added to a list', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a contact is added to a list within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a contact was added to a list within FluentCRM
	 *
	 * @param array $attachedListIds The attached list ids or pivot IDs
	 * @param object|Subscriber $subscriber   The subscriber object
	 */
	public function fluentcrm_contact_added_to_lists_callback( $attached_list_ids, $contact ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'fcrm_contact_added_to_list' );
		$fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
		$list_ids = $fcrm_helpers->validate_list_ids( $attached_list_ids );
		$user_email = ( isset( $contact->email ) ) ? sanitize_email( $contact->email ) : '';
		$user = ( ! empty( $user_email ) ) ? get_user_by( 'email', $user_email ) : false;
		$status = ( isset( $contact->status ) ) ? $contact->status : '';

		$payload = array(
			'list_ids' => $list_ids,
			'contact' => $contact,
			'user' => $user,
			'user_meta' => ( ! empty( $user ) && isset( $user->ID ) ) ? get_user_meta( $user->ID ) : array(),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_lists'] ) && ! empty( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_lists'] ) ){
					if( is_array( $list_ids ) ){
						$is_valid = false;

						foreach( $list_ids as $list_id ){
							if( in_array( $list_id, $webhook['settings']['wpwhpro_fluent_crm_trigger_on_lists'] ) ){
								$is_valid = true;
							}
						}
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_statuses'] ) && ! empty( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_statuses'] ) ){
					if( ! in_array( $status, $webhook['settings']['wpwhpro_fluent_crm_trigger_on_statuses'] ) ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_user_only'] ) && ! empty( $webhook['settings']['wpwhpro_fluent_crm_trigger_on_user_only'] ) ){
					if( empty( $user ) || is_wp_error( $user ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_fcrm_contact_added_to_list', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'list_ids' => array(
				'2',
				'3'
			),
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