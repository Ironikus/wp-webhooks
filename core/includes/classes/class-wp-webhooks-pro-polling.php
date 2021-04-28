<?php

/**
 * WP_Webhooks_Pro_Polling Class
 *
 * This class contains the whole polling functionality
 * Polling is used, e.g. for Zapier to watch certain data on 
 * your website. For polling, a third party service can check on new ocurances of data
 * on your website ad do certain actions based on it.  
 *
 * @since 1.1.1
 */

/**
 * The whitelist class of the plugin.
 *
 * @since 1.1.1
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Polling {

	/**
	 * WP_Webhooks_Pro_Polling constructor.
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * The main function for adding our WordPress related hooks
	 *
	 * @return void
	 */
	public function add_hooks(){

		add_filter( 'wpwhpro/webhooks/add_webhook_actions', array( $this, 'add_webhook_action_pollings' ), 10, 4 );

	}

	/**
	 * Initialize the polling handler with every single 
	 * polling function
	 *
	 * @param string $action - The currently called action
	 * @return void
	 */
	public function add_webhook_action_pollings( $response, $action, $response_ident_value, $response_api_key ){

		if( strpos( $action, 'polling_' ) === FALSE ){
			return $response;
		}

		switch( $action ){
			case 'polling_user':
				$response = $this->poll_users();
			break;
		}

		return $response;
	}

	/**
	 * The main uer Poll function. 
	 * It contains all the logic for polling users
	 *
	 * @return void
	 */
	private function poll_users(){

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array();

		$page = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'page' );
		$role_type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'role_type' );
		$roles = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'roles' );
		$search = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'search' );
		$search_columns = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'search_columns' );

		if( ! is_numeric( $page ) ){
			$page = 1;
		} else {
			$page = intval( $page );
		}

		$arguments = array(
			'number' => 10,
			'paged' => $page,
			'orderby' => 'registered'
		);

		if( ! empty( $roles ) ){
			switch( $role_type ){
				case 'role':
				$arguments['role'] = array_map( 'trim', explode( ',', $roles ) );
					break;
				case 'role__in':
				$arguments['role__in'] = array_map( 'trim', explode( ',', $roles ) );
					break;
				case 'role__not_in':
				$arguments['role__not_in'] = array_map( 'trim', explode( ',', $roles ) );
					break;
			}
		}

		if( ! empty( $search ) ){
			$arguments['search'] = esc_html( $search );
		}

		if( ! empty( $search_columns ) ){
			$arguments['search_columns'] = array_map( 'esc_html', array_map( 'trim', explode( ',', $search_columns ) ) );
		}

		$user_query = new WP_User_Query( $arguments );

		if( ! empty( $user_query ) ){
			$user_results = $user_query->get_results();

			foreach( $user_results as $urk => $urv ){
				$user_results[ $urk ] = (array) $urv;
				$user_results[ $urk ]['id'] = $urv->ID;
			}

			$return_args = $user_results;
		}

		return $return_args;

	}
}
