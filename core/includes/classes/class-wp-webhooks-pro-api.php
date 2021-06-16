<?php

/**
 * WP_Webhooks_Pro_API Class
 *
 * This class contains all of the available api functions
 *
 * @since 2.0.3
 */

/**
 * The api class of the plugin.
 *
 * @since 2.0.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_API {

	/**
	 * This is the main page for handling api requests
	 * @var string
	 */
	protected $api_url = 'https://wp-webhooks.com';

	/**
	 * ################################
	 * ###
	 * ##### EXTENSION LIST
	 * ###
	 * ################################
	 */

	/**
	 * Get a list of all available extensions
	 *
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_extension_list(){

		$extensions_transient = get_transient( WPWHPRO()->settings->get_extensions_transient_key() );

		if( empty ( $extensions_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$extensions = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/extensions/list/', 'body' );

			if(!empty($extensions)){
				$extensions             = ! empty( $extensions ) ? json_decode( $extensions, true ) : '' ;
				$extensions             = ( is_array( $extensions ) && $extensions['success'] == true ) ? $extensions['data'] : '' ;

				set_transient( WPWHPRO()->settings->get_extensions_transient_key(), $extensions, strtotime('1 day', 0) );

				return $extensions;
			} else {
				return false;
			}

		} else {
			return $extensions_transient;
		}

	}

}
