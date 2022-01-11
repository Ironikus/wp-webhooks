<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_file_downloaded' ) ) :

 /**
  * Load the edd_file_downloaded trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_file_downloaded {

	public function is_active(){

		$is_active = true;

		//Backwards compatibility
		if( defined( 'WPWH_EDD_NAME' ) ){
			$is_active = false;
		}

		return $is_active;
	}

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
				'hook' => 'edd_process_verified_download',
				'callback' => array( $this, 'wpwh_trigger_edd_file_downloaded' ),
				'priority' => 10,
				'arguments' => 4,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-edd_file_downloaded-description";

		$choices = array();
		if( function_exists( 'edd_get_payment_statuses' ) ){
			$choices = edd_get_payment_statuses();

			//add our custom delete status
			$choices['wpwh_deleted'] = WPWHPRO()->helpers->translate( 'Deleted', $translation_ident );
		}

		$parameter = array(
			'file_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The file name without the file extension.', $translation_ident ) ),
			'file' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full file URL.', $translation_ident ) ),
			'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer who started the download.', $translation_ident ) ),
			'product' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The product name wich contains the download.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'File downloaded',
			'webhook_slug' => 'edd_file_downloaded',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'edd_process_verified_download',
					'url' => 'https://docs.easydigitaldownloads.com/article/518-edd-process-verified-download',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
		);

		return array(
			'trigger'		   => 'edd_file_downloaded',
			'name'			  => WPWHPRO()->helpers->translate( 'File downloaded', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a file was downloaded', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires once a file download is initiated within Easy Digital Downloads.', $translation_ident ),
			'description'	   => $description,
			'callback'		  => 'test_edd_file_downloaded',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD file was downloaded
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_file_downloaded( $download_id, $email, $payment_id, $args ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_file_downloaded' );

		$data  = array();
		$files = edd_get_download_files( $download_id );

		$data['file_name'] = $files[ $args['file_key'] ]['name'];
		$data['file']	  = $files[ $args['file_key'] ]['file'];
		$data['email']	 = $email;
		$data['product']   = get_the_title( $download_id );

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			}
			
		}

		do_action( 'wpwhpro/webhooks/trigger_edd_file_downloaded', $download_id, $email, $payment_id, $args, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'file_name' => 'sample_file_name',
			'file'	  => home_url( 'sample/file/url/file.zip' ),
			'email'	 => 'jane@test.com',
			'product'   => 'Sample Product',
		);

		return $data;
	}

  }

endif; // End if class_exists check.