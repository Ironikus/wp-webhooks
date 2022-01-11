<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_referral_status_change' ) ) :

 /**
  * Load the affwp_referral_status_change trigger
  *
  * @since 4.2.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_referral_status_change {

	public function get_details(){

	  $translation_ident = "trigger-affwp_referral_status_change-description";
	  $validated_statuses = array();

	  if( function_exists( 'affwp_get_referral_statuses' ) ){
		$validated_statuses = affwp_get_referral_statuses();
	  }

	  $parameter = array(
		'referral_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the referral that the status was changed of.', $translation_ident ) ),
		'referral' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Further data about the referral.', $translation_ident ) ),
		'status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The new referral status.', $translation_ident ) ),
		'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The old referral status.', $translation_ident ) ),
		'affiliate_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the connected affiliate.', $translation_ident ) ),
		'affiliate' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data of the affiliate.', $translation_ident ) ),
		'third-party' => array( 'short_description' => WPWHPRO()->helpers->translate( 'In case you use AffiliateWP with various third-party integrations, you will find additional data here.', $translation_ident ) ),
		'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the connected user.', $translation_ident ) ),
		'user' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user data of the connected user.', $translation_ident ) ),
		'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta data of the connected user.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Referral status changed',
			'webhook_slug' => 'affwp_referral_status_change',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'affwp_set_referral_status',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => false,
		'data' => array(
		  'wpwhpro_affwp_referral_status_change_trigger_on_status' => array(
			'id'	 => 'wpwhpro_affwp_referral_status_change_trigger_on_status',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => $validated_statuses,
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected referral status', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the referral status you want to fire the trigger on. If none is selected, all are triggered.', $translation_ident )
		  ),
		  'wpwhpro_affwp_referral_status_change_trigger_on_third_party' => array(
			'id'	 => 'wpwhpro_affwp_referral_status_change_trigger_on_third_party',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected third-party integrations', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the third-party integrations for this referral you want to fire the trigger on. If none is selected, all are triggered. Please note: In case you do not see a specific extension here, make sure you activated it within the settings of AffiliateWP first.', $translation_ident )
		  ),
		)
	  );

	  if( function_exists( 'affiliate_wp' ) ){
		$integrations = affiliate_wp()->integrations->get_integrations();
		$integrations_enabled = affiliate_wp()->integrations->get_enabled_integrations();
		foreach( $integrations_enabled as $slug => $data ){
		  $integration_name = isset( $integrations[ $slug ] ) ? $integrations[ $slug ] : $slug;
		  $settings['data']['wpwhpro_affwp_referral_status_change_trigger_on_third_party']['choices'][ $slug ] = $integration_name;
		}
	}

	if( empty( $settings['data']['wpwhpro_affwp_referral_status_change_trigger_on_third_party']['choices'] ) ){
		unset( $settings['data']['wpwhpro_affwp_referral_status_change_trigger_on_third_party'] );
	}

	  return array(
		'trigger'	  => 'affwp_referral_status_change',
		'name'	   => WPWHPRO()->helpers->translate( 'Referral status changed', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a referral status changed', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after an referral status was changed within AffiliateWP.', $translation_ident ),
		'description'	=> $description,
		'integration'	=> 'affiliatewp',
		'premium'	=> true,
	  );

	}

	public function get_demo( $options = array() ) {

	  $data = array (
		'referral_id' => 8,
		'referral' => 
		array (
		  'referral_id' => 8,
		  'affiliate_id' => 5,
		  'visit_id' => 0,
		  'rest_id' => '',
		  'customer_id' => '0',
		  'parent_id' => 0,
		  'description' => '',
		  'status' => 'pending',
		  'amount' => '16.00',
		  'currency' => '',
		  'custom' => '',
		  'context' => '',
		  'campaign' => '',
		  'reference' => '',
		  'products' => '',
		  'date' => '2021-08-25 20:47:10',
		  'type' => 'opt-in',
		  'payout_id' => '0',
		),
		'affiliate_id' => 5,
		'affiliate' => 
		array (
		  'affiliate_id' => 5,
		  'rest_id' => '',
		  'user_id' => 97,
		  'rate' => '20',
		  'rate_type' => 'percentage',
		  'flat_rate_basis' => '',
		  'payment_email' => 'payment@email.com',
		  'status' => 'active',
		  'earnings' => 37,
		  'unpaid_earnings' => 25,
		  'referrals' => 2,
		  'visits' => 0,
		  'date_registered' => '2021-08-25 16:01:01',
		),
		'status' => 'pending',
		'old_status' => 'rejected',
		'user_id' => 97,
		'user' => 
		array (
		  'data' => 
		  array (
			'ID' => '97',
			'user_login' => 'profile1',
			'user_pass' => '$P$Bgt27hhP2HHHHHHHIDtLWPPq3AH81E1',
			'user_nicename' => 'profile1',
			'user_email' => 'demo@email.test',
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