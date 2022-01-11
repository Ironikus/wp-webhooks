<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_affiliate' ) ) :

 /**
  * Load the affwp_new_affiliate trigger
  *
  * @since 4.2.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_affiliate {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'affwp_insert_affiliate',
		'callback' => array( $this, 'affwp_insert_affiliate_callback' ),
		'priority' => 20,
		'arguments' => 3,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

	  $translation_ident = "trigger-affwp_new_affiliate-description";
	  $validated_statuses = array();

	  if( function_exists( 'affwp_get_affiliate_statuses' ) ){
		  $validated_statuses = affwp_get_affiliate_statuses();
	  }

	  $parameter = array(
		'affiliate_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The affiliate id of the created affiliate.', $translation_ident ) ),
		'status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The status of the currently created affiliate.', $translation_ident ) ),
		'args' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Further information about the affiliate.', $translation_ident ) ),
		'user' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user details about the assigned user.', $translation_ident ) ),
		'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The meta data of the assigned user.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'New Affiliate',
			'webhook_slug' => 'affwp_new_affiliate',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'affwp_insert_affiliate',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => false,
		'data' => array(
		  'wpwhpro_affwp_new_affiliate_trigger_on_status' => array(
			'id'	 => 'wpwhpro_affwp_new_affiliate_trigger_on_status',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => $validated_statuses,
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected affiliate status', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the affiliate status you want to fire the trigger on.', $translation_ident )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'affwp_new_affiliate',
		'name'	   => WPWHPRO()->helpers->translate( 'New affiliate', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a new affiliate signed up', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a new affiliate signed up within AffiliateWP.', $translation_ident ),
		'description'	=> $description,
		'integration'	=> 'affiliatewp',
		'premium'	=> false,
	  );

	}

	public function affwp_insert_affiliate_callback( $add, $args ){

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'affwp_new_affiliate' );
	  $status = isset( $args['status'] ) ? $args['status'] : false;
	  $user_id = isset( $args['user_id'] ) ? $args['user_id'] : 0;
	  $user = array();
	  $user_meta = array();
	  $data_array = array(
		'affiliate_id' => $add,
		'status' => $status,
		'args' => $args,
	  );
	  $response_data = array();

	  if( ! empty( $user_id ) ){
		$user = get_user_by( 'id', $user_id );
		$user_meta = get_user_meta( $user_id );
	  }
	  $data_array['user'] = $user;
	  $data_array['user_meta'] = $user_meta;

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_affwp_new_affiliate_trigger_on_status' && ! empty( $settings_data ) ){
			  if( ! in_array( $status, $settings_data ) ){
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

	  do_action( 'wpwhpro/webhooks/trigger_affwp_new_affiliate', $data_array, $response_data );
	}

	public function get_demo( $options = array() ) {

	  $data = array (
		'affiliate_id' => 5,
		'status' => 'active',
		'args' => 
		array (
		  'status' => 'active',
		  'earnings' => 0,
		  'referrals' => 0,
		  'visits' => 0,
		  'user_id' => 97,
		  'rate' => '20',
		  'rate_type' => 'percentage',
		  'flat_rate_basis' => '',
		  'payment_email' => 'payment@email.com',
		  'notes' => '',
		  'website_url' => '',
		),
		'user' => 
		array (
		  'data' => 
		  array (
			'ID' => '97',
			'user_login' => 'profile1',
			'user_pass' => '$P$Bgt27hhP2U56OHHHHDtLWPPq3AH81E1',
			'user_nicename' => 'profile1',
			'user_email' => 'user@email.test',
			'user_url' => '',
			'user_registered' => '2019-09-26 23:03:37',
			'user_activation_key' => '',
			'user_status' => '0',
			'display_name' => 'profile1',
			'spam' => '0',
			'deleted' => '0',
		  ),
		  'ID' => 97,
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
			0 => 'profile1',
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
		  'dismissed_wp_pointers' => 
		  array (
			0 => '',
		  ),
		  'primary_blog' => 
		  array (
			0 => '1',
		  ),
		  'source_domain' => 
		  array (
			0 => 'wpme.dev',
		  ),
		  'wp_capabilities' => 
		  array (
			0 => 'a:1:{s:10:"subscriber";b:1;}',
		  ),
		  'wp_user_level' => 
		  array (
			0 => '0',
		  ),
		  'affwp_referral_notifications' => 
		  array (
			0 => '1',
		  ),
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.