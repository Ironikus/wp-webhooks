<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_affiliate_status_change' ) ) :

 /**
  * Load the affwp_affiliate_status_change trigger
  *
  * @since 4.2.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_affiliate_status_change {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'affwp_set_affiliate_status',
		'callback' => array( $this, 'affwp_set_affiliate_status_callback' ),
		'priority' => 20,
		'arguments' => 3,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

	  $translation_ident = "trigger-affwp_affiliate_status_change-description";

	  $parameter = array(
		'affiliate_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The affiliate id of the affiliate that the status was changed of.', $translation_ident ) ),
		'affiliate' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data of the affiliate.', $translation_ident ) ),
		'status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The new affiliate status.', $translation_ident ) ),
		'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The old affiliate status.', $translation_ident ) ),
		'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the connected user.', $translation_ident ) ),
		'user' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user data of the connected user.', $translation_ident ) ),
		'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta data of the connected user.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Affiliate status changed',
			'webhook_slug' => 'affwp_affiliate_status_change',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'affwp_set_affiliate_status',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => false,
		'data' => array(
		  'wpwhpro_affwp_affiliate_status_change_trigger_on_status' => array(
			'id'	 => 'wpwhpro_affwp_affiliate_status_change_trigger_on_status',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(
				'all' => WPWHPRO()->helpers->translate( 'All', $translation_ident ),
				'active' => WPWHPRO()->helpers->translate( 'Active', $translation_ident ),
				'inactive' => WPWHPRO()->helpers->translate( 'Inactive', $translation_ident ),
				'pending' => WPWHPRO()->helpers->translate( 'Pending', $translation_ident ),
				'rejected' => WPWHPRO()->helpers->translate( 'Rejected', $translation_ident ),
			),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected affiliate status', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the affiliate status you want to fire the trigger on. If none is selected, all are triggered.', $translation_ident )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'affwp_affiliate_status_change',
		'name'	   => WPWHPRO()->helpers->translate( 'Affiliate status changed', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'an affiliate status was changed', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after an affiliate status was changed within AffiliateWP.', $translation_ident ),
		'description'	=> $description,
		'integration'	=> 'affiliatewp',
		'premium'	=> false,
	  );

	}

	public function affwp_set_affiliate_status_callback( $affiliate_id, $status, $old_status ){

		//Abort if nothing happened
		if( $status === $old_status ){
			return;
		}

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'affwp_affiliate_status_change' );
	  $affiliate = affwp_get_affiliate( $affiliate_id );
	  $user_id = ( is_object( $affiliate ) && isset( $affiliate->user_id ) ) ? intval( $affiliate->user_id ) : 0;
	  $user = array();
	  $user_meta = array();
	  $data_array = array(
		'affiliate_id' => $affiliate_id,
		'affiliate' => $affiliate,
		'status' => $status,
		'old_status' => $old_status,
	  );
	  $response_data = array();

	  if( ! empty( $user_id ) ){
		$user = get_user_by( 'id', $user_id );
		$user_meta = get_user_meta( $user_id );
	  }
	  $data_array['user_id'] = $user_id;
	  $data_array['user'] = $user;
	  $data_array['user_meta'] = $user_meta;

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_affwp_affiliate_status_change_trigger_on_status' && ! empty( $settings_data ) ){
			  if( ! in_array( $status, $settings_data ) && ! in_array( 'all', $settings_data ) ){
				$is_valid = false;
			  }
			}

		  }
		}

		if( $is_valid ) {
		  $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

		  if( $webhook_url_name !== null ){
			$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  } else {
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  }
		}
	  }

	  do_action( 'wpwhpro/webhooks/trigger_affwp_affiliate_status_change', $data_array, $response_data );
	}

	public function get_demo( $options = array() ) {

	  $data = array (
		'affiliate_id' => 8,
		'affiliate' => 
		array (
		  'affiliate_id' => 8,
		  'rest_id' => '',
		  'user_id' => 141,
		  'rate' => '',
		  'rate_type' => '',
		  'flat_rate_basis' => '',
		  'payment_email' => 'payment@email.test',
		  'status' => 'active',
		  'earnings' => 0,
		  'unpaid_earnings' => 0,
		  'referrals' => 0,
		  'visits' => 0,
		  'date_registered' => '2021-08-25 16:07:09',
		),
		'status' => 'active',
		'old_status' => 'pending',
		'user_id' => 141,
		'user' => 
		array (
		  'data' => 
		  array (
			'ID' => '141',
			'user_login' => 'demouser',
			'user_pass' => '$P$BS4efXdGHf9vUHHHHHz39v0BSIGnCB1',
			'user_nicename' => 'demouser',
			'user_email' => 'demo@user.test',
			'user_url' => '',
			'user_registered' => '2021-05-28 14:22:48',
			'user_activation_key' => '',
			'user_status' => '0',
			'display_name' => 'demouser',
			'spam' => '0',
			'deleted' => '0',
		  ),
		  'ID' => 141,
		  'caps' => 
		  array (
			'subscriber' => true,
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
			'subscriber' => true,
		  ),
		  'filter' => NULL,
		),
		'user_meta' => 
		array (
		  'nickname' => 
		  array (
			0 => 'demouser',
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
		  'use_ssl' => 
		  array (
			0 => '0',
		  ),
		  'show_admin_bar_front' => 
		  array (
			0 => 'true',
		  ),
		  'locale' => 
		  array (
			0 => '',
		  ),
		  'wp_capabilities' => 
		  array (
			0 => 'a:1:{s:10:"subscriber";b:1;}',
		  ),
		  'wp_user_level' => 
		  array (
			0 => '0',
		  ),
		  'first_custom_key' => 
		  array (
			0 => 'Some custom value',
			1 => 'Some custom value',
		  ),
		  'second_custom_key' => 
		  array (
			0 => 'a:1:{s:14:"some_array_key";s:16:"Some array Value";}',
		  ),
		  'affwp_referral_notifications' => 
		  array (
			0 => '1',
		  ),
		  'last_update' => 
		  array (
			0 => '1629911663',
		  ),
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.