<?php

/**
 * Class WP_Webhooks_Pro_Settings
 *
 * This class contains all of our important settings
 * Here you can configure the whole plugin behavior.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Settings{

	/**
	 * Our globally used capability
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $admin_cap;

	/**
	 * The main page name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * Our global array for translateable strings
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $trans_strings;

	/**
	 * The action nonce data
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $action_nonce;

	/**
	 * The trigger nonce data
	 *
	 * @var array
	 * @since 3.0.0
	 */
	private $trigger_nonce;

	/**
	 * Backwards compatibility for other plugins
	 * fetching the active webhooks
	 *
	 * @var array
	 * @since 3.0.0
	 */
	private $active_webhooks = null;

	/**
	 * WP_Webhooks_Pro_Settings constructor.
	 *
	 * We define all of our necessary settings in here.
	 * If you need to do plugin related changes, everything will
	 * be available in this file.
	 */
	function __construct(){
		$this->admin_cap            = 'manage_options';
		$this->page_name            = 'wp-webhooks-pro';
		$this->page_title           = WPWH_NAME;
		$this->webhook_settings_key = 'ironikus_webhook_webhooks';
		$this->news_transient_key   = 'ironikus_cached_news';
		$this->extensions_transient_key   = 'ironikus_cached_extensions';
		$this->webhook_ident_param  = 'wpwhpro_action';
		$this->active_webhook_ident_param  = 'wpwhpro_active_webhooks';
		$this->default_settings     = $this->load_default_settings();
		$this->required_trigger_settings     = $this->load_required_trigger_settings();
		$this->default_trigger_settings     = $this->load_default_trigger_settings();
		$this->required_action_settings     = $this->load_required_action_settings();
		$this->authentication_methods     = $this->load_authentication_methods();
		$this->authentication_table_data   = $this->setup_authentication_table_data();
		$this->default_integration_dependencies = array(
            'helpers',
            'actions',
            'triggers',
        );
		$this->settings_nonce        = array(
			'action' => 'ironikus_wpwhpro_settings',
			'arg'    => 'ironikus_wpwhpro_settings_nonce'
		);
		$this->trigger_nonce        = array(
			'action' => 'ironikus_wpwhpro_triggers',
			'arg'    => 'ironikus_wpwhpro_triggers_nonce'
		);
		$this->action_nonce        = array(
			'action' => 'ironikus_wpwhpro_actions',
			'arg'    => 'ironikus_wpwhpro_actions_nonce'
		);
		$this->authentication_nonce        = array(
			'action' => 'ironikus_wpwhpro_authentication',
			'arg'    => 'ironikus_wpwhpro_authentication_nonce'
		);
		$this->trans_strings        = $this->load_default_strings();
	}

	/**
	 * Setup the authentication table data
	 *
	 * @return array - the authentication table data
	 */
	public function setup_authentication_table_data(){

		$data = array();
		$table_name = 'wpwhpro_authentication';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  name VARCHAR(100),
		  auth_type VARCHAR(100),
		  template LONGTEXT,
		  log_time DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Load the default settings for the main settings page
	 * of our plugin.
	 *
	 * @return array - an array of all available settings
	 */
	private function load_default_settings(){
		$fields = array(

			/**
			 * MAIN MENU ITEM
			 */
			'wpwhpro_show_main_menu' => array(
				'id'          => 'wpwhpro_show_main_menu',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate( 'Show in admin menu', 'wpwhpro-fields-show-main-menu' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => sprintf( WPWHPRO()->helpers->translate( 'Check this button if you want to show %s within the main admin menu instead of a sub menu item.', 'wpwhpro-fields-show-main-menu' ), $this->get_page_title() )
			),

			/**
			 * ACTIVATE TRANSLATIONS
			 */
			'wpwhpro_activate_translations' => array(
				'id'          => 'wpwhpro_activate_translations',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Activate Translations', 'wpwhpro-fields-activate-translations'),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to make this plugin translateable.', 'wpwhpro-fields-translations-tip')
			),

			/**
			 * Deactivate Post Delay
			 */
			'wpwhpro_deactivate_post_delay' => array(
				'id'          => 'wpwhpro_deactivate_post_delay',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Deactivate Post Trigger Delay', 'wpwhpro-fields-reset'),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => WPWHPRO()->helpers->translate('Advanced: By default, we delay every webhook trigger until the WordPress "shutdown" hook fires. This allows us to also keep track of the changes third-party plugins make. If you do not want that, check this box.', 'wpwhpro-fields-reset-tip')
			),

			/**
			 * Deactivate Post Delay
			 */
			'wpwhpro_activate_debug_mode' => array(
				'id'          => 'wpwhpro_activate_debug_mode',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Activate Debug Mode', 'wpwhpro-fields-reset'),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => WPWHPRO()->helpers->translate('This feature adds additional debug information to the plugin. It logs, e.g. further details within the WordPress debug.log file about issues that occur from a configurational perspective.', 'wpwhpro-fields-reset-tip')
			),

			/**
			 * Reset WP Webbhooks Pro
			 */
			'wpwhpro_reset_data' => array(
				'id'          => 'wpwhpro_reset_data',
				'type'        => 'checkbox',
				'label'       => sprintf( WPWHPRO()->helpers->translate('Reset %s', 'wpwhpro-fields-reset'), WPWH_NAME ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => true,
				'description' => sprintf( WPWHPRO()->helpers->translate('Reset %s and set it back to its default settings (Excludes license & Extensions). BE CAREFUL: Once you activate the button and click save, all of your saved data for the plugin is gone.', 'wpwhpro-fields-reset-tip'), WPWH_NAME )
			),
		);

		foreach( $fields as $key => $field ){
			$value = get_option( $key );

			$fields[ $key ]['value'] = $value;

			if( $fields[ $key ]['type'] == 'checkbox' ){
				if( empty( $fields[ $key ]['value'] ) || $fields[ $key ]['value'] == 'no' ){
					$fields[ $key ]['value'] = 'no';
				} else {
					$fields[ $key ]['value'] = 'yes';
				}
			}
		}

		return apply_filters('wpwhpro/settings/fields', $fields);
	}

	/**
	 * Load the strictly necessary trigger settings
	 * to any available trigger.
	 *
	 * @return array - the trigger settings
	 */
	private function load_required_trigger_settings(){

		$fields = array();

		//todo - remove if done
		if( defined( 'WPWH_DEV' ) && WPWH_DEV === true ){

			$fields['wpwhpro_trigger_demo_text'] = array(
				'id'          => 'wpwhpro_trigger_demo_text',
				'type'        => 'text',
				'label'       => WPWHPRO()->helpers->translate('Demo Text', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'required' 		=> true,
				'description' => WPWHPRO()->helpers->translate('This is the demo text field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_text_variable'] = array(
				'id'          => 'wpwhpro_trigger_demo_text_variable',
				'type'        => 'text',
				'variable'    => true,
				'label'       => WPWHPRO()->helpers->translate('Demo Text Variable', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'required' 		=> true,
				'description' => WPWHPRO()->helpers->translate('This is the demo text field description with dynamic variables.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_select'] = array(
				'id'          => 'wpwhpro_trigger_demo_select',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Demo Simple Select', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('This is the demo select field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_select_def'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_def',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Demo Simple Default Select', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => 'demo2',
				'description' => WPWHPRO()->helpers->translate('This is the demo select field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_select_mult'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_mult',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Demo Multiple Select', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'multiple' => true,
				'description' => WPWHPRO()->helpers->translate('This is the demo select field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_select_mult_def'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_mult_def',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Demo Multiple Default Select', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => array(
					'demo1',
					'demo3',
				),
				'multiple' => true,
				'description' => WPWHPRO()->helpers->translate('This is the demo select field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_radio'] = array(
				'id'          => 'wpwhpro_trigger_demo_radio',
				'type'        => 'radio',
				'label'       => WPWHPRO()->helpers->translate('Demo Radio', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'demo1' => array(
						'label' => 'First value',
						'description' => 'This is a short value description'
					),
					'demo2' => array(
						'label' => 'Second value',
						'description' => 'This is a short value description'
					),
					'demo3' => array(
						'label' => 'Third value',
						'description' => 'This is a short value description'
					),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('This is the demo radio field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_textarea'] = array(
				'id'          => 'wpwhpro_trigger_demo_textarea',
				'type'        => 'textarea',
				'label'       => WPWHPRO()->helpers->translate('Demo Textarea', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('This is the demo textarea field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_wysiwyg'] = array(
				'id'          => 'wpwhpro_trigger_demo_wysiwyg',
				'type'        => 'wysiwyg',
				'label'       => WPWHPRO()->helpers->translate('Demo Wysiwyg', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('This is the demo Wysiwyg field description.', 'wpwhpro-fields-trigger-required-settings')
			);

			$fields['wpwhpro_trigger_demo_repeater'] = array(
				'id'          => 'wpwhpro_trigger_demo_repeater',
				'type'        => 'repeater',
				'label'       => WPWHPRO()->helpers->translate('Demo Repeater', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('This is the demo repeater field description.', 'wpwhpro-fields-trigger-required-settings')
			);

		}

		$real_fields = array(

			'wpwhpro_trigger_response_type' => array(
				'id'          => 'wpwhpro_trigger_response_type',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Change the data request type', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'json' => array( 'label' => 'JSON' ),
					'xml' => array( 'label' => 'XML' ),
					'form' => array( 'label' => 'X-WWW-FORM-URLENCODE' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Set a custom request type for the data that gets send to the specified URL. Default is JSON.', 'wpwhpro-fields-trigger-required-settings')
			),
			'wpwhpro_trigger_request_method' => array(
				'id'          => 'wpwhpro_trigger_request_method',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Change the data request method', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'POST' => array( 'label' => 'POST' ),
					'GET' => array( 'label' => 'GET' ),
					'HEAD' => array( 'label' => 'HEAD' ),
					'PUT' => array( 'label' => 'PUT' ),
					'DELETE' => array( 'label' => 'DELETE' ),
					'TRACE' => array( 'label' => 'TRACE' ),
					'OPTIONS' => array( 'label' => 'OPTIONS' ),
					'PATCH' => array( 'label' => 'PATCH' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Set a custom request method for the data that gets send to the specified URL. Default is POST.', 'wpwhpro-fields-trigger-required-settings')
			),
			'wpwhpro_trigger_authentication' => array(
				'id'          => 'wpwhpro_trigger_authentication',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Add authentication template', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => WPWHPRO()->helpers->translate('Choose...', 'wpwhpro-fields-trigger-required-settings') )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Set a custom authentication template in case the other endpoint requires authentication.', 'wpwhpro-fields-trigger-required-settings')
			),
			'wpwhpro_trigger_allow_unsafe_urls' => array(
				'id'          => 'wpwhpro_trigger_allow_unsafe_urls',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Allow unsafe URLs', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Activating this setting allows you to use unsafe looking URLs like zfvshjhfbssdf.szfdhdf.com.', 'wpwhpro-fields-trigger-settings')
			),
			'wpwhpro_trigger_allow_unverified_ssl' => array(
				'id'          => 'wpwhpro_trigger_allow_unverified_ssl',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Allow unverified SSL', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Activating this setting allows you to use unverified SSL connections for this URL (We won\'t verify the SSL for this webhook URL).', 'wpwhpro-fields-trigger-settings')
			),
			'wpwhpro_trigger_single_instance_execution' => array(
				'id'          => 'wpwhpro_trigger_single_instance_execution',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Fire only once per instance', 'wpwhpro-fields-trigger-required-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('By default, our plugin is able to fire on an event multiple times (in case the event was called more than once per WordPress instance). If you check this box, we make sure to fire the webhook only once per instance. A WordPress instance is a single website call from beginning to end.', 'wpwhpro-fields-trigger-settings')
			),

		);

		$fields = array_merge( $fields, $real_fields );

		return apply_filters('wpwhpro/settings/required_trigger_settings', $fields);
	}

	/**
	 * Load the default trigger settings.
	 *
	 * These settings can be loaded optionally with every single webhook trigger.
	 *
	 * @return array - the default trigger settings
	 */
	private function load_default_trigger_settings(){
		$fields = array(

			'wpwhpro_user_must_be_logged_in' => array(
				'id'          => 'wpwhpro_user_must_be_logged_in',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('User must be logged in', 'wpwhpro-fields-trigger-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to fire this webhook only when the user is logged in ( is_user_logged_in() function is used ).', 'wpwhpro-fields-trigger-settings')
			),
			'wpwhpro_user_must_be_logged_out' => array(
				'id'          => 'wpwhpro_user_must_be_logged_out',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('User must be logged out', 'wpwhpro-fields-trigger-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to fire this webhook only when the user is logged out ( ! is_user_logged_in() function is used ).', 'wpwhpro-fields-trigger-settings')
			),
			'wpwhpro_trigger_backend_only' => array(
				'id'          => 'wpwhpro_trigger_backend_only',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Trigger from backend only', 'wpwhpro-fields-trigger-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to fire this trigger only from the backend. Every post submitted through the frontend is ignored ( is_admin() function is used ).', 'wpwhpro-fields-trigger-settings')
			),
			'wpwhpro_trigger_frontend_only' => array(
				'id'          => 'wpwhpro_trigger_frontend_only',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Trigger from frontend only', 'wpwhpro-fields-trigger-settings'),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to fire this trigger only from the frontent. Every post submitted through the backend is ignored ( ! is_admin() function is used ).', 'wpwhpro-fields-trigger-settings')
			)

		);

		return apply_filters('wpwhpro/settings/default_trigger_settings', $fields);
	}

	/**
	 * Load the strictly necessary action settings
	 * to any available action.
	 *
	 * @return array - the action settings
	 */
	private function load_required_action_settings(){
		$fields = array(

			'wpwhpro_action_authentication' => array(
				'id'          => 'wpwhpro_action_authentication',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Add authentication template', 'wpwhpro-fields-action-required-settings'),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => WPWHPRO()->helpers->translate('Choose...', 'wpwhpro-fields-action-required-settings') )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Set a custom authentication template in case the other endpoint requires authentication. Currently, only API Keys and Basic Auth is allowed for webhook actions.', 'wpwhpro-fields-action-required-settings')
			),

		);

		return apply_filters('wpwhpro/settings/required_action_settings', $fields);
	}

	/**
	 * Load all available authentication methods
	 *
	 * @return array - the action settings
	 */
	private function load_authentication_methods(){
		$methods = array(
			//APi Key Authentication
			'api_key' => array(
				'name' => WPWHPRO()->helpers->translate('API Key', 'wpwhpro-fields-authentication-settings'),
				'description' => WPWHPRO()->helpers->translate('Add an API key to your request header/body', 'wpwhpro-fields-authentication-settings'),
				'fields' => array(

					'wpwhpro_auth_api_key_key' => array(
						'id'          => 'wpwhpro_auth_api_key_key',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Key', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('Set the key you have to use to recognize the API key from the other endpoint.', 'wpwhpro-fields-authentication-settings')
					),

					'wpwhpro_auth_api_key_value' => array(
						'id'          => 'wpwhpro_auth_api_key_value',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Value', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('This is the field you can include your API key. ', 'wpwhpro-fields-authentication-settings')
					),

					'wpwhpro_auth_api_key_add_to' => array(
						'id'          => 'wpwhpro_auth_api_key_add_to',
						'type'        => 'select',
						'label'       => WPWHPRO()->helpers->translate('Add to', 'wpwhpro-fields-authentication-settings'),
						'choices'     => array(
							'header' => array( 'label' => WPWHPRO()->helpers->translate('Header', 'wpwhpro-fields-authentication-settings') ),
							'body' => array( 'label' => WPWHPRO()->helpers->translate('Body', 'wpwhpro-fields-authentication-settings') ),
							'both' => array( 'label' => WPWHPRO()->helpers->translate('Header & Body', 'wpwhpro-fields-authentication-settings') ),
						),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('Choose where you want to place the API Key within the request.', 'wpwhpro-fields-authentication-settings')
					),

				),
			),

			//Bearer Token Authentication
			'bearer_token' => array(
				'name' => WPWHPRO()->helpers->translate('Bearer Token', 'wpwhpro-fields-authentication-settings'),
				'description' => WPWHPRO()->helpers->translate('Authenticate yourself on an external API using a Bearer token.', 'wpwhpro-fields-authentication-settings'),
				'fields' => array(
					'wpwhpro_auth_bearer_token_scheme' => array(
						'id'          => 'wpwhpro_auth_bearer_token_scheme',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Scheme', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => 'Bearer',
						'default_value' => 'Bearer',
						'description' => WPWHPRO()->helpers->translate('In case you use a custom scheme, you can adjust it here. In most cases, you can leave it at "Bearer".', 'wpwhpro-fields-authentication-settings')
					),
					'wpwhpro_auth_bearer_token_token' => array(
						'id'          => 'wpwhpro_auth_bearer_token_token',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Token', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('Add the bearer token you received from the other endpoint here. Please add only the token, without the "Bearer " in front.', 'wpwhpro-fields-authentication-settings')
					),
				),
			),

			//Basic Authentication
			'basic_auth' => array(
				'name' => WPWHPRO()->helpers->translate('Basic Auth', 'wpwhpro-fields-authentication-settings'),
				'description' => WPWHPRO()->helpers->translate('Authenticate yourself on an external API using Basic Authentication.', 'wpwhpro-fields-authentication-settings'),
				'fields' => array(
					'wpwhpro_auth_basic_auth_username' => array(
						'id'          => 'wpwhpro_auth_basic_auth_username',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Username', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('Add the username you want to use for the authentication.', 'wpwhpro-fields-authentication-settings')
					),
					'wpwhpro_auth_basic_auth_password' => array(
						'id'          => 'wpwhpro_auth_basic_auth_password',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('Password', 'wpwhpro-fields-authentication-settings'),
						'placeholder' => '',
						'default_value' => '',
						'description' => WPWHPRO()->helpers->translate('Add the password you want to use for the authentication.', 'wpwhpro-fields-authentication-settings')
					),
				),
			),

		);

		return apply_filters('wpwhpro/settings/authentication_methods', $methods);
	}

	/**
	 * Initialize all available, active webhooks
	 *
	 * @deprecated deprecated since version 3.0.0
	 * @return array - active webhooks
	 */
	public function setup_active_webhooks(){

		$webhooks = array(
			'triggers' => array(),
			'actions' => array(),
		);

		$triggers_data = WPWHPRO()->webhook->get_triggers();
		if( ! empty( $triggers_data ) && is_array( $triggers_data ) ){
			foreach( $triggers_data as $td ){
				$trigger = $td['trigger'];
				$webhooks['triggers'][ $trigger ] = array();
			}
		}

		$action_data = WPWHPRO()->webhook->get_actions();
		if( ! empty( $action_data ) && is_array( $action_data ) ){
			foreach( $action_data as $td ){
				$action = $td['action'];
				$webhooks['actions'][ $action ] = array();
			}
		}

		$this->active_webhooks = $webhooks;

		return $webhooks;
	}

	/*
	 * ######################
	 * ###
	 * #### TRANSLATEABLE STRINGS
	 * ###
	 * ######################
	 */

	 /**
	  * Default settings that are used multiple times throughout the page
	  *
	  * @return array - the default settings
	  */
	private function load_default_strings(){
		$trans_arr = array(
			'sufficient-permissions'    => 'You do not have sufficient permissions to access this page.',
		);

		return apply_filters( 'wpwhpro/admin/default_strings', $trans_arr );
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Our admin cap handler function
	 *
	 * This function handles the admin capability throughout
	 * the whole plugin.
	 *
	 * $target - With the target function you can make a more precised filtering
	 * by changing it for specific actions.
	 *
	 * @param string $target - A identifier where the call comes from
	 * @return mixed
	 */
	public function get_admin_cap($target = 'main'){
		/**
		 * Customize the globally used capability for this plugin
		 *
		 * This filter is called every time the capability is needed.
		 */
		return apply_filters( 'wpwhpro/admin/settings/capability', $this->admin_cap, $target );
	}

	/**
	 * Return the page name for our admin page
	 *
	 * @return string - the page name
	 */
	public function get_page_name(){
		/*
		 * Filter the page name based on your needs
		 */
		return apply_filters( 'wpwhpro/admin/settings/page_name', $this->page_name );
	}

	/**
	 * Return the page title for our admin page
	 *
	 * @return string - the page title
	 */
	public function get_page_title(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/page_title', $this->page_title );
	}

	/**
	 * Return the authentication table data
	 *
	 * @return string - the log table data
	 */
	public function get_authentication_table_data(){
		/*
		 * Filter the authentication table data based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/authentication_table_data', $this->authentication_table_data );
	}

	/**
	 * Return the webhook option key
	 *
	 * @return string - the option key
	 */
	public function get_webhook_option_key(){

		return $this->webhook_settings_key;

	}

	/**
	 * Return the news transient key
	 *
	 * @return string - the news transient key
	 */
	public function get_news_transient_key(){

		return $this->news_transient_key;

	}

	/**
	 * Return the extensions transient key
	 *
	 * @return string - the extensions transient key
	 */
	public function get_extensions_transient_key(){

		return $this->extensions_transient_key;

	}

	/**
	 * Return the page title for our admin page
	 *
	 * @return string - the page title
	 */
	public function get_webhook_ident_param(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/webhook_ident_param', $this->webhook_ident_param );
	}

	/**
	 * Return the default integration depenencies
	 *
	 * @return array - the default integration depenencies
	 */
	public function get_default_integration_dependencies(){
		return apply_filters( 'wpwhpro/admin/settings/default_integration_dependencies', $this->default_integration_dependencies );
	}

	/**
	 * Return the settings nonce data
	 *
	 * @return array - the settings nonce data
	 */
	public function get_settings_nonce(){

		return $this->settings_nonce;

	}

	/**
	 * Return the action nonce data
	 *
	 * @return array - the action nonce data
	 */
	public function get_action_nonce(){

		return $this->action_nonce;

	}

	/**
	 * Return the trigger nonce data
	 *
	 * @return array - the trigger nonce data
	 */
	public function get_trigger_nonce(){

		return $this->trigger_nonce;

	}

	/**
	 * Return the authentication nonce data
	 *
	 * @return array - the authentication nonce data
	 */
	public function get_authentication_nonce(){

		return $this->authentication_nonce;

	}

	/**
	 * Return the settings data
	 *
	 * @return array - the settings data
	 */
	public function get_settings(){

		return $this->default_settings;

	}

	/**
	 * Return the required trigger settings data
	 *
	 * @since 1.0.5
	 * @return array - the default trigger settings data
	 */
	public function get_required_trigger_settings(){

		return $this->required_trigger_settings;

	}

	/**
	 * Return the default trigger settings data
	 *
	 * @since 1.6.4
	 * @return array - the default trigger settings data
	 */
	public function get_default_trigger_settings(){

		return $this->default_trigger_settings;

	}

	/**
	 * Return the required action settings data
	 *
	 * @since 1.0.6
	 * @return array - the default action settings data
	 */
	public function get_required_action_settings(){

		return $this->required_action_settings;

	}

	/**
	 * Return all available authentication methods
	 *
	 * @since 3.0.0
	 * @return array - all available authentication methods
	 */
	public function get_authentication_methods(){

		return $this->authentication_methods;

	}

	/**
	 * Return the active webhook ident
	 *
	 * @return string - the active webhook ident
	 */
	public function get_active_webhooks_ident(){

		return $this->active_webhook_ident_param;

	}

	/**
	 * Return the currently active webhooks
	 *
	 * @param string $type - wether you want to receive active webhooks or triggers
	 *
	 * @deprecated deprecated since version 3.0.0
	 * @return array - the active webhooks
	 */
	public function get_active_webhooks( $type = 'all' ){

		if( $this->active_webhooks === null ){
			$return = $this->setup_active_webhooks();
		} else {
			$return = $this->active_webhooks;
		}

		switch( $type ){
			case 'actions':
				$return = $this->active_webhooks['actions'];
				break;
			case 'triggers':
				$return = $this->active_webhooks['triggers'];
				break;
		}

		return $return;

	}

	/**
	 * Return the default strings that are available
	 * for this plugin.
	 *
	 * @param $cname - the identifier for your specified string
	 * @return string - the default string
	 */
	public function get_default_string( $cname ){
		$return = '';

		if(empty( $cname )){
			return $return;
		}

		if( isset( $this->trans_strings[ $cname ] ) ){
			$return = $this->trans_strings[ $cname ];
		}

		return $return;
	}

	public function get_all_post_statuses(){

		$post_statuses = array();

		//Merge default statuses
		$post_statuses = array_merge( $post_statuses, get_post_statuses() );

		//Merge woocommerce statuses
		if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_order_statuses' ) ) {
			$post_statuses = array_merge( $post_statuses, wc_get_order_statuses() );
		}


		return apply_filters( 'wpwhpro/admin/settings/get_all_post_statuses', $post_statuses );
	}

	public function save_settings( $new_settings ){
		$success = false;

		if( empty( $new_settings ) ) {
			return $success;
		}

		$settings = WPWHPRO()->settings->get_settings();

		// START General Settings
		foreach( $settings as $settings_name => $setting ){

			$value = '';

			if( $setting['type'] == 'checkbox' ){
				if( ! isset( $new_settings[ $settings_name ] ) ){
					$value = 'no';
				} else {
					$value = 'yes';
				}
			} elseif( $setting['type'] == 'text' ){
				if( isset( $new_settings[ $settings_name ] ) ){
					$value = sanitize_title( $new_settings[ $settings_name ] );
				}
			}

			update_option( $settings_name, $value );
			$settings[ $settings_name ][ 'value' ] = $value;
		}
		// END General Settings

		$success = true;

		do_action( 'wpwh/admin/settings/settings_saved', $new_settings );

		return $success;
	 }

}