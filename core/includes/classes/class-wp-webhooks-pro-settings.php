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
		$this->webhook_ident_param  = 'wpwhpro_action';
		$this->active_webhook_ident_param  = 'wpwhpro_active_webhooks';
		$this->default_settings     = $this->load_default_settings();
		$this->required_trigger_settings     = $this->load_required_trigger_settings();
		$this->default_trigger_settings     = $this->load_default_trigger_settings();
		$this->required_action_settings     = $this->load_required_action_settings();
		$this->action_nonce        = array(
			'action' => 'ironikus_wpwhpro_actions',
			'arg'    => 'ironikus_wpwhpro_actions_nonce'
		);
		$this->trans_strings        = $this->load_default_strings();
		$this->active_webhooks      = $this->setup_active_webhooks();
	}

	private function load_default_settings(){
		$fields = array(

			/**
			 * DEACTIVATE SHORTCODE
			 */
			'wpwhpro_activate_translations' => array(
				'id'          => 'wpwhpro_activate_translations',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Activate Translations', 'wpwhpro-fields-activate-translations'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPWHPRO()->helpers->translate('Check this button if you want to enable our translation engine on your website.', 'wpwhpro-fields-translations-tip')
			),

			/**
			 * SET TRIGGER SECRET
			 */
			'wpwhpro_trigger_secret' => array(
				'id'          => 'wpwhpro_trigger_secret',
				'type'        => 'text',
				'label'       => WPWHPRO()->helpers->translate('Trigger Secret', 'wpwhpro-fields-trigger-secret'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPWHPRO()->helpers->translate('Enhance your website security by setting a custom trigger secret key. This allows you to validate against the incoming data from the recipient url side.', 'wpwhpro-fields-trigger-secret-tip')
			),

			/**
			 * Reset WP Webbhooks Pro
			 */
			'wpwhpro_reset_data' => array(
				'id'          => 'wpwhpro_reset_data',
				'type'        => 'checkbox',
				'label'       => WPWHPRO()->helpers->translate('Reset WP Webhooks Pro', 'wpwhpro-fields-reset'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPWHPRO()->helpers->translate('Reset WP Webhooks Pro and set it back to its default settings (Excludes license & Extensions). BE CAREFUL: Once you activate the button and click save, all of your saved data for the plugin is gone.', 'wpwhpro-fields-reset-tip')
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

	private function load_required_trigger_settings(){
		$fields = array(

			'wpwhpro_trigger_response_type' => array(
				'id'          => 'wpwhpro_trigger_response_type',
				'type'        => 'select',
				'label'       => WPWHPRO()->helpers->translate('Change the data response type', 'wpwhpro-fields-trigger-required-settings'),
				'choices'     => array(
					'json' => 'JSON',
					'xml' => 'XML',
					'form' => 'X-WWW-FORM-URLENCODE',
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => WPWHPRO()->helpers->translate('Set a custom response type for the data that gets send to the specified URL. Default is JSON.', 'wpwhpro-fields-trigger-required-settings')
			)

		);

		return apply_filters('wpwhpro/settings/required_trigger_settings', $fields);
	}

	/*
	 * Return the default filter settings
	 *
	 * @since 1.6.4
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
			//Will soon be filled
		);

		return apply_filters('wpwhpro/settings/required_action_settings', $fields);
	}

	public function setup_active_webhooks(){

		$webhooks = get_option( $this->active_webhook_ident_param );

		if( empty( $webhooks ) && ! is_array( $webhooks ) ){
			$webhooks = array(
				'triggers' => array(),
				'actions' => array(),
			);
		}

		return $webhooks;
	}

	/**
	 * ######################
	 * ###
	 * #### TRANSLATEABLE STRINGS
	 * ###
	 * ######################
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
	 * Return the action nonce data
	 *
	 * @return array - the action nonce data
	 */
	public function get_action_nonce(){

		return $this->action_nonce;

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
	 * @return array - the active webhooks
	 */
	public function get_active_webhooks( $type = 'all' ){
		$return = $this->active_webhooks;

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
}