<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_customer_created' ) ) :

 /**
  * Load the wc_customer_created trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_customer_created {

	public function get_details(){

		$translation_ident = "trigger-wc_customer_created-description";
		$validated_api_versions = array();

		if( class_exists( 'WooCommerce' ) ){
			$wc_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce', 'wc_helpers' );
		
			$validated_api_versions = $wc_helpers->get_wc_api_versions();
		}
		

		$parameter = array(
			'custom' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom data construct from your chosen Woocommerce API.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Customer created',
			'webhook_slug' => 'wc_customer_created',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'user_register',
				),
				array( 
					'hook' => 'woocommerce_created_customer',
				),
				array( 
					'hook' => 'woocommerce_new_customer',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'Please make sure to set the user id setting within the webhook URL. This setting allows our webhook to request the original payload from the REST API, just as Woocommerce does.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on a specific Woocommerce API version. To do that, select a version within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'You can also set a custom secret key just as for the default Woocommerce webhooks. IF you do not set one, there will be one automatically generated.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_set_user' => array(
					'id'		  => 'wpwhpro_woocommerce_set_user',
					'type'		=> 'text',
					'label'	   => WPWHPRO()->helpers->translate( 'Set user id', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set the id of a user that has permission to view the Woocommerce REST API. If you do not set a valid user id, the response will not be verified.', $translation_ident )
				),
				'wpwhpro_woocommerce_set_api_version' => array(
					'id'		  => 'wpwhpro_woocommerce_set_api_version',
					'type'		=> 'select',
					'multiple'	=> false,
					'choices'	  => $validated_api_versions,
					'label'	   => WPWHPRO()->helpers->translate( 'Set API version', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'default_value'	=> 'wp_api_v2',
					'description' => WPWHPRO()->helpers->translate( 'Select the Woocommerce API version you want to use for this request. By default, we use wp_api_v2', $translation_ident )
				),
				'wpwhpro_woocommerce_set_secret' => array(
					'id'		  => 'wpwhpro_woocommerce_set_secret',
					'type'		=> 'text',
					'label'	   => WPWHPRO()->helpers->translate( 'Set secret', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a custom secret that gets validated by Woocommerce, just as you know it from the default Woocommerce webhooks.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wc_customer_created',
			'name'			  => WPWHPRO()->helpers->translate( 'Customer created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a customer was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a customer was created within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'id' => 150,
			'date_created' => '2021-12-27T20:07:10',
			'date_modified' => '2021-12-27T20:07:12',
			'email' => 'demo@yourdomain.test',
			'first_name' => 'Jon',
			'last_name' => 'Doe',
			'username' => 'jon.doe',
			'last_order' => 
			array (
			  'id' => 8092,
			  'date' => '2021-12-27T20:07:11',
			),
			'orders_count' => 1,
			'total_spent' => '0.00',
			'avatar_url' => 'https://secure.gravatar.com/avatar/ac0153ebc7286731000000000000?s=96&d=mm&r=g',
			'billing' => 
			array (
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'company' => 'Doe Corp',
			  'address_1' => 'Demo St. 5',
			  'address_2' => '',
			  'city' => 'Demo City',
			  'postcode' => '12345',
			  'country' => 'DE',
			  'state' => '',
			  'email' => 'demo@yourdomain.test',
			  'phone' => '123456789',
			),
			'shipping' => 
			array (
			  'first_name' => '',
			  'last_name' => '',
			  'company' => '',
			  'address_1' => '',
			  'address_2' => '',
			  'city' => '',
			  'postcode' => '',
			  'country' => '',
			  'state' => '',
			  'phone' => '',
			),
			'_links' => 
			array (
			  'self' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v1/customers/150',
				),
			  ),
			  'collection' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v1/customers',
				),
			  ),
			),
			'wpwh_meta_data' => 
			array (
				'nickname' => 
				array (
				0 => 'jon.doe',
				),
				'first_name' => 
				array (
				0 => 'Jon',
				),
				'last_name' => 
				array (
				0 => 'Doe',
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
				'zipf_capabilities' => 
				array (
				0 => 'a:1:{s:8:"customer";b:1;}',
				),
				'zipf_user_level' => 
				array (
				0 => '0',
				),
				'last_update' => 
				array (
				0 => '1641461719',
				),
				'session_tokens' => 
				array (),
				'billing_first_name' => 
				array (
				0 => 'Jon',
				),
				'billing_last_name' => 
				array (
				0 => 'Doe',
				),
				'billing_company' => 
				array (
				0 => 'Demo Corp',
				),
				'billing_address_1' => 
				array (
				0 => 'Demo St. 55',
				),
				'billing_city' => 
				array (
				0 => 'Demo City',
				),
				'billing_postcode' => 
				array (
				0 => '12345',
				),
				'billing_country' => 
				array (
				0 => 'DE',
				),
				'billing_email' => 
				array (
				0 => 'demouser@yourdomain.test',
				),
				'billing_phone' => 
				array (
				0 => '123456789',
				),
				'shipping_method' => 
				array (
				0 => '',
				),
				'wc_last_active' => 
				array (
				0 => '1640649600',
				),
				'paying_customer' => 
				array (
				0 => '1',
				),
				'_last_order' => 
				array (
				0 => '8095',
				),
				'_order_count' => 
				array (
				0 => '1',
				),
				'_money_spent' => 
				array (
				0 => '0',
				),
				'billing_address_2' => 
				array (
				0 => '',
				),
				'billing_state' => 
				array (
				0 => '',
				),
				'shipping_first_name' => 
				array (
				0 => '',
				),
				'shipping_last_name' => 
				array (
				0 => '',
				),
				'shipping_company' => 
				array (
				0 => '',
				),
				'shipping_address_1' => 
				array (
				0 => '',
				),
				'shipping_address_2' => 
				array (
				0 => '',
				),
				'shipping_city' => 
				array (
				0 => '',
				),
				'shipping_postcode' => 
				array (
				0 => '',
				),
				'shipping_country' => 
				array (
				0 => '',
				),
				'shipping_state' => 
				array (
				0 => '',
				),
				'shipping_phone' => 
				array (
				0 => '',
				),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.