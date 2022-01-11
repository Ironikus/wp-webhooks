<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_restored' ) ) :

 /**
  * Load the wc_product_restored trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_restored {

	public function get_details(){

		$translation_ident = "trigger-wc_product_restored-description";
		$validated_api_versions = array();

		if( class_exists( 'WooCommerce' ) ){
			$wc_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce', 'wc_helpers' );
		
			$validated_api_versions = $wc_helpers->get_wc_api_versions();
		}
		

		$parameter = array(
			'custom' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom data construct from your chosen Woocommerce API.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Product restored',
			'webhook_slug' => 'wc_product_restored',
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
			'trigger'		   => 'wc_product_restored',
			'name'			  => WPWHPRO()->helpers->translate( 'Product restored', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a product was restored', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a product was restored within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'id' => 658,
			'name' => 'Demo Product',
			'slug' => 'demo-product',
			'permalink' => 'https://yourdomain.test/product/demo-product/',
			'date_created' => '2020-04-10T19:02:30',
			'date_created_gmt' => '2020-04-10T19:02:30',
			'date_modified' => '2021-12-28T07:11:22',
			'date_modified_gmt' => '2021-12-28T07:11:22',
			'type' => 'simple',
			'status' => 'publish',
			'featured' => false,
			'catalog_visibility' => 'visible',
			'description' => '',
			'short_description' => '',
			'sku' => '',
			'price' => '',
			'regular_price' => '',
			'sale_price' => '',
			'date_on_sale_from' => NULL,
			'date_on_sale_from_gmt' => NULL,
			'date_on_sale_to' => NULL,
			'date_on_sale_to_gmt' => NULL,
			'on_sale' => false,
			'purchasable' => false,
			'total_sales' => 0,
			'virtual' => false,
			'downloadable' => false,
			'downloads' => 
			array (
			),
			'download_limit' => -1,
			'download_expiry' => -1,
			'external_url' => '',
			'button_text' => '',
			'tax_status' => 'taxable',
			'tax_class' => '',
			'manage_stock' => false,
			'stock_quantity' => NULL,
			'in_stock' => true,
			'backorders' => 'no',
			'backorders_allowed' => false,
			'backordered' => false,
			'sold_individually' => false,
			'weight' => '',
			'dimensions' => 
			array (
			  'length' => '',
			  'width' => '',
			  'height' => '',
			),
			'shipping_required' => true,
			'shipping_taxable' => true,
			'shipping_class' => '',
			'shipping_class_id' => 0,
			'reviews_allowed' => true,
			'average_rating' => '0.00',
			'rating_count' => 0,
			'upsell_ids' => 
			array (
			),
			'cross_sell_ids' => 
			array (
			),
			'parent_id' => 0,
			'purchase_note' => '',
			'categories' => 
			array (
			  0 => 
			  array (
				'id' => 34,
				'name' => 'Uncategorized',
				'slug' => 'uncategorized',
			  ),
			),
			'tags' => 
			array (
			),
			'images' => 
			array (
			  0 => 
			  array (
				'id' => 0,
				'date_created' => '2021-12-28T07:11:22',
				'date_created_gmt' => '2021-12-28T07:11:22',
				'date_modified' => '2021-12-28T07:11:22',
				'date_modified_gmt' => '2021-12-28T07:11:22',
				'src' => 'https://yourdomain.test/wp-content/uploads/woocommerce-placeholder.png',
				'name' => 'Placeholder',
				'alt' => 'Placeholder',
				'position' => 0,
			  ),
			),
			'attributes' => 
			array (
			),
			'default_attributes' => 
			array (
			),
			'variations' => 
			array (
			),
			'grouped_products' => 
			array (
			),
			'menu_order' => 0,
			'price_html' => '',
			'related_ids' => 
			array (
			  0 => 156,
			  1 => 8096,
			  2 => 604,
			  3 => 155,
			  4 => 659,
			),
			'meta_data' => 
			array (
			),
			'_links' => 
			array (
			  'self' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v2/products/658',
				),
			  ),
			  'collection' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v2/products',
				),
			  ),
			),
			'wpwh_meta_data' => 
			array (
				'_edit_lock' => 
				array (
				0 => '1641462455:1',
				),
				'_edit_last' => 
				array (
				0 => '1',
				),
				'total_sales' => 
				array (
				0 => '0',
				),
				'_tax_status' => 
				array (
				0 => 'taxable',
				),
				'_tax_class' => 
				array (
				0 => '',
				),
				'_manage_stock' => 
				array (
				0 => 'no',
				),
				'_backorders' => 
				array (
				0 => 'no',
				),
				'_sold_individually' => 
				array (
				0 => 'no',
				),
				'_virtual' => 
				array (
				0 => 'no',
				),
				'_downloadable' => 
				array (
				0 => 'no',
				),
				'_download_limit' => 
				array (
				0 => '-1',
				),
				'_download_expiry' => 
				array (
				0 => '-1',
				),
				'_stock' => 
				array (
				0 => NULL,
				),
				'_stock_status' => 
				array (
				0 => 'instock',
				),
				'_wc_average_rating' => 
				array (
				0 => '0',
				),
				'_wc_review_count' => 
				array (
				0 => '0',
				),
				'_product_version' => 
				array (
				0 => '6.0.0',
				),
			),
			'wpwh_tax_data' => 
			array (
				'product_cat' => 
				array (
				'demo-category' => 
				array (
					'term_id' => 79,
					'name' => 'Demo Category',
					'slug' => 'demo-category',
					'term_group' => 0,
					'term_taxonomy_id' => 79,
					'taxonomy' => 'product_cat',
					'description' => '',
					'parent' => 0,
					'count' => 0,
					'filter' => 'raw',
				),
				'demo-category-2' => 
				array (
					'term_id' => 80,
					'name' => 'Demo Category 2',
					'slug' => 'demo-category-2',
					'term_group' => 0,
					'term_taxonomy_id' => 80,
					'taxonomy' => 'product_cat',
					'description' => '',
					'parent' => 0,
					'count' => 0,
					'filter' => 'raw',
				),
				),
				'product_type' => 
				array (
				'simple' => 
				array (
					'term_id' => 21,
					'name' => 'simple',
					'slug' => 'simple',
					'term_group' => 0,
					'term_taxonomy_id' => 21,
					'taxonomy' => 'product_type',
					'description' => '',
					'parent' => 0,
					'count' => 5,
					'filter' => 'raw',
				),
				),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.