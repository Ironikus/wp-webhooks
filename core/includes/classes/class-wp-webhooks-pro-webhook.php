<?php

/**
 * WP_Webhooks_Pro_Webhook Class
 *
 * This class contains all of the available api functions
 *
 * @since 1.0.0
 */

/**
 * The webhook class of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Webhook {

	/**
	 * Add the option key 
	 *
	 * @var - the webhook option key
	 */
	private $webhook_options_key;

	/**
	 * Add the processed triggers 
	 *
	 * @since 4.3.2
	 * @var - the processed triggers
	 */
	private $processed_triggers = array();

	/**
	 * If an action call is present, this var contains the webhook
	 *
	 * @since 4.0.0
	 * @var - The currently present action webhook
	 */
	private $current_webhook_action;

	public function __construct() {
		$this->webhook_options_key = WPWHPRO()->settings->get_webhook_option_key();
		$this->webhook_ident_param = WPWHPRO()->settings->get_webhook_ident_param();

		$this->current_webhook_action = null;
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

		add_action( 'init', array( $this, 'validate_incoming_data' ), 100 );
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

		if( empty( $webhook_data ) || ! is_array( $webhook_data ) ){
			$webhook_data = array();
		}

		foreach( $webhook_data as $wd_key => $wd_val ){

			switch( $wd_key ){
				case 'action':
					foreach( $webhook_data[ $wd_key ] as $wds_key => $wds_val ){
						if( is_array( $webhook_data[ $wd_key ][ $wds_key ] ) ){
							$webhook_data[ $wd_key ][ $wds_key ]['webhook_name'] = $wds_key;
						}
					}
					break;
				case 'trigger':
					foreach( $webhook_data[ $wd_key ] as $wds_key => $wds_val ){
						if( is_array( $webhook_data[ $wd_key ][ $wds_key ] ) ){
							foreach( $webhook_data[ $wd_key ][ $wds_key ] as $wdss_key => $wdss_val ){
								if( is_array( $webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ] ) ){
									$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_name'] = $wds_key;
									$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_url_name'] = $wdss_key;
								}
							}
						}
					}
					break;
			}

		}

		return $webhook_data;
	}

	/**
	 * Reload webhook hooks 
	 *
	 * @return array The webhook hooks
	 */
	public function reload_webhooks(){
		$this->webhook_options = $this->setup_webhooks();
		return $this->webhook_options;
	}

	/**
	 * Get all of the available webhooks
	 *
	 * This is the main handler function for all
	 * of our triggers and actions.
	 *
	 * @param string $type - the type of the hooks you want to get (trigger, action, all (default))
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

		return apply_filters( 'wpwhpro/admin/webhooks/get_hooks', $return, $type, $group, $single ) ;
	}

	/**
	 * Set custom webhooks inside of our array()
	 *
	 * @param $key - The key of the single webhook (not the idetifier)
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
			//A trigger needs to belong to a group
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

		$permission_set = WPWHPRO()->settings->get_admin_cap('default_webhook');
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
				$data['api_key'] = $this->generate_api_key();

				if( isset( $args['settings'] ) && is_array( $args['settings'] ) ){
					$data['settings'] = $args['settings'];
				}

				break;
			case 'trigger':
				$data['webhook_url'] = $args['webhook_url'];

				if( isset( $args['settings'] ) && is_array( $args['settings'] ) ){
					$data['settings'] = $args['settings'];
				}

				$group = $args['group'];
				break;
		}


		return $this->set_hooks( $webhook, $type, $data, $group );

	}

	/**
	 * Update an existig webhook URL
	 *
	 * @param $key - The webhook identifier
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param array $args - Custom attributes depending on the webhooks
	 *
	 * @return bool - Wether the webhook was updated or not
	 */
	public function update( $key, $type, $group = '', $args = array() ){

		if( empty( $key ) || empty( $type ) ){
			return false;
		}

		$current_hooks = $this->get_hooks();
		$group = ( ! empty( $group ) ) ? $group : '';


		$data = array();

		if( ! empty( $group ) ){
			if( isset( $current_hooks[ $type ] ) ){
				if( isset( $current_hooks[ $type ][ $group ] ) ){
					if( isset( $current_hooks[ $type ][ $group ][ $key ] ) ){
						$data = $current_hooks[ $type ][ $group ][ $key ];
					}
				}
			}
		} else {
			if( isset( $current_hooks[ $type ] ) ){
				if( isset( $current_hooks[ $type ][ $key ] ) ){
					$data = $current_hooks[ $type ][ $key ];
				}
			}
		}

		$check = false;
		if( ! empty( $data ) ){
			$data = array_merge( $data, $args );

			//Revalidate the settings data with the $data array
			if( isset( $args['settings'] ) ){

				$data['settings'] = $args['settings'];

				//Remove empty entries since we don't want to save what's not necessary
				foreach( $data['settings'] as $skey => $sdata ){
					if( $sdata === '' ){
						unset( $data['settings'][ $skey ] );
					}
				}

			}

			$check = $this->set_hooks( $key, $type, $data, $group );
		}

		return $check;

	}

	/**
	 * Initialize the default webhook url
	 */
	public function initialize_default_webhook(){

		if( ! empty( $this->webhook_options['action'] ) ){
			return;
		}

		$default_wehook = apply_filters( 'wpwhpro/admin/webhooks/default_webhook_name', 'main_' . rand( 1000, 9999 ) );

		$data = array(
			'api_key'       => $this->generate_api_key(),
			'permission'    => WPWHPRO()->settings->get_admin_cap('default_webhook'),
			'date_created'  => date( 'Y-m-d H:i:s' )
		);
		$this->set_hooks( $default_wehook, 'action', $data );

	}

	public function generate_api_key( $length = 64 ){

		if( ! is_int( $length ) ){
			$length = 64; //Fallack on non-integers
		}

		$password = strtolower( wp_generate_password( $length, false ) );

		return apply_filters( 'wpwhpro/admin/webhooks/generate_api_key', $password, $length );
	}

	/**
	 * Return a list of all available, processed triggers
	 *
	 * @return array
	 */
	public function get_processed_triggers(){
		return apply_filters( 'wpwhpro/admin/webhooks/get_processed_triggers', $this->processed_triggers );
	}

	/**
	 * Set a trigger to the processed trigger list
	 *
	 * @return array
	 */
	public function set_processed_trigger( $trigger, $data ){

		$all_processed_triggers = $this->get_processed_triggers();

		if( is_array( $all_processed_triggers ) ){
			$all_processed_triggers[ $trigger ] = $data;
		}

		$this->processed_triggers = $all_processed_triggers;

		return apply_filters( 'wpwhpro/admin/webhooks/set_processed_trigger', $this->processed_triggers );
	}

	/**
	 * ######################
	 * ###
	 * #### CORE LOGIC
	 * ###
	 * ######################
	 */

	/*
	 * The core logic for reseting our plugin
	 *
	 * @since 1.6.4
	 */
	public function reset_wpwhpro(){

		//Reset settings
		$settings = WPWHPRO()->settings->get_settings();
		foreach( $settings as $key => $value ){
			if( $key ){
				delete_option( $key );
			}
		}

		//Reset active webhook parameter and all its data
		delete_option( WPWHPRO()->settings->get_active_webhooks_ident() );

		//Reset all the webhook settings
		delete_option( WPWHPRO()->settings->get_webhook_option_key() );

		//Reset authentication
		WPWHPRO()->auth->delete_table();

		//Reset transients
		delete_transient( WPWHPRO()->settings->get_news_transient_key() );
		delete_transient( WPWHPRO()->settings->get_extensions_transient_key() );

		//Reset custom post meta entries
		WPWHPRO()->sql->run( "DELETE FROM {postmeta} WHERE meta_key LIKE 'wpwhpro_create_post_temp_status%';" );

	}

	/**
	 * Create the webhook url for the specified webhook
	 *
	 * @param $webhook - the webhook ident
	 * @param $api_key - the api key on the webhook
	 *
	 * @return string - the webhook url
	 */
	public function built_url( $webhook, $api_key, $additional_args = array() ){

		$args = array_merge( $additional_args, array(
			$this->webhook_ident_param => $webhook,
			'wpwhpro_api_key' => $api_key
		) );

		$args = apply_filters( 'wpwhpro/admin/webhooks/url_args', $args, $additional_args );

		$url = add_query_arg( $args, WPWHPRO()->helpers->safe_home_url( '/' ) );
		return $url;
	}

	/**
	 * Function to output all the available arguments for actions
	 *
	 * @since 3.0.7
	 * @param array $args
	 */
	public function echo_action_data( $args = array() ){

		$current_webhook = $this->get_current_webhook_action();
		$response_body = WPWHPRO()->helpers->get_response_body();
		$action = $this->get_incoming_action( $response_body );
		
		$validated_data = $this->echo_response_data( $args );

		do_action( 'wpwhpro/webhooks/echo_action_data', $action, $validated_data );

		return $validated_data;
	}

	/**
	 * Function to output all the available json arguments.
	 *
	 * @param array $args
	 */
	public function echo_response_data( $args = array() ){
		$return = array(
			'arguments' => $args,
			'response_type' => '',
		);

		$response_body = WPWHPRO()->helpers->get_response_body();
		$response_type = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'response_type' ) );

		if( empty( $response_type ) ){
			$response_type = 'json';
		}

		$response_type = apply_filters( 'wpwhpro/webhooks/response_response_type', $response_type, $args );
		$args = apply_filters( 'wpwhpro/webhooks/response_json_arguments', $args, $response_type );

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

		$return['arguments'] = $args;
		$return['response_type'] = $response_type;

		return $return;
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
		$actions = WPWHPRO()->integrations->get_actions();
		return apply_filters( 'wpwhpro/webhooks/get_webhooks_actions', $actions, $active_webhooks );
	}
	/**
	 * Display the actions in our frontend actions table
	 *
	 * The structure to include your recpient looks like this:
	 * array( 'action' => 'my-action', 'parameter' => array( 'my_parameter' => array( 'short_description => 'my text', 'required' => true ) ), 'short_description' => 'This is my short description.', 'description' => 'My HTML Content' )
	 */
	public function get_triggers( $single = '', $active_webhooks = true ){

		$triggers = WPWHPRO()->integrations->get_triggers();

		$triggers = apply_filters( 'wpwhpro/webhooks/get_webhooks_triggers', $triggers, $active_webhooks );

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
	 * Get the currently present webhook action
	 *
	 * @since 3.0.0
	 * @return mixed Array on success, null on no webhook given
	 */
	public function get_current_webhook_action(){
		return apply_filters( 'wpwhpro/webhooks/get_current_webhook_action', $this->current_webhook_action );
	}

	public function get_incoming_action( $response_body = false ){

		if( $response_body === false ){
			$response_body = WPWHPRO()->helpers->get_response_body();
		}

		$action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'action' );
		if( empty( $action ) ){
			if( ! empty( $_REQUEST['action'] ) ){
				$action = sanitize_title( $_REQUEST['action'] );
			} else {
				$action = '';
			}

			if( empty( $action ) ){
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook call did not contain any action argument.", 'admin-debug-feature' )  );
			}
		}

		return apply_filters( 'wpwhpro/webhooks/get_incoming_action', $action, $response_body );
	}

	/**
	 * Get a properly formatted description of a webhook endpoint
	 *
	 * @since 3.2.1
	 * @return string the HTML formatted webhook description
	 */
	public function get_endpoint_description( $type = 'trigger', $data = array() ){

		$description = '';

		switch( $type ){
			case 'trigger':
				ob_start();
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger.php' );
				$description = ob_get_clean();
				break;
			case 'action':
				ob_start();
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action.php' );
				$description = ob_get_clean();
				break;
		}

		return apply_filters( 'wpwhpro/webhooks/get_endpoint_description', $description, $type, $data );
	}

	/**
	 * Validate an incoming webhook action
	 */
	public function validate_incoming_data(){
		$webhooks = $this->get_hooks( 'action' );
		$response_auth_request = ( isset( $_REQUEST['wpwhpro_auth_response'] ) && intval( $_REQUEST['wpwhpro_auth_response'] ) === 1 ) ? true : false;
		$response_api_key = ! empty( $_REQUEST['wpwhpro_api_key'] ) ? sanitize_key( $_REQUEST['wpwhpro_api_key'] ) : '';
		$response_ident_value = ! empty( $_REQUEST[$this->webhook_ident_param] ) ? sanitize_key( $_REQUEST[$this->webhook_ident_param] ) : '';

		if( empty( $response_api_key ) || empty( $response_ident_value ) ){
			return;
		}

		//Setup default response
		$return = array(
			'success' => false
		);

		//Validate against inactive action webhooks
		if( isset( $webhooks[ $response_ident_value ] ) && isset( $webhooks[ $response_ident_value ]['status'] ) ){
			if( $webhooks[ $response_ident_value ]['status'] === 'inactive' ){
				status_header( 403 );
				$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'Your current %s webhook is deactivated. Please activate it first.', 'webhooks-deactivated-webhook' ), WPWH_NAME );
				WPWHPRO()->webhook->echo_response_data( $return );
				exit;
			}
		}
		
		$response_body = WPWHPRO()->helpers->get_response_body();

		// set the output to be JSON. (Default)
		header( 'Content-Type: application/json' );

		$action = $this->get_incoming_action( $response_body );

		if( isset( $webhooks[ $response_ident_value ] ) ){
			if( $webhooks[ $response_ident_value ]['api_key'] != $response_api_key ){
				status_header( 403 );
				$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'The given %s API Key is not valid, please enter a valid API key and try again.', 'webhooks-invalid-license-invalid' ), WPWH_NAME );
				WPWHPRO()->webhook->echo_response_data( $return );
				exit;
			}
		} else {
			status_header( 403 );
			$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'The given %s API Key is missing, please add it first.', 'webhooks-invalid-license-missing' ), WPWH_NAME );
			WPWHPRO()->webhook->echo_response_data( $return );
			exit;
		}

		$this->current_webhook_action = $webhooks[ $response_ident_value ];

		//Return auth request
		if( $response_auth_request ){
			$return_auth = array(
				'success' => true,
				'msg' => WPWHPRO()->helpers->translate( 'The authentication was successful', 'webhooks-auth-response-success' ),
				'domain' => home_url(),
				'name' => ( ! empty( $response_ident_value ) ) ? $response_ident_value : 'none'
			);

			$webhook_response = WPWHPRO()->webhook->echo_response_data( $return_auth );
			die();
		}

		if( is_array($webhooks[ $response_ident_value ]) && isset( $webhooks[ $response_ident_value ]['settings'] ) && ! empty( $webhooks[ $response_ident_value ]['settings'] ) ){

			foreach( $webhooks[ $response_ident_value ]['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_action_authentication' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$is_valid_auth = WPWHPRO()->auth->verify_incoming_request( $settings_data );

						if( empty( $is_valid_auth['success'] ) ){
							status_header( 401 );
							$return['msg'] = $is_valid_auth['msg'];

							$webhook_response = WPWHPRO()->webhook->echo_response_data( $return );
							die();
						}
					}
				}

			}

		}

		//Keep the old hook to keep other extensions working (Extensions need to adjust as this way we won't be able to catch the response)
		do_action( 'wpwhpro/webhooks/add_webhooks_actions', $action, $response_ident_value, $response_api_key );
	
		$default_return_data = array(
            'success' => false,
			'action' => $action,
			'msg' => WPWHPRO()->helpers->translate("It looks like your current webhook call has no action argument defined, it is deactivated or it does not have any action function.", 'action-add-webhook-actions' ),
        );

		//since 3.2.0
		$return_data = WPWHPRO()->integrations->execute_actions( $default_return_data, $action, $response_ident_value, $response_api_key );

		$return_data = apply_filters( 'wpwhpro/webhooks/add_webhook_actions', $return_data, $action, $response_ident_value, $response_api_key );
		
		if( $return_data === $default_return_data ){
			$webhook_response = WPWHPRO()->webhook->echo_response_data( $return_data );
		} elseif( $return_data === null ){
			$webhook_response = WPWHPRO()->webhook->echo_response_data( $default_return_data );
		} else {
			$webhook_response = WPWHPRO()->webhook->echo_action_data( $return_data );
		}

		die();
	}

	public function generate_trigger_signature( $data, $secret ) {
		$hash_signature = apply_filters( 'wpwhpro/admin/webhooks/webhook_trigger_signature', 'sha256', $data );

		return base64_encode( hash_hmac( $hash_signature, $data, $secret, true ) );
	}

	/**
	 * Our external API Call to post a certain trigger
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return array
	 */
	public function post_to_webhook( $webhook, $data, $args = array(), $skip_validation = false ){

		//Preserve original values
		$original_webhook = $webhook;
		$original_data = $data;
		$original_args = $args;
		$original_validation = $skip_validation;

		/*
		 * Allow also to send the whole webhook
		 * @since 1.6.4
		 */
		if( is_array( $webhook ) ){
			$url = $webhook['webhook_url'];
		} else {
			$url = $webhook;
		}

		$url_unvalidated = $url;

		/*
		 * Validate default settings
		 *
		 * @since 1.6.4
		 */
		$response = array(
			'success' => false,
			'is_valid' => true,
		);
		$response_content_type_slug = 'json';
		$response_content_type_method = 'POST';
		$response_content_type = 'application/json';
		$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : '';
		$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : '';
		$authentication_data = array();
		$allow_unsafe_urls = false;
		$allow_unverified_ssl = false;

		//Required settings
		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) ) {

			foreach ( $webhook['settings'] as $settings_name => $settings_data ) {

				//Authentication
				if( $settings_name === 'wpwhpro_trigger_authentication' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$template = WPWHPRO()->auth->get_auth_templates( $settings_data );
						if( ! empty( $template ) && ! empty( $template->template ) && ! empty( $template->auth_type ) ){
							$sub_template_data = base64_decode( $template->template );
							if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
								$template_data = json_decode( $sub_template_data, true );
								if( ! empty( $template_data ) ){
									$authentication_data = array(
										'auth_type' => $template->auth_type,
										'data' => $template_data
									);
								}
							}
						}
					}
				
				}

				if( $settings_name === 'wpwhpro_trigger_response_type' && ! empty( $settings_data ) ){

					switch( $settings_data ){
						case 'form':
							$response_content_type_slug = 'form';
							$response_content_type = 'application/x-www-form-urlencoded';
							break;
						case 'xml':
							if( extension_loaded('simplexml') ){
								$response_content_type_slug = 'xml';
								$response_content_type = 'application/xml';
							} else {
								$response['msg'] = WPWHPRO()->helpers->translate( 'SimpleXML is not activated on your server. Please activate it first or switch the content type of your webhook.', 'wpwhpro-admin-webhooks' );
								$response['is_valid'] = false;
							}
							break;
						case 'json':
						default:
							//Just for reference
							$response_content_type_slug = 'json';
							$response_content_type = 'application/json';
							break;
					}

				}

				if( $settings_name === 'wpwhpro_trigger_request_method' && ! empty( $settings_data ) ){

					switch( $settings_data ){
						case 'GET':
							$response_content_type_method = 'GET';
							break;
						case 'HEAD':
							$response_content_type_method = 'HEAD';
							break;
						case 'PUT':
							$response_content_type_method = 'PUT';
							break;
						case 'DELETE':
							$response_content_type_method = 'DELETE';
							break;
						case 'TRACE':
							$response_content_type_method = 'TRACE';
							break;
						case 'OPTIONS':
							$response_content_type_method = 'OPTIONS';
							break;
						case 'PATCH':
							$response_content_type_method = 'PATCH';
							break;
						case 'POST':
						default:
							//Just for reference
							$response_content_type_method = 'POST';
							break;
					}

				}

				//Allow unsafe URLs
				if( $settings_name === 'wpwhpro_trigger_allow_unsafe_urls' && (integer) $settings_data === 1 ){
					$allow_unsafe_urls = true;
				}

				//Allow unverified SSL
				if( $settings_name === 'wpwhpro_trigger_allow_unverified_ssl' && (integer) $settings_data === 1 ){
					$allow_unverified_ssl = true;
				}

			}
		}

		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) && ! $skip_validation ){

			foreach( $webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_user_must_be_logged_in' && (integer) $settings_data === 1 ){
					if( ! is_user_logged_in() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_user_must_be_logged_out' && (integer) $settings_data === 1 ){
					if( is_user_logged_in() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_backend_only' && (integer) $settings_data === 1 ){
					if( ! is_admin() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_frontend_only' && (integer) $settings_data === 1 ){
					if( is_admin() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_single_instance_execution' && (integer) $settings_data === 1 ){
					
					$all_processed_triggers = $this->get_processed_triggers();
					if( is_array( $all_processed_triggers ) && ! empty( $all_processed_triggers ) && isset( $all_processed_triggers[ $webhook_name . '_' . $webhook_url_name ] ) ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'This was a duplicate request as the Single Instance Execution was set for the webhook.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
						$response['duplicate'] = true;
					}

				}

			}

		}

		//Validate against inactive action webhooks
		if( isset( $webhook['status'] ) && ! $skip_validation ){
			if( $webhook['status'] === 'inactive' ){
				$response['msg'] = WPWHPRO()->helpers->translate( 'The following webhook trigger url is deactivated. Please activate it first.', 'webhooks-deactivated-webhook' );
				$response['is_valid'] = false;
			}
		}

		$response = apply_filters( 'wpwhpro/admin/webhooks/is_valid_trigger_response', $response, $webhook, $data, $args );

		if( $response['is_valid'] === false ){
			return $response;
		}

		$http_args = array(
			'method'      => $response_content_type_method,
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking'    => false,
			'user-agent'  => sprintf(  WPWH_NAME . '/%s Trigger (WordPress/%s)', WPWH_VERSION, $GLOBALS['wp_version'] ),
			'headers'     => array(
				'Content-Type' => $response_content_type,
			),
			'cookies'     => array(),
		);

		if( $allow_unverified_ssl ){
			$http_args['sslverify'] = false;
		}

		$data = apply_filters( 'wpwhpro/admin/webhooks/webhook_data', $data, $response, $webhook, $args, $authentication_data );

		switch( $response_content_type_slug ){
			case 'form':
				$http_args['body'] = $data;
				break;
			case 'xml':
				$sxml_data = apply_filters( 'wpwhpro/admin/webhooks/simplexml_data', '<data/>', $http_args );
				$xml_data = $data;
				$xml = WPWHPRO()->helpers->convert_to_xml( new SimpleXMLElement( $sxml_data ), $xml_data );
				$http_args['body'] = $xml->asXML();
				break;
			case 'json':
			default:
				$http_args['body'] = trim( wp_json_encode( $data ) );
				break;
		}

		//Add charset if available
		$blog_charset = get_option( 'blog_charset' );
		if ( ! empty( $blog_charset ) ) {
			$http_args['headers']['Content-Type'] .= '; charset=' . $blog_charset;
		}

		$http_args = apply_filters( 'wpwhpro/admin/webhooks/webhook_http_args', array_merge( $http_args, $args ), $args, $url, $webhook, $authentication_data, $url_unvalidated );

		$http_args['headers']['X-WP-Webhook-Source'] = home_url( '/' );
		$http_args['headers']['X-WP-Webhook-Name'] = $webhook_name;
		$http_args['headers']['X-WP-Webhook-URL-Name'] = $webhook_url_name;

		$secret_key = get_option( 'wpwhpro_trigger_secret' ); //deprecated since 3.0.1
		/*
		 * Set a custom secret key
		 * @since 2.0.1
		 */
		$secret_key = apply_filters( 'wpwhpro/admin/webhooks/secret_key', $secret_key, $webhook, $args, $authentication_data );
		if( ! empty( $secret_key ) ){
			$http_args['headers']['X-WP-Webhook-Signature'] = $this->generate_trigger_signature( $http_args['body'], $secret_key );
		}

		$url = apply_filters( 'wpwhpro/admin/webhooks/webhook_url', $url, $http_args, $webhook, $authentication_data, $url, $url_unvalidated );

		if( $allow_unsafe_urls ){
			$response = wp_remote_request( $url, $http_args );
		} else {
			$response = wp_safe_remote_request( $url, $http_args );	
		}

		$validated_response_body = array(
			'type' => 'trigger_response',
			'content_type' => ( ! is_wp_error( $response ) ) ? wp_remote_retrieve_header( $response, 'content-type' ) : 'application/json',
			'payload' => ( ! is_wp_error( $response ) ) ? wp_remote_retrieve_body( $response ) : $response->get_error_message(),
		);

		$validated_response_data = WPWHPRO()->helpers->get_response_body( $validated_response_body );
		if( isset( $validated_response_data ) && is_array( $response ) && isset( $validated_response_data['content'] ) ){
			$response['body_validated'] = $validated_response_data['content'];
		}

		$log_data = array(
			'webhook_type' => 'trigger',
			'webhook_name' => $webhook_name,
			'webhook_url_name' => $webhook_url_name,
			'identifier' => $url,
			'request_data' => $http_args,
			'response_data' => $response,
			'log_version' => WPWH_VERSION,
			'init_vars' => array(
				'webhook' => $original_webhook,
				'data' => $original_data,
				'args' => $original_args,
				'skip_validation' => $original_validation,
				'url_unvalidated' => $url_unvalidated,
			),
		);

		//Mark the trigger as processed
		$this->set_processed_trigger( $webhook_name . '_' . $webhook_url_name, $log_data );

		do_action( 'wpwhpro/admin/webhooks/webhook_trigger_sent', $response, $url, $http_args, $webhook );

		return $response;
	}

}
