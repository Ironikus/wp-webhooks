<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_coupon_restored' ) ) :

 /**
  * Load the wc_coupon_restored trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_coupon_restored {

	public function get_details(){

		$translation_ident = "trigger-wc_coupon_restored-description";
		$validated_api_versions = array();

		if( class_exists( 'WooCommerce' ) ){
			$wc_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce', 'wc_helpers' );
		
			$validated_api_versions = $wc_helpers->get_wc_api_versions();
		}
		

		$parameter = array(
			'custom' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom data construct from your chosen Woocommerce API.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Coupon restored',
			'webhook_slug' => 'wc_coupon_restored',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'untrashed_post',
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
			'trigger'		   => 'wc_coupon_restored',
			'name'			  => WPWHPRO()->helpers->translate( 'Coupon restored', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a coupon was restored', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a coupon was restored within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'id' => 8090,
			'code' => 'demo coupon',
			'date_created' => '2021-12-27T19:47:26',
			'date_modified' => '2021-12-27T19:56:16',
			'discount_type' => 'percent',
			'description' => 'This is a demo coupon',
			'amount' => '10.00',
			'expiry_date' => '2021-12-31T00:00:00',
			'usage_count' => 0,
			'individual_use' => true,
			'product_ids' => 
			array (
			  0 => 658,
			),
			'exclude_product_ids' => 
			array (
			),
			'usage_limit' => 1000,
			'usage_limit_per_user' => 1,
			'limit_usage_to_x_items' => NULL,
			'free_shipping' => true,
			'product_categories' => 
			array (
			),
			'excluded_product_categories' => 
			array (
			),
			'exclude_sale_items' => false,
			'minimum_amount' => '10.00',
			'maximum_amount' => '500.00',
			'email_restrictions' => 
			array (
			),
			'used_by' => 
			array (
			),
			'_links' => 
			array (
			  'self' => 
			  array (
				0 => 
				array (
				  'href' => 'https://zipfme.test/wp-json/wc/v1/coupons/8090',
				),
			  ),
			  'collection' => 
			  array (
				0 => 
				array (
				  'href' => 'https://zipfme.test/wp-json/wc/v1/coupons',
				),
			  ),
			),
			'wpwh_meta_data' => 
			array (
				'_edit_lock' => 
				array (
				0 => '1641462745:1',
				),
				'_edit_last' => 
				array (
				0 => '1',
				),
				'discount_type' => 
				array (
				0 => 'percent',
				),
				'coupon_amount' => 
				array (
				0 => '10',
				),
				'individual_use' => 
				array (
				0 => 'yes',
				),
				'product_ids' => 
				array (
				0 => '658',
				),
				'usage_limit' => 
				array (
				0 => '1000',
				),
				'usage_limit_per_user' => 
				array (
				0 => '1',
				),
				'limit_usage_to_x_items' => 
				array (
				0 => '0',
				),
				'usage_count' => 
				array (
				0 => '0',
				),
				'date_expires' => 
				array (
				0 => '1640908800',
				),
				'free_shipping' => 
				array (
				0 => 'yes',
				),
				'exclude_sale_items' => 
				array (
				0 => 'no',
				),
				'minimum_amount' => 
				array (
				0 => '10',
				),
				'maximum_amount' => 
				array (
				0 => '500',
				),
				'_wp_old_slug' => 
				array (
				0 => 'demo-coupon-2__trashed',
				),
			),
			'wpwh_tax_data' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.