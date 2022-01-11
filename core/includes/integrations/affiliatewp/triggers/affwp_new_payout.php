<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_payout' ) ) :

 /**
  * Load the affwp_new_payout trigger
  *
  * @since 4.2.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_payout {

	public function get_details(){

	  $translation_ident = "trigger-affwp_new_payout-description";

	  $parameter = array(
		'payout_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the created payout.', $translation_ident ) ),
		'payout' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Further data about the payout.', $translation_ident ) ),
		'affiliate_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The affiliate id of the related affiliate.', $translation_ident ) ),
		'affiliate' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Further data about the affiliate.', $translation_ident ) ),
		'user' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user details about the assigned user.', $translation_ident ) ),
		'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The meta data of the assigned user.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'New Payout',
			'webhook_slug' => 'affwp_new_payout',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'affwp_insert_payout',
				),
			)
		) );

	  	$settings = array(
			'load_default_settings' => false,
	  	);

	  return array(
		'trigger'	  => 'affwp_new_payout',
		'name'	   => WPWHPRO()->helpers->translate( 'New payout', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a new payout was received', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a new payout was received within AffiliateWP.', $translation_ident ),
		'description'	=> $description,
		'integration'	=> 'affiliatewp',
		'premium'	=> true,
	  );

	}

	public function get_demo( $options = array() ) {

	$data = array (
		'payout_id' => 4,
		'payout' => 
		array (
			'payout_id' => 4,
			'affiliate_id' => 5,
			'referrals' => '5',
			'amount' => 20,
			'payout_method' => 'manual',
			'status' => 'paid',
			'date' => '2021-08-25 21:13:37',
			'owner' => 1,
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
		'user' => 
		array (
			'data' => 
			array (
			'ID' => '97',
			'user_login' => 'profile1',
			'user_pass' => '$P$Bgt27hhHHHHHHHHGIDtLWPPq3AH81E1',
			'user_nicename' => 'profile1',
			'user_email' => 'useremail@demo.test',
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