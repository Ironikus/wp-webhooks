<?php

/**
 * WP_Webhooks_Webhook Class
 *
 * This class contains all of the available api functions
 *
 * @since 1.0.0
 */

/**
 * The webhook class of the plugin.
 *
 * @since 1.0.0
 * @package WPWH
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Webhook {

	/**
	 * Add the option key for s
	 *
	 * @var - the webhook option key
	 */
	private $webhook_options_key;

	public function __construct() {
		$this->webhook_options_key = WPWH()->settings->get_webhook_option_key();
		$this->webhook_ident_param = WPWH()->settings->get_webhook_ident_param();

		$this->webhook_options = $this->setup_webhooks();
		$this->add_hooks();
	}

	/**
	 * Add all necessary hooks for preloading the data
	 */
	private function add_hooks(){

		if( is_admin() ){
			add_action( 'plugins_loaded', array( $this, 'initialize_default_webhook' ), 10 );
		}

		add_action( 'init', array( $this, 'validate_incoming_data' ), 10 );
	}

	/**
	 * ######################
	 * ###
	 * #### OPTION LOGIC
	 * ###
	 * ######################
	 */

	/**
	 * Initialize all available webhooks for
	 * a better performance
	 *
	 * @return array
	 */
	private function setup_webhooks(){
		$webhook_data = get_option( $this->webhook_options_key );

		if( empty( $webhook_data ) || ! is_array( $webhook_data ) )
			$webhook_data = array();

		return $webhook_data;
	}

	/**
	 * Get all of the available webhooks
	 *
	 * This is the main handler function for all
	 * of our triggers and actions.
	 *
	 * @param string $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param string $group - Wether you want to display grouped ones or not
	 * @param string $single - In case you want to output a single one
	 *
	 * @return array|mixed - An array of the available webhooks
	 */
	public function get_hooks( $type = 'all', $group = '', $single = '' ){
		if( $type != 'all' ){
			if( isset( $this->webhook_options[ $type ] ) && ! empty( $group ) ){
				if( isset( $this->webhook_options[ $type ][ $group ] ) ){
					if( ! empty( $single ) ){
						$return = $this->webhook_options[ $type ][ $group ][ $single ];
					} else {
						$return = $this->webhook_options[ $type ][ $group ];
					}
				} else {
					$return = array();
				}
			} else {

				if( isset( $this->webhook_options[ $type ] ) ){
					if( ! empty( $single ) ){
						$return = $this->webhook_options[ $type ][ $single ];
					} else {
						$return = $this->webhook_options[ $type ];
					}
				} else {
					//Return empty array if nothing is set
					$return = array();
				}

			}
		} else {
			$return = $this->webhook_options;
		}

		if( empty( $return ) ){
			$return = array();
		}

		return $return;
	}

	/**
	 * Set custom webhooks inside of our array()
	 *
	 * @param $key - The key of the single webhook
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param $data - (array) The custom data of the specified webhook
	 * @param string $group - (Optional) A webhook group
	 *
	 * @return bool - True if the hook was successfully set
	 */
	public function set_hooks( $key, $type, $data, $group = '' ){
		$return = false;

		if( empty( $key ) || empty( $type ) || empty( $data ) ){
			return $return;
		}

		if( ! isset( $this->webhook_options[ $type ] ) ){
			$this->webhook_options[ $type ] = array();
		}

		if( $type == 'trigger' ){
			if( ! empty( $group ) ){
				if( ! isset( $this->webhook_options[ $type ][ $group ] ) ){
					$this->webhook_options[ $type ][ $group ] = array();
				}

				$this->webhook_options[$type][ $group ][ $key ] = $data;
				$return = update_option( $this->webhook_options_key, $this->webhook_options );
			} else {
				$return = false;
			}
		} else {
			$this->webhook_options[$type][ $key ] = $data;
			$return = update_option( $this->webhook_options_key, $this->webhook_options );
		}

		return $return;
	}

	/**
	 * Remove a hook from the currently set arrays
	 *
	 * @param $webhook - The slug of the webhook
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param string $group - (Optional) A webhook group
	 *
	 * @return bool - Wether the webhook was deleted or not
	 */
	public function unset_hooks( $webhook, $type, $group = '' ){

		if( empty( $webhook ) || empty( $type ) )
			return false;

		if( isset( $this->webhook_options[ $type ] ) ){
			if( $type == 'trigger' ){
				if( isset( $this->webhook_options[ $type ][$group][ $webhook ] ) ){
					unset( $this->webhook_options[ $type ][$group][ $webhook ] );
					return update_option( $this->webhook_options_key, $this->webhook_options );
				}
			} else {
				if( isset( $this->webhook_options[ $type ][ $webhook ] ) ){
					unset( $this->webhook_options[ $type ][ $webhook ] );
					return update_option( $this->webhook_options_key, $this->webhook_options );
				}
			}
		} else {
			//return true if it doesnt exist
			return true;
		}

		return false;
	}

	/**
	 * Register a new webhook URL
	 *
	 * @param $webhook - The webhook name
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param array $args - Custom attributes depending on the webhooks
	 * @param string $permission - in case a custom permission is set
	 *
	 * @return bool - Wether the webhook url was created or not
	 */
	public function create( $webhook, $type, $args = array(), $permission = '' ){

		if( empty( $webhook ) || empty( $type ) ){
			return false;
		}

		$permission_set = WPWH()->settings->get_admin_cap('default_webhook');
		if( ! empty( $permission ) ){
			$permission_set = $permission;
		}

		$data = array(
			'permission'    => $permission_set,
			'date_created'  => date( 'Y-m-d H:i:s' )
		);

		$group = '';
		switch( $type ){
			case 'action':
				$data['api_key'] = strtolower( wp_generate_password( 64, false ) );
				break;
			case 'trigger':
				$data['webhook_url'] = $args['webhook_url'];
				$group = $args['group'];
				break;
		}


		return $this->set_hooks( $webhook, $type, $data, $group );

	}

	/**
	 * Initialize the default webhook url
	 */
	public function initialize_default_webhook(){

		if( ! empty( $this->webhook_options['action'] ) ){
			return;
		}

		$default_wehook = apply_filters( 'wpwh/admin/webhooks/default_webhook_name', 'main_' . rand( 1000, 9999 ) );

		$data = array(
			'api_key'       => strtolower( wp_generate_password( 64, false ) ),
			'permission'    => WPWH()->settings->get_admin_cap('default_webhook'),
			'date_created'  => date( 'Y-m-d H:i:s' )
		);
		$this->set_hooks( $default_wehook, 'action', $data );

	}

	/**
	 * ######################
	 * ###
	 * #### CORE LOGIC
	 * ###
	 * ######################
	 */

	/**
	 * Create the webhook url for the specified webhook
	 *
	 * @param $webhook - the webhook ident
	 * @param $api_key - the api key on the webhook
	 *
	 * @return string - the webhook url
	 */
	public function built_url( $webhook, $api_key ){

		$args = apply_filters( 'wpwh/admin/webhooks/url_args', array(
			$this->webhook_ident_param => $webhook,
			'wpwh_api_key' => $api_key
		) );

		$url = add_query_arg( $args, home_url( '/' ) );
		return $url;
	}

	/**
	 * Function to output all the available json arguments.
	 *
	 * @param array $args
	 */
	public function echo_response_data( $args = array() ){

		$response_body = WPWH()->helpers->get_response_body();
		$response_type = WPWH()->helpers->validate_request_value( $response_body['content'], 'response_type' );

		if( empty( $response_type ) ){
			$response_type = 'json';
		}

		$response_type = apply_filters( 'wpwh/webhooks/response_response_type', $response_type );
		$args = apply_filters( 'wpwh/webhooks/response_json_arguments', $args, $response_type );

		switch( $response_type ){
			case 'xml':
				header( 'Content-Type: application/xml' );
				$xml = new SimpleXMLElement('<root/>');
				array_walk_recursive($args, array ($xml, 'addChild'));
				print $xml->asXML();
				break;
			case 'json':
			default:
				header( 'Content-Type: application/json' );
				echo json_encode( $args );
				break;
		}
	}

	/**
	 * ######################
	 * ###
	 * #### RECIPIENTS LOGIC
	 * ###
	 * ######################
	 */

	/**
	 * Display the actions in our backend actions table
	 *
	 * The structure to include your recpient looks like this:
	 * array( 'action' => 'my-action', 'parameter' => array( 'my_parameter' => array( 'short_description => 'my text', 'required' => true ) ), 'short_description' => 'This is my short description.', 'description' => 'My HTML Content' )
	 */
	public function get_actions( $active_webhooks = true ){
		return apply_filters( 'wpwh/webhooks/get_webhooks_actions', array(), $active_webhooks );
	}
	/**
	 * Display the actions in our frontend actions table
	 *
	 * The structure to include your recpient looks like this:
	 * array( 'action' => 'my-action', 'parameter' => array( 'my_parameter' => array( 'short_description => 'my text', 'required' => true ) ), 'short_description' => 'This is my short description.', 'description' => 'My HTML Content' )
	 */
	public function get_triggers( $single = '', $active_webhooks = true ){

		$triggers = apply_filters( 'wpwh/webhooks/get_webhooks_triggers', array(), $active_webhooks );

		if( ! empty( $single ) ){
			if( isset( $triggers[ $single ] ) ){
				return $triggers[ $single ];
			} else {
				return false;
			}
		} else {
			return $triggers;
		}


	}

	/**
	 * Validate an incoming webhook action
	 */
	public function validate_incoming_data(){
		$webhooks = $this->get_hooks( 'action' );
		$response_api_key = ! empty( $_REQUEST['wpwh_api_key'] ) ? sanitize_key( $_REQUEST['wpwh_api_key'] ) : '';
		$response_ident_value = ! empty( $_REQUEST[$this->webhook_ident_param] ) ? sanitize_key( $_REQUEST[$this->webhook_ident_param] ) : '';

		if( empty( $response_api_key ) || empty( $response_ident_value ) ){
			return;
		}

		$response_body = WPWH()->helpers->get_response_body();

		// set the output to be JSON. (Default)
		header( 'Content-Type: application/json' );

		if( isset( $webhooks[ $response_ident_value ] ) ){
			if( $webhooks[ $response_ident_value ]['api_key'] != $response_api_key ){
				status_header( 403 );
				echo json_encode( WPWH()->helpers->translate( 'WP Webhook Pro API Key not valid.', 'webhooks-invalid-license-invalid' ) );
				exit;
			}
		} else{
			status_header( 403 );
			echo json_encode( WPWH()->helpers->translate( 'WP Webhook Pro API Key is missing.', 'webhooks-invalid-license-missing' ) );
			exit;
		}

		$action = WPWH()->helpers->validate_request_value( $response_body['content'], 'action' );
		$action = ! empty( $action ) ? $action : '';

		/*
		 * Register all of our available action actions
		 */
		do_action( 'wpwh/webhooks/add_webhooks_actions', $action, $response_ident_value, $response_api_key );
	}

	/**
	 * Our external API Call to post a certain trigger
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return array
	 */
	public function post_to_webhook( $url, $data, $args = array() ){
		$response = array();
		$default_args = array(
			'body' => $data,
			'blocking' => false,
			'timeout' => 100
		);

		$webhook_response = wp_remote_post( $url, array_merge( $default_args, $args ) );

		if ( ! is_wp_error( $webhook_response ) ) {

			if ( isset( $webhook_response['body'] ) && strlen( $webhook_response['body'] ) > 0 ) {
				$response = wp_remote_retrieve_body( $webhook_response );
			}

		}

		return $response;
	}

}
