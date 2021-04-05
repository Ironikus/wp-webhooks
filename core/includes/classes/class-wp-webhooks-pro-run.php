<?php

/**
 * Class WP_Webhooks_Pro_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */

class WP_Webhooks_Pro_Run{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_title;

	/**
	 * Preserver certain values
	 *
	 * @var string
	 * @since 2.0.0
	 */
	private $pre_action_values;

	/**
	 * Our WP_Webhooks_Pro_Run constructor.
	 */
	function __construct(){
		$this->page_name    = WPWHPRO()->settings->get_page_name();
		$this->page_title   = WPWHPRO()->settings->get_page_title();
		$this->add_hooks();
	}

	/**
	 * Define all of our necessary hooks
	 */
	private function add_hooks(){

		add_action( 'plugin_action_links_' . WPWH_PLUGIN_BASE, array($this, 'plugin_action_links') );

		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_user_submenu' ), 150 );
		add_filter( 'wpwhpro/helpers/throw_admin_notice_bootstrap', array( $this, 'throw_admin_notice_bootstrap' ), 100, 1 );

		// Ajax related
		add_action( 'wp_ajax_ironikus_add_webhook_trigger',  array( $this, 'ironikus_add_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_add_webhook_action',  array( $this, 'ironikus_add_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_remove_webhook_trigger',  array( $this, 'ironikus_remove_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_remove_webhook_action',  array( $this, 'ironikus_remove_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_change_status_webhook_action',  array( $this, 'ironikus_change_status_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_test_webhook_trigger',  array( $this, 'ironikus_test_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_trigger_settings',  array( $this, 'ironikus_save_webhook_trigger_settings' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_action_settings',  array( $this, 'ironikus_save_webhook_action_settings' ) );
		add_action( 'wp_ajax_ironikus_add_authentication_template',  array( $this, 'ironikus_add_authentication_template' ) );
		add_action( 'wp_ajax_ironikus_load_authentication_template_data',  array( $this, 'ironikus_load_authentication_template_data' ) );
		add_action( 'wp_ajax_ironikus_save_authentication_template',  array( $this, 'ironikus_save_authentication_template' ) );
		add_action( 'wp_ajax_ironikus_delete_authentication_template',  array( $this, 'ironikus_delete_authentication_template' ) );
		add_action( 'wp_ajax_ironikus_manage_extensions',  array( $this, 'ironikus_manage_extensions' ) );

		// Load admin page tabs
		add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
		add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );

		// Validate settings
		add_action( 'admin_init',  array( $this, 'ironikus_save_main_settings' ) );
		add_action( 'admin_init',  array( $this, 'ironikus_save_whitelabel_settings' ) );

		// Setup actions
		add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_webhook_actions_content' ), 10 );
		add_action( 'wpwhpro/webhooks/add_webhooks_actions', array( $this, 'add_webhook_actions' ), 1000, 3 );

		// Setup triggers
		add_action( 'plugins_loaded', array( $this, 'add_webhook_triggers' ), 10 );
		add_filter( 'wpwhpro/webhooks/get_webhooks_triggers', array( $this, 'add_webhook_triggers_content' ), 10 );

		//Reset wp webhooks
		add_action( 'admin_init', array( $this, 'reset_wpwhpro_data' ), 10 );

		//Template validations
		add_filter( 'wpwhpro/admin/webhooks/webhook_data', array( $this, 'apply_authentication_template_data' ), 100, 5 );
		add_filter( 'wpwhpro/admin/webhooks/webhook_http_args', array( $this, 'apply_authentication_template_header' ), 100, 5 );

	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->page_name ), WPWHPRO()->helpers->translate('Settings', 'plugin-page') );

		array_unshift( $links, $settings_link );

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://ironikus.com/products/?utm_source=wp-webhooks-pro&utm_medium=plugin-overview-shop-button&utm_campaign=WP%20Webhooks%20Pro', WPWHPRO()->helpers->translate('Our Shop', 'plugin-page') );

		return $links;
	}

	/**
	 * ######################
	 * ###
	 * #### SCRIPTS & STYLES
	 * ###
	 * ######################
	 */

	/**
	 * Register all necessary scripts and styles
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts_and_styles() {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {
			$is_dev_mode = defined( 'WPWH_DEV' ) && WPWH_DEV === true;
			wp_enqueue_style( 'wpwhpro-google-fonts', 'https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;700&family=Poppins:wght@500&display=swap', array(), null );

			// wp_enqueue_style( 'wpwhpro-admin-styles-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/styles.min.css', array(), WPWHPRO_VERSION, 'all' );
			wp_enqueue_style( 'wpwhpro-admin-styles', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWHPRO_VERSION, 'all' );

			wp_enqueue_script( 'jquery-ui-sortable');

			wp_enqueue_script( 'wpwhpro-admin-vendors', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
			wp_enqueue_script( 'wpwhpro-admin-scripts', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
			wp_localize_script( 'wpwhpro-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
				'plugin_url' => WPWHPRO_PLUGIN_URL,
			));
			// wp_enqueue_script( 'wpwhpro-admin-scripts-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets-old/dist/js/admin-scripts.js', array( 'jquery' ), WPWHPRO_VERSION, true );
		}
	}

	/**
	 * Register the bootstrap styling for posts on our own settings page
	 *
	 * @since    1.0.0
	 */
	public function throw_admin_notice_bootstrap( $bool ) {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {
			$bool = true;
		}

		return $bool;
	}

	/*
     * Functionality to save the main settings of the settings page
     */
	public function ironikus_save_main_settings(){

        if( ! is_admin() || ! WPWHPRO()->helpers->is_page( $this->page_name ) ){
			return;
		}

		if( ! isset( $_POST['wpwh_settings_submit'] ) ){
			return;
		}

		$settings_nonce_data = WPWHPRO()->settings->get_settings_nonce();

		if ( ! check_admin_referer( $settings_nonce_data['action'], $settings_nonce_data['arg'] ) ){
			return;
		}

		if ( ! current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwh-save-settings' ) ) ){
			return;
		}

		$current_url = WPWHPRO()->helpers->get_current_url();

		WPWHPRO()->settings->save_settings( $_POST );

		wp_redirect( $current_url );
		exit;

    }

	/**
	 * ######################
	 * ###
	 * #### AJAX
	 * ###
	 * ######################
	 */

	/**
	 * Handler for dealing with the ajax based webhook triggers
	 */
	public function ironikus_add_webhook_trigger(){
		check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

		$percentage_escape		= '{irnksescprcntg}';
		$webhook_url            = isset( $_REQUEST['webhook_url'] ) ? $_REQUEST['webhook_url'] : '';
		$webhook_url 			= str_replace( '%', $percentage_escape, $webhook_url );
		$webhook_url 			= sanitize_text_field( $webhook_url );
		$webhook_url 			= str_replace( $percentage_escape, '%', $webhook_url );

        $webhook_slug            = isset( $_REQUEST['webhook_slug'] ) ? sanitize_title( $_REQUEST['webhook_slug'] ) : '';
        $webhook_current_url    = isset( $_REQUEST['current_url'] ) ? sanitize_text_field( $_REQUEST['current_url'] ) : '';
        $webhook_group          = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback       = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks               = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
		$response               = array( 'success' => false );
		$url_parts              = parse_url( $webhook_current_url );
		parse_str($url_parts['query'], $query_params);
		$clean_url              = strtok( $webhook_current_url, '?' );

		if( ! empty( $webhook_slug ) ){
			$new_webhook = $webhook_slug;
		} else {
			$new_webhook = strtotime( date( 'Y-n-d H:i:s' ) ) . 999 . rand( 10, 9999 );
		}

        if( ! isset( $webhooks[ $new_webhook ] ) ){
            WPWHPRO()->webhook->create( $new_webhook, 'trigger', array( 'group' => $webhook_group, 'webhook_url' => $webhook_url ) );

	        $response['success']            = true;
	        $response['webhook']            = $new_webhook;
	        $response['webhook_group']      = $webhook_group;
	        $response['webhook_url']        = $webhook_url;
	        $response['webhook_callback']   = $webhook_callback;
	        $response['delete_url']         = WPWHPRO()->helpers->built_url( $clean_url, array_merge( $query_params, array( 'wpwhpro_delete' => $new_webhook, ) ) );
        } else {
			$response['msg'] = WPWHPRO()->helpers->translate( 'This key already exists. Please use a different one.', 'wpwhpro-page-actions' );
		}


        echo json_encode( $response );
		die();
	}

	/**
	 * Handler for creating a new webhook action url
	 *
	 * @return void
	 */
	public function ironikus_add_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook_slug   = isset( $_REQUEST['webhook_slug'] ) ? $_REQUEST['webhook_slug'] : '';
        $webhooks 		= WPWHPRO()->webhook->get_hooks( 'action' ) ;
		$response       = array( 'success' => false );

		//Sanitize webhook slug properly
		$webhook_slug = str_replace( '§', '', $webhook_slug );
		$webhook_slug = sanitize_title( $webhook_slug );

		if( ! isset( $webhooks[ $webhook_slug ] ) ){
			WPWHPRO()->webhook->create( $webhook_slug, 'action' );

			$webhooks_updated 		= WPWHPRO()->webhook->get_hooks( 'action' ) ;
			if( isset( $webhooks_updated[ $webhook_slug ] ) ){
				$response['success'] = true;
				$response['webhook'] = $webhook_slug;
				$response['webhook_action_delete_name'] = WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' );
				$response['webhook_url'] = WPWHPRO()->webhook->built_url( $webhook_slug, $webhooks_updated[ $webhook_slug ]['api_key'] );
				$response['webhook_api_key'] = $webhooks_updated[ $webhook_slug ]['api_key'];
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Remove the action via ajax
     */
	public function ironikus_remove_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
		$response       = array( 'success' => false );

		$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'action' );
		if( $check ){
			$response['success'] = true;
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Change the status of the action via ajax
     */
	public function ironikus_change_status_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_status = isset( $_REQUEST['webhook_status'] ) ? sanitize_title( $_REQUEST['webhook_status'] ) : '';
		$response       = array( 'success' => false, 'new_status' => '', 'new_status_name' => '' );

		$new_status = null;
		$new_status_name = null;
		switch( $webhook_status ){
			case 'active':
				$new_status = 'inactive';
				$new_status_name = 'Activate';
				break;
			case 'inactive':
				$new_status = 'active';
				$new_status_name = 'Deactivate';
				break;
		}

		if( ! empty( $webhook ) ){

			if( ! empty( $webhook_group ) ){
				$check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
					'status' => $new_status
				) );
			} else {
				$check = WPWHPRO()->webhook->update( $webhook, 'action', '', array(
					'status' => $new_status
				) );
			}

			if( $check ){
				$response['success'] = true;
				$response['new_status'] = $new_status;
				$response['new_status_name'] = $new_status_name;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Remove the trigger via ajax
     */
	public function ironikus_remove_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group  = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$webhooks       = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
		$response       = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'trigger', $webhook_group );
			if( $check ){
			    $response['success'] = true;
            }
		}


        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_test_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback   = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks           = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
        $response           = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			if( ! empty( $webhook_callback ) ){
				$data = apply_filters( 'ironikus_demo_' . $webhook_callback, array(), $webhook, $webhook_group, $webhooks[ $webhook ] );

				$response_data = WPWHPRO()->webhook->post_to_webhook( $webhooks[ $webhook ], $data, array( 'blocking' => true ), true );

				if ( ! empty( $response_data ) ) {
					$response['data']       = $response_data;
					$response['success']    = true;
				}
			}
		}

        echo json_encode( $response );
		die();
    }

	/*
     * Functionality to add the currently chosen data mapping
     */
	public function ironikus_add_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $auth_template    = isset( $_REQUEST['auth_template'] ) ? sanitize_title( $_REQUEST['auth_template'] ) : '';
        $auth_type    = isset( $_REQUEST['auth_type'] ) ? sanitize_title( $_REQUEST['auth_type'] ) : '';
		$response           = array( 'success' => false );

		if( ! empty( $auth_template ) && ! empty( $auth_type ) ){
		    $check = WPWHPRO()->auth->add_template( $auth_template, $auth_type );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to load the currently chosen authentication
     */
	public function ironikus_load_authentication_template_data(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $auth_template_id    = isset( $_REQUEST['auth_template_id'] ) ? intval( $_REQUEST['auth_template_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $auth_template_id ) && is_numeric( $auth_template_id ) ){
		    $check = WPWHPRO()->auth->get_auth_templates( intval( $auth_template_id ) );

		    if( ! empty( $check ) ){

				$response['success'] = true;
		        $response['text'] 	 = array(
					'save_button_text' => WPWHPRO()->helpers->translate( 'Save Template', 'wpwhpro-page-authentication' ),
					'delete_button_text' => WPWHPRO()->helpers->translate( 'Delete Template', 'wpwhpro-page-authentication' ),
				);
				$response['id'] = '';
				$response['content'] = '';

				if( isset( $check->id ) && ! empty( $check->id ) ){
					$response['id'] = $check->id;
				}

				$template_data = ( isset( $check->template ) && ! empty( $check->template ) ) ? base64_decode( $check->template ) : '';

				if( isset( $check->auth_type ) && ! empty( $check->auth_type )  ){
					$response['content'] = WPWHPRO()->auth->get_html_fields_form( $check->auth_type, $template_data );
				}

            }
        }

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to save the current authentication template
     */
	public function ironikus_save_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_auth_id    = isset( $_REQUEST['data_auth_id'] ) ? intval( $_REQUEST['data_auth_id'] ) : '';
        $datastring    = isset( $_REQUEST['datastring'] ) ? $_REQUEST['datastring'] : '';
		$response           = array( 'success' => false );

		parse_str( $datastring, $authentication_template );

		//Maybe validate the incoming template data
		if( empty( $authentication_template ) ){
			$authentication_template = array();
		}

		//Validate arrays
		if( is_array( $authentication_template ) ){
			$authentication_template = json_encode( $authentication_template );
		}

		if( ! empty( $data_auth_id ) && is_string( $authentication_template ) ){

			if( WPWHPRO()->helpers->is_json( $authentication_template ) ){
				$check = WPWHPRO()->auth->update_template( $data_auth_id, array(
					'template' => $authentication_template
				) );

				if( ! empty( $check ) ){

					$response['success'] = true;

				}
			}
		}

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to delete the currently chosen authentication template
     */
	public function ironikus_delete_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_auth_id    = isset( $_REQUEST['data_auth_id'] ) ? intval( $_REQUEST['data_auth_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $data_auth_id ) && is_numeric( $data_auth_id ) ){
		    $check = WPWHPRO()->auth->delete_authentication_template( intval( $data_auth_id ) );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
	}

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_save_webhook_trigger_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$trigger_settings   = ( isset( $_REQUEST['trigger_settings'] ) && ! empty( $_REQUEST['trigger_settings'] ) ) ? $_REQUEST['trigger_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $trigger_settings, $trigger_settings_data );

		if( ! empty( $webhook_group ) && ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
                'settings' => $trigger_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to save all available webhook actions
     */
	public function ironikus_save_webhook_action_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
        $action_settings   = ( isset( $_REQUEST['action_settings'] ) && ! empty( $_REQUEST['action_settings'] ) ) ? $_REQUEST['action_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $action_settings, $action_settings_data );

		if( ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'action', '', array(
                'settings' => $action_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Manage WP Webhooks extensions
     */
	public function ironikus_manage_extensions(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $extension_slug            = isset( $_REQUEST['extension_slug'] ) ? sanitize_text_field( $_REQUEST['extension_slug'] ) : '';
        $extension_status            = isset( $_REQUEST['extension_status'] ) ? sanitize_text_field( $_REQUEST['extension_status'] ) : '';
        $extension_download            = isset( $_REQUEST['extension_download'] ) ? sanitize_text_field( $_REQUEST['extension_download'] ) : '';
        $extension_id            = isset( $_REQUEST['extension_id'] ) ? intval( $_REQUEST['extension_id'] ) : '';
        $extension_version            = isset( $_REQUEST['extension_version'] ) ? sanitize_text_field( $_REQUEST['extension_version'] ) : '';
		$response           = array( 'success' => false );

		if( empty( $extension_slug ) || empty( $extension_status ) ){
			$response['msg'] = WPWHPRO()->helpers->translate('An error occured while doing this action.', 'ajax-settings');
			return $response;
		}

		switch( $extension_status ){
			case 'activated': //runs when the "Deactivate" button was clicked
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_deactivate( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully deactivated.', 'ajax-settings');
				break;
			case 'deactivated': //runs when the "Activate" button was clicked
				$response['new_class'] = 'text-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_activate( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully activated.', 'ajax-settings');
				break;
			case 'uninstalled': //runs when the "Install" button was clicked
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['delete_name'] = WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-extensions' );
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_install( $extension_slug, $extension_download, $extension_id, $extension_version );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully installed.', 'ajax-settings');
				break;
			case 'update_active': //runs when the "Update" button was clicked and the previous status was active
				$response['new_class'] = 'text-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_update( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'update_deactive': //runs when the "Update" button was clicked and the previous status was inactive
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_update( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'delete': //runs when the "Delete" link was clicked
				$response['new_class'] = 'text-secondary';
				$response['new_status'] = 'uninstalled';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Install', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extension_uninstall( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully deleted.', 'ajax-settings');
				break;
		}

        echo json_encode( $response );
		die();
    }

	/**
	 * ######################
	 * ###
	 * #### MANAGE EXTENSIONS
	 * ###
	 * ######################
	 */

	 public function manage_extensions_deactivate( $slug ){

		if( empty( $slug ) ){
			return false;
		}

		if ( is_plugin_active( $slug ) ) {
			deactivate_plugins( $slug );
		}

		return true;
	 }

	 public function manage_extensions_activate( $slug ){

		if( empty( $slug ) ){
			return false;
		}

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		$activate = activate_plugin( $slug );
		if (is_null($activate)) {
			return true;
		}

		return false;
	 }

	 public function manage_extensions_install( $slug, $dl, $item_id, $version ){

		if( empty( $slug ) || empty( $dl ) ){
			return false;
		}

		if ( WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		$check = $this->manage_extension_install( $slug, $dl );

		return $check;
	 }

	 public function manage_extension_install( $slug, $dl ){

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-upgrader-skin.php';

		if( ! class_exists( 'Plugin_Upgrader' ) || ! class_exists( 'WP_Webhooks_Upgrader_Skin' ) ){
			return false;
		}

		wp_cache_flush();
		$skin = new WP_Webhooks_Upgrader_Skin( array( 'plugin' => $slug ) );
		$upgrader = new Plugin_Upgrader( $skin );
		$installed = $upgrader->install( $dl );
		wp_cache_flush();

		if( ! is_wp_error( $installed ) && $installed ) {
			return true;
		} else {
			return false;
		}

	 }

	 public function manage_extension_uninstall( $slug ){

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';

		if( ! function_exists( 'delete_plugins' ) ){
			return false;
		}

		if ( is_plugin_active( $slug ) ) {
			deactivate_plugins( $slug );
		}

		$deleted = delete_plugins( array( $slug ) );

		if( ! is_wp_error( $deleted ) && $deleted ) {
			return true;
		} else {
			return false;
		}

	 }

	 public function manage_extensions_update( $slug, $dl, $item_id, $version ){

		if( empty( $slug ) || empty( $dl ) ){
			return false;
		}

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		$check = $this->manage_extension_update( $slug, $dl );

		return $check;
	 }

	 public function manage_extension_update( $slug, $dl ){

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-upgrader-skin.php';

		if( ! class_exists( 'Plugin_Upgrader' ) || ! class_exists( 'WP_Webhooks_Upgrader_Skin' ) ){
			return false;
		}

		wp_cache_flush();
		$skin = new WP_Webhooks_Upgrader_Skin( array( 'plugin' => $slug ) );
		$upgrader = new Plugin_Upgrader( $skin );
		$updated = $upgrader->upgrade( $slug );
		wp_cache_flush();

		if( ! is_wp_error( $updated ) && $updated ) {
			return true;
		} else {
			return false;
		}

	 }

	/**
	 * ######################
	 * ###
	 * #### MENU TEMPLATE ITEMS
	 * ###
	 * ######################
	 */

	/**
	 * Add our custom admin user page
	 */
	public function add_user_submenu(){
		add_submenu_page( 'options-general.php', WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-title' ), WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-site-title' ), WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ), $this->page_name, array( $this, 'render_admin_submenu_page' ) );
	}

	/**
	 * Render the admin submenu page
	 *
	 * You need the specified capability to edit it.
	 */
	public function render_admin_submenu_page(){
		if( ! current_user_can( WPWHPRO()->settings->get_admin_cap('admin-submenu-page') ) ){
			wp_die( WPWHPRO()->helpers->translate( WPWHPRO()->settings->get_default_string( 'sufficient-permissions' ), 'admin-submenu-page-sufficient-permissions' ) );
		}

		include( WPWH_PLUGIN_DIR . 'core/includes/partials/wpwhpro-page-display.php' );

	}

	/**
	 * Register all of our default tabs to our plugin page
	 *
	 * @param $tabs - The previous tabs
	 *
	 * @return array - Return the array of all available tabs
	 */
	public function add_main_settings_tabs( $tabs ){

		$tabs['home']           = WPWHPRO()->helpers->translate( 'Home', 'admin-menu' );

		$tabs['send-data']      = array(
			'label' => WPWHPRO()->helpers->translate( 'Send Data', 'admin-menu' ),
			'items' => array(
				'send-data'  	=> WPWHPRO()->helpers->translate( 'All Triggers', 'admin-menu' ),
			)
		);

		$tabs['receive-data']   = array(
			'label' => WPWHPRO()->helpers->translate( 'Receive Data', 'admin-menu' ),
			'items' => array(
				'receive-data'  	=> WPWHPRO()->helpers->translate( 'All Actions', 'admin-menu' ),
			),
		);

		$tabs['authentication'] = WPWHPRO()->helpers->translate( 'Authentication', 'admin-menu' );
		$tabs['settings']   = array(
			'label' => WPWHPRO()->helpers->translate( 'Settings', 'admin-menu' ),
			'items' => array(
				'settings'  		=> WPWHPRO()->helpers->translate( 'All Settings', 'admin-menu' ),
				'extensions'  		=> WPWHPRO()->helpers->translate( 'Extensions', 'admin-menu' ),
			),
		);

		$tabs['pro'] = WPWHPRO()->helpers->translate( 'Pro', 'admin-menu' );

		return $tabs;

	}

	/**
	 * Load the content for our plugin page based on a specific tab
	 *
	 * @param $tab - The currently active tab
	 */
	public function add_main_settings_content( $tab ){

		switch($tab){
			case 'pro':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/pro.php' );
				break;
			case 'send-data':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/send-data.php' );
				break;
			case 'recieve-data': // Keep it backwards compatible
			case 'receive-data':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/receive-data.php' );
				break;
			case 'settings':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/settings.php' );
				break;
			case 'authentication':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/authentication.php' );
				break;
			case 'extensions':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/extensions.php' );
				break;
			case 'home':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/home.php' );
				break;
		}

	}

	/**
	 * ######################
	 * ###
	 * #### TEMPLATE MAPPING
	 * ###
	 * ######################
	 */

	 public function apply_authentication_template_data( $data, $response, $webhook, $args, $authentication_data ){

		if( empty( $authentication_data ) ){
			return $data;
		}

		$auth_type = $authentication_data['auth_type'];
		$auth_data = $authentication_data['data'];

		switch( $auth_type ){
			case 'api_key':
				$data = WPWHPRO()->auth->validate_http_api_key_body( $data, $auth_data );
			break;
		}

		return $data;
	 }

	 public function apply_authentication_template_header( $http_args, $args, $url, $webhook, $authentication_data ){

		if( empty( $authentication_data ) ){
			return $http_args;
		}

		$auth_type = $authentication_data['auth_type'];
		$auth_data = $authentication_data['data'];

		switch( $auth_type ){
			case 'api_key':
				$http_args = WPWHPRO()->auth->validate_http_api_key_header( $http_args, $auth_data );
			break;
			case 'bearer_token':
				$http_args = WPWHPRO()->auth->validate_http_bearer_token_header( $http_args, $auth_data );
			break;
			case 'basic_auth':
				$http_args = WPWHPRO()->auth->validate_http_basic_auth_header( $http_args, $auth_data );
			break;
		}

		return $http_args;
	 }

	/**
	 * ######################
	 * ###
	 * #### SETTINGS EXTENSIONS
	 * ###
	 * ######################
	 */

	/*
	 * Reset the settings and webhook data
	 */
	public function reset_wpwhpro_data(){

	    if( ! is_admin() || ! is_user_logged_in() ){
	        return;
        }

		$current_url_full = WPWHPRO()->helpers->get_current_url();
		$reset_all = get_option( 'wpwhpro_reset_data' );
		if( $reset_all && $reset_all === 'yes' ){
			delete_option( 'wpwhpro_reset_data' );

			WPWHPRO()->webhook->reset_wpwhpro();

			wp_redirect( $current_url_full );
			die();
		}
    }

	/**
	 * ######################
	 * ###
	 * #### ACTIONS
	 * ###
	 * ######################
	 */

	/*
	 * Register all available action webhooks
	 */
	public function add_webhook_actions_content( $actions ){

	    //User actions
		$actions[] = $this->action_create_user_content();
		$actions[] = $this->action_delete_user_content();
		$actions[] = $this->action_get_users_content();
		$actions[] = $this->action_get_user_content();

		//Post actions
		$actions[] = $this->action_create_post_content();
		$actions[] = $this->action_delete_post_content();
		$actions[] = $this->action_get_posts_content();
		$actions[] = $this->action_get_post_content();

		//Custom actions
		$actions[] = $this->action_custom_action_content();

		//Testing actions
		$actions[] = $this->action_ironikus_test_content();

		return $actions;

	}

	/*
	 * Add the callback function for a defined action
	 */
	public function add_webhook_actions( $action, $webhook, $api_key ){

		$default_return = array(
            'success' => false
        );

		switch( $action ){
			case 'create_user':
			    $this->action_create_user();
				break;
			case 'update_user':
				$this->action_create_user( true );
				break;
			case 'delete_user':
				$this->action_delete_user();
				break;
			case 'get_users':
				$this->action_get_users();
				break;
			case 'get_user':
				$this->action_get_user();
				break;
			case 'create_post':
				$this->action_create_post();
				break;
			case 'delete_post':
				$this->action_delete_post();
				break;
			case 'get_posts':
				$this->action_get_posts();
				break;
			case 'get_post':
				$this->action_get_post();
				break;
			case 'custom_action':
				$this->action_custom_action();
				break;
			case 'ironikus_test':
				$this->action_ironikus_test();
				break;
		}

		$default_return['data'] = $action;
		$default_return['msg'] = WPWHPRO()->helpers->translate("It looks like your current webhook call has no action argument defined, it is deactivated or it does not have any action function.", 'action-add-webhook-actions' );

		WPWHPRO()->webhook->echo_response_data( $default_return );
		die();
	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_user_content(){

		$translation_ident = 'action-create-user-content';

		$parameter = array(
			'user_email'        => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'This field is required. Include the email for the user.', $translation_ident ) ),
			'first_name'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the user.', $translation_ident ) ),
			'last_name'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the user.', $translation_ident ) ),
			'nickname'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'The nickname. Please note that the nickname will be sanitized by WordPress automatically.', $translation_ident ) ),
			'user_login'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'A string with which the user can log in to your site.', $translation_ident ) ),
			'display_name'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'The name that will be seen on the frontend of your site.', $translation_ident ) ),
			'user_nicename'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'A URL-friendly name. Default is user\' username.', $translation_ident ) ),
			'description'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'A description for the user that will be available on the profile page.', $translation_ident ) ),
			'rich_editing'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether the user should be able to use the Rich editor. Set it to "yes" or "no". Default "no".', $translation_ident ) ),
			'user_registered'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date the user gets registered. Date structure: Y-m-d H:i:s', $translation_ident ) ),
			'user_url'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'Include a website url.', $translation_ident ) ),
			'role'              => array(
				'short_description' => WPWHPRO()->helpers->translate( 'The main user role. If not set, default is subscriber.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "<p>The slug of the role. The default roles have the following slugs: </p>", $translation_ident ) . '<p><ul><li>administrator</li> <li>editor</li> <li>author</li> <li>contributor</li> <li>subscriber</li></ul></p>',
			),
			'additional_roles'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'This allows to add multiple roles to a user.', $translation_ident ) ),
			'user_pass'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user password. If not defined, we generate a 32 character long password dynamically.', $translation_ident ) ),
			'send_email'        => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user with the data.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing his login details.", $translation_ident )
			),
			'do_action'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add or remove additional roles on the user. There are two possible ways of doing that:", $translation_ident ); ?>
<ol>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add or remove the user roles using a simple string. To make it work, simply add the slug of the role and define the action (add/remove) after, separated by double points (:). If you want to add multiple roles, simply separate them with a semicolon (;). Please refer to the example down below.", $translation_ident ); ?>
        <pre>editor:add;custom-role:add;custom-role-1:remove</pre>
    </li>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "We also support a JSON formatted string, which contains the role slug as the JSON key and the action (add/remove) as the value. Please refer to the example below:", $translation_ident ); ?>
        <pre>{
  "editor": "add",
  "custom-role": "add",
  "custom-role-1": "remove"
}</pre>
    </li>
</ol>
		<?php
		$parameter['additional_roles']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the create_user action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user_data, $user_id, $user_meta, $update ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$user_data</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to create the user.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the user id of the newly created user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_meta</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the unformatted user meta as you sent it over within the webhook request as a string.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$update</strong> (bool)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This value will be set to 'false' for the create_user webhook.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user data with the key "user_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
        );

		$return_code_data = array(
			"success" => true,
			"msg" => "User successfully created.",
			"data" => array(
				"user_id" => 131,
				"user_data" => array(
					"user_email" => "demo_user@email.email",
					"role" => "subscriber",
					"nickname" => "nickname",
					"user_login" => "userlogin",
					"user_nicename" => "The Nice Name",
					"description" => "This is a user description",
					"rich_editing" => true,
					"user_registered" => "2020-12-11 14:10:10",
					"user_url" => "https://somedomain.com",
					"display_name" => "username",
					"first_name" => "Jon",
					"last_name" => "Doe",
					"user_pass" => "SomeCustomUserpass123",
					"additional_roles" => "author:add"
				),
			)
		);
		ob_start();
			echo WPWHPRO()->helpers->display_var( $return_code_data );
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-create_user.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'create_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => '<pre>' . $returns_code . '</pre>',
			'short_description' => WPWHPRO()->helpers->translate( 'Create a new user via webhooks.', $translation_ident ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to delete a specified user
	 */
	public function action_delete_user_content(){

		$translation_ident = "action-delete-user-content";

		$parameter = array(
			'user_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the numeric id of the user.', $translation_ident ) ),
			'user_email'    => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the assigned email of the user.', $translation_ident ) ),
			'send_email'    => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user that the account got deleted.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing the notice of the deleted account.", $translation_ident )
			),
			'remove_from_network'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to delete a user from the whole network. WARNING: This will delete all posts authored by the user. Default: "no"', $translation_ident ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_user</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user, $user_id, $user_email, $send_email ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$user</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the WordPress user object.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the user id of the deleted user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_email</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the user email.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$send_email</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the send_email argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user delete success boolean with the key "user_deleted". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array(
        'user_deleted' => false,
        'user_id' => 0
    )
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-delete_user.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'delete_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Delete a user via ' . $this->page_title . '.', 'action-create-user-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function action_get_users_content(){

		$translation_ident = 'action-get_users-content';

		$parameter = array(
			'arguments'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_User_Query notation.', $translation_ident ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: get_results', $translation_ident ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON formatted string, which includes certain arguments from the WordPress user query called <strong>WP_User_Query</strong>. For further details, please check out the following link:", $translation_ident ); ?>
<br>
<a href="https://codex.wordpress.org/Class_Reference/WP_User_Query" title="wordpress.org" target="_blank">https://codex.wordpress.org/Class_Reference/WP_User_Query</a>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example on how the JSON is set up:", $translation_ident ); ?>
<pre>{"search":"Max","number":5}</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above will filter the users for the name \"Max\" and returns maximum five users with that name.", $translation_ident ); ?>
		<?php
		$parameter['arguments']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the output of the query using the <strong>return_only</strong> parameter. This allows you to, for example, output either only the search results, the total count, the whole query object or any combination in between. Here is an example that returns all of the data:", $translation_ident ); ?>
<pre>get_total,get_results,all,meta_data<?php echo ( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ) ? 'acf_data' : ''; ?></pre>
<?php echo WPWHPRO()->helpers->translate( "The <code>all</code> argument returns the whole WP_Query object, but not the results of the query. If you want the results of the query, you can use the <code>get_results</code> value. To use the <code>meta_data</code> setting, you also need to set the <code>get_results</code> key since the meta data will be attached to every user entry.", $translation_ident ); ?>
<?php 

if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
	echo '<br>' . WPWHPRO()->helpers->translate( "Since you have Advanced Custom Fields installed and active, you can also use the <code>acf_data</code> value for the <code>return_only</code> argument. Please keep in mind that you need to set the <code>get_results</code> argument as well.", $translation_ident );
}

?>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_users</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $user_query, $args, $return_only ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_query</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The full WP_User_Query object.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$args</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted JSON construct that was sent by the caller within the arguments argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_only</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string that was sent by the caller via the return_only argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user query. This depends on the parameters you send.', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array()
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-get_users.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'get_users',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Search for users on your WordPress website', $translation_ident ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to get the user
	 */
	public function action_get_user_content(){

		$translation_ident = "action-get_user-content";

		$parameter = array(
			'user_value'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id of the user. You can also use certain other values by changing the value_type argument.', $translation_ident ) ),
			'value_type'    => array(
				'short_description' => WPWHPRO()->helpers->translate( 'You can choose between certain value types. Possible: id, slug, email, login', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "This argument is used to change the data you can add within the <strong>user_value</strong> argument. Possible values are: <strong>id, ID, slug, email, login</strong>", $translation_ident )
			),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_user</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $user_value, $value_type, $user ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_value</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The value you included into the user_value argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$value_type</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The value you included into the value_type argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user</strong> (mixed)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns null in case an the user_value wasn't set, the user object on success or a wp_error object in case an error occurs.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user qury. This depends on the parameters you send.', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array()
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-get_user.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'get_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Returns the object of a user', $translation_ident ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_post_content(){

		$translation_ident = 'action-create-post-content';

		$parameter = array(
			'post_author'           => array(
				'short_description' => WPWHPRO()->helpers->translate( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", $translation_ident ),
			),
			'post_date'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', $translation_ident ) ),
			'post_date_gmt'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', $translation_ident ) ),
			'post_content'          => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post content. Default empty.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", $translation_ident )
			),
			'post_content_filtered' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The filtered post content. Default empty.', $translation_ident ) ),
			'post_title'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post title. Default empty.', $translation_ident ) ),
			'post_excerpt'          => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post excerpt. Default empty.', $translation_ident ) ),
			'post_status'           => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post status. Default \'draft\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post status defines further details about how your post will be treated. By default, WordPress offers the following post statuses: <strong>draft, pending, private, publish</strong>. Please note that other plugins can extend the post status values to offer a bigger variety, e.g. Woocommerce.", $translation_ident ),
			),
			'post_type'             => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post type. Default \'post\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post type determines to which group of posts your currently created post belongs. Please use the slug of the post type to assign it properly.", $translation_ident ),
			),
			'comment_status'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', $translation_ident ) ),
			'ping_status'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', $translation_ident ) ),
			'post_password'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The password to access the post. Default empty.', $translation_ident ) ),
			'post_name'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', $translation_ident ) ),
			'to_ping'               => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', $translation_ident ) ),
			'pinged'                => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', $translation_ident ) ),
			'post_parent'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', $translation_ident ) ),
			'menu_order'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', $translation_ident ) ),
			'post_mime_type'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The mime type of the post. Default empty.', $translation_ident ) ),
			'guid'                  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', $translation_ident ) ),
			'import_id'             => array(
				'short_description' => WPWHPRO()->helpers->translate( '(integer) In case you want to give your post a specific post id, please define it here.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "This argument allows you to define a suggested post ID for your post. In case the ID is already taken, the post will be created using the default behavior by asigning automatically an ID. ", $translation_ident ),
			),
			'post_category'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of category IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', $translation_ident ) ),
			'tags_input'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', $translation_ident ) ),
			'tax_input'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty.', $translation_ident ) ),
			'wp_error'              => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", $translation_ident ),
			),
			'do_action'             => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument supports the default tags_input variable of the <strong>wp_insert_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example:", $translation_ident ); ?>
<pre>342,5678,2</pre>
<?php echo WPWHPRO()->helpers->translate( "This argument supports a comma separated list of tag names, slugs, or IDs.", $translation_ident ); ?>
		<?php
		$parameter['tags_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add/append/delete any kind of taxonomies on your post. It uses a custom functionality that adds the taxonomies independently of the <strong>wp_update_post()</strong> function.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "To make it work, we offer certain different features and methods to make the most out of the taxonomy management. Down below, you will find further information about the whole functionality.", $translation_ident ); ?>
<ol>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too. In case you use more complex taxonomies that use semicolons or double points within the slugs, you need to use the JSON method.", $translation_ident ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Replace existing taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "This method allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", $translation_ident ); ?>
                <pre>taxonomy_1,tax_item_1:tax_item_2:tax_item_3;taxonomy_2,tax_item_5:tax_item_7:tax_item_8</pre>
                <?php echo WPWHPRO()->helpers->translate( "To separate the taxonomies from the single taxonomy items, please use a comma \",\". In case you want to add multiple items per taxonomy, you can separate them via a double point \":\". To separate multiple taxonomies from each other, please separate them with a semicolon \";\" (It is not necessary to set a semicolon at the end of the last one)", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove all taxonomy items for a single taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all;</strong> in front of a semicolon-separated list of the taxonomies you want to remove all items for. Here is an example:", $translation_ident ); ?>
                <pre>ironikus-remove-all;taxonomy_1;taxonomy_2</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove single taxonomy items for a taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also remove only single taxonomy items for one or multiple taxonomies. Here is an example:", $translation_ident ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2-ironikus-delete:value_3;taxonomy_2,value_5:value_6:value_7-ironikus-delete</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2. We also add the taxonomy items value_1, value_3, value_5 and value_6. We also remove the taxonomy items value_2 and value_7.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Append taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append;</strong> at the beginning of the string.", $translation_ident ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2:value_3;taxonomy_2,value_1:value_2:value_3</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2 with multiple taxonomy items on the post. The already assigned ones won't be replaced.", $translation_ident ); ?>
            </li>
        </ul>
    </li>
    <li>
    <strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too.", $translation_ident ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Replace existing taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "This JSON allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", $translation_ident ); ?>
                <pre>{
  "category": [
    "test-category",
    "second-category"
  ],
  "post_tag": [
    "dog",
    "male",
    "simple"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "The key on the first layer of the JSON is the slug of the taxonomy. As a value, it accepts multiple slugs of the single taxonomy terms. To add multiple taxonomies, simply append them on the first layer of the JSON.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove all taxonomy items for a single taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all</strong> as a separate value with the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. Here is an example:", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-remove-all",
  "category": [],
  "post_tag": []
}</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Append taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append</strong> to the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. All the taxonomies you add after, will be added to the existing ones on the post.", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-append",
  "category": [
    "test-category",
    "second-category"
  ],
  "post_tag": [
    "dog"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies category and post_tag with multiple taxonomy items on the post. The already assigned ones won't be replaced.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove single taxonomy items for a taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also remove only single taxonomy items for one or multiple taxonomies. To do that, simply append <strong>-ironikus-delete</strong> at the end of the taxonomy term slug. This specific taxonomy term will then be removed from the post. Here is an example:", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-append",
  "category": [
    "test-category",
    "second-category-ironikus-delete"
  ],
  "post_tag": [
    "dog-ironikus-delete"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies category and post_tag. We also add the taxonomy item test-category. We also remove the taxonomy items second-category and dog.", $translation_ident ); ?>
            </li>
        </ul>
    </li>
</ol>
		<?php
		$parameter['tax_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the create_post action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post_data, $post_id, $meta_input, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$post_data</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to create the post and some additional data as the meta input.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the post id of the newly created post. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$meta_input</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the unformatted post meta as you sent it over within the webhook request as a string.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success'   => false,
    'msg'       => '',
    'data'      => array(
        'post_id' => null,
        'post_data' => null
    )
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-create_post.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'create_post',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Insert/Create a post. You have all functionalities available from wp_insert_post', $translation_ident ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to delete a specified user
	 */
	public function action_delete_post_content(){

		$translation_ident = 'action-delete-post-content';

		$parameter = array(
			'post_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of your specified post. This field is required.', $translation_ident ) ),
			'force_delete'  => array(
				'short_description' => WPWHPRO()->helpers->translate( '(optional) Whether to bypass trash and force deletion (added in WordPress 2.9). Possible values: "yes" and "no". Default: "no". Please note that soft deletion just works for the "post" and "page" post type.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the post will be completely removed from your WordPress website.", $translation_ident ),
			),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post, $post_id, $check, $force_delete ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$post</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the WordPress post object of the already deleted post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the post id of the deleted post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$check</strong> (mixed)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the response of the wp_delete_post() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$force_delete</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the force_delete argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Post related data as an array. We return the post id with the key "post_id" and the force delete boolean with the key "force_delete". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg' => '',
    'data' => array(
        'post_id' => 0,
        'force_delete' => false
    )
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-delete_post.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'delete_post',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => sprintf( WPWHPRO()->helpers->translate( 'Delete a post via %s Pro.', $translation_ident ), WPWHPRO_NAME ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function action_get_posts_content(){

		$translation_ident = 'action-get_posts-content';

		$parameter = array(
			'arguments'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_Query notation.', $translation_ident ) ),
			'return_only'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: posts', $translation_ident ) ),
			'load_meta'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" to add the post meta to each given post. Default: "no"', $translation_ident ) ),
			'load_acf'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" to add the Advanced Custom Fields related post meta to each given post. Default: "no"', $translation_ident ) ),
			'load_taxonomies'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Single value or comma separated list of the taxonomies you want to addto the response.', $translation_ident ) ),
			'do_action'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		if( ! WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			unset( $parameter['load_acf'] );
		}

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON formatted string, which includes certain arguments from the WordPress post query called <strong>WP_Query</strong>. For further details, please check out the following link:", $translation_ident ); ?>
<br>
<a href="https://developer.wordpress.org/reference/classes/wp_query/" title="wordpress.org" target="_blank">https://developer.wordpress.org/reference/classes/wp_query/</a>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example on how the JSON is set up:", $translation_ident ); ?>
<pre>{"post_type":"post","posts_per_page":8}</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above will filter the posts for the post type \"post\" and returns maximum eight posts.", $translation_ident ); ?>
		<?php
		$parameter['arguments']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the output of the query using the <strong>return_only</strong> parameter. This allows you to output only certain elements or the whole WP_Query class. Here is an example:", $translation_ident ); ?>
<pre>posts,post_count,found_posts,max_num_pages</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li>all</li>
    <li>posts</li>
    <li>post</li>
    <li>post_count</li>
    <li>found_posts</li>
    <li>max_num_pages</li>
    <li>current_post</li>
    <li>query_vars</li>
    <li>query</li>
    <li>tax_query</li>
    <li>meta_query</li>
    <li>date_query</li>
    <li>request</li>
    <li>in_the_loop</li>
    <li>current_post</li>
</ol>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also attach the assigned taxonomies of the returned posts. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", $translation_ident ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
		<?php
		$parameter['load_taxonomies']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_posts</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $post_query, $args, $return_only ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_query</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The full WP_Query object.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$args</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted JSON construct that was sent by the caller within the arguments argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_only</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string that was sent by the caller via the return_only argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the post query. This depends on the parameters you send.', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array()
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-get_posts.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'get_posts',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Search for posts on your WordPress website', $translation_ident ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to get a single post
	 */
	public function action_get_post_content(){

		$translation_ident = 'action-get_post-content';

		$parameter = array(
			'post_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the post you want to fetch.', $translation_ident ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Select the values you want to return. Default is all.', $translation_ident ) ),
			'thumbnail_size'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Pass the size of the thumbnail of your given post id. Default is full.', $translation_ident ) ),
			'post_taxonomies'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Single value or comma separated list of the taxonomies you want to return. Default: post_tag.', $translation_ident ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after our plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the result of the post data gathering using the <strong>return_only</strong> parameter. This allows you to output only certain elements of the request. Here is an example:", $translation_ident ); ?>
<pre>post,post_thumbnail,post_terms,post_meta,post_permalink</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li><strong>all</strong></li>
    <li><strong>post</strong></li>
    <li><strong>post_thumbnail</strong></li>
    <li><strong>post_terms</strong></li>
    <li><strong>post_meta</strong></li>
    <li><strong>post_permalink</strong></li>
	<?php if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
		echo '<li><strong>acf_data</strong> (' . WPWHPRO()->helpers->translate( "Integrates Advanced Custom Fields", $translation_ident ) . ')</li>';
	} ?>
</ol>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to return one or multiple thumbnail_sizes for the given post thumbnail. By default, we output only the full image. Here is an example: ", $translation_ident ); ?>
<pre>full,medium</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available sizes for the <strong>thumbnail_size</strong> argument (The availalbe sizes may vary since you can also use third-party size definitions). In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li><strong>thumbnail</strong> <?php echo WPWHPRO()->helpers->translate( "(150px square)", $translation_ident ); ?></li>
    <li><strong>medium</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 300px width and height)", $translation_ident ); ?></li>
    <li><strong>large</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 1024px width and height)", $translation_ident ); ?></li>
    <li><strong>full</strong> <?php echo WPWHPRO()->helpers->translate( "(full/original image size you uploaded)", $translation_ident ); ?></li>
</ol>
		<?php
		$parameter['thumbnail_size']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also customize the output of the returned taxonomies using the <strong>post_taxonomies</strong> argument. Default is post_tag. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", $translation_ident ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
		<?php
		$parameter['post_taxonomies']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $post_id, $thumbnail_size, $post_taxonomies ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The id of the currently fetched post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$thumbnail_size</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted thumbnail sizes sent by the caller within the thumbnail_size argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_taxonomies</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted taxonomy slugs sent by the caller within the post_taxonomies argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the single post. This depends on the parameters you send.', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array()
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-get_post.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'get_post',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Returns the object of a user', $translation_ident ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to use a custom action webhook
	 */
	public function action_custom_action_content(){

		$translation_ident = 'action-ironikus-custom_action-content';

		$parameter = array(
			'wpwh_identifier'       => array(
				'short_description' => WPWHPRO()->helpers->translate( 'This value is send over within the WordPress hooks to identify the incoming action. You can use it to fire your customizatios only on specific webhooks.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "Set this argument to identify your webhook call within the add_filter() function. It can be used to diversify between multiple calls that use this custom action. You can set it to e.g. <strong>validate-user</strong> and then check within the add_filter() callback against it to only fire it for this specific webhook call. You can also define this argument within the URL as a parameter, e.g. <code>&wpwh_identifier=my-custom-identifier</code>. In case you have defined the wpwh_identifier within the payload and the URL, we prioritize the parameter set within the payload.", $translation_ident ),
			),
		);

		$returns = array(
			'custom'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'This webhook returns whatever you define withiin the filters. Please check the description for more detials.', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
//This is how the default response looks like
$return_args = array(
    'success' => false,
    'msg' => ''
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-custom_action.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'custom_action',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Do whatever you like with the incoming data by defining this custom action.', $translation_ident ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to test a webhook
	 */
	public function action_ironikus_test_content(){

		$parameter = array(
			'test_var'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A test var. Include the following value to get a success message back: test-value123', 'action-ironikus-test-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-ironikus-test-content' ) ),
			'test_var'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The variable that was set for the request.', 'action-ironikus-test-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-ironikus-test-content' ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg' => '',
    'test_var' => 'test-value123'
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-ironikus_test.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'ironikus_test',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Test the functionality of this plugin by sending over a demo request.', 'action-ironikus-test-content' ),
			'description'       => $description
		);

	}

	public function action_custom_action(){

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => true,
			'msg' => WPWHPRO()->helpers->translate("Custom action was successfully fired.", 'action-custom_action-success' )
		);

		$identifier = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wpwh_identifier' );
		if( empty( $identifier ) && isset( $_GET['wpwh_identifier'] ) ){
			$identifier = $_GET['wpwh_identifier'];
		}

		$return_args = apply_filters( 'wpwhpro/run/actions/custom_action/return_args', $return_args, $identifier, $response_body );

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();

    }

	public function action_ironikus_test(){

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg' => '',
			'test_var' => ''
		);

		$test_var = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'test_var' ) );

		if( $test_var == 'test-value123' ){
			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate("Test value successfully filled.", 'action-test-success' );
        } else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("test_var was not filled properly. Please set it to 'test-value123'", 'action-test-success' );
        }

        $return_args['test_var'] = $test_var;

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();

    }

	/**
	 * Create a user via a action call
     *
     * @param $update - Wether the user gets created or updated
	 */
	public function action_create_user(){

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
            'msg' => '',
            'data' => array(
	            'user_id' => 0,
	            'user_data' => array()
            )
		);

		$nickname           = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'nickname' ) );
		$user_login         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_login' ) );
		$user_nicename      = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_nicename' ) );
		$description        = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' ) );
		$user_registered    = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_registered' ) );
		$user_url           = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_url' ) );
		$display_name       = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'display_name' ) );
		$user_email         = sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' ) );
		$first_name         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' ) );
		$last_name          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' ) );
		$role               = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'role' ) );
		$user_pass          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_pass' );
		$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

		$rich_editing     	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'rich_editing' ) == 'yes' ) ? true : false;
		$send_email         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
		$additional_roles   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'additional_roles' );

		if ( empty( $user_email ) ) {
			$return_args['msg'] = WPWHPRO()->helpers->translate("An email is required to create a user.", 'action-create-user-success' );

			WPWHPRO()->webhook->echo_action_data( $return_args );
			die();
		}

		$user_data = array();

		if( $user_email ){
			$user_data['user_email'] = $user_email;
		}

		$dynamic_user_login = apply_filters( 'wpwhpro/run/create_action_user_login', false );
		if ( empty( $user_login ) && $dynamic_user_login ) {
			$user_login = WPWHPRO()->helpers->create_random_unique_username( $user_email, 'user_' );
		}

		//Define on new user
		if( ! empty( $role ) ){
			$user_data['role'] = 'subscriber';
		}

		//Auto generate on new user
		if( empty( $user_pass ) ){
			$user_data['user_pass'] = wp_generate_password( 32, true, false );
		}

		if( ! empty( $nickname ) ){
			$user_data['nickname'] = $nickname;
		}

		if( ! empty( $user_login ) ){
			$user_data['user_login'] = $user_login;
		} else {
			$user_data['user_login'] = sanitize_title( $user_email );
		}

		if( ! empty( $user_nicename ) ){
			$user_data['user_nicename'] = $user_nicename;
		}

		if( ! empty( $description ) ){
			$user_data['description'] = $description;
		}

		if( ! empty( $rich_editing ) ){
			$user_data['rich_editing'] = $rich_editing;
		}

		if( ! empty( $user_registered ) ){
			$user_data['user_registered'] = $user_registered;
		}

		if( ! empty( $user_url ) ){
			$user_data['user_url'] = $user_url;
		}

		if( ! empty( $display_name ) ){
			$user_data['display_name'] = $display_name;
		}

		if( ! empty( $first_name ) ){
			$user_data['first_name'] = $first_name;
		}

		if( ! empty( $last_name ) ){
			$user_data['last_name'] = $last_name;
		}

		if( ! empty( $role ) ){
			$user_data['role'] = $role;
		}

		if( ! empty( $user_pass ) ){
			$user_data['user_pass'] = $user_pass;
		}

		$user_id = wp_insert_user( $user_data );

		if ( ! is_wp_error( $user_id ) && is_numeric( $user_id ) ) {

			//Manage user roles
			if( ! empty( $additional_roles ) ){

				$wpwh_current_user = new WP_User( $user_id );

				if( WPWHPRO()->helpers->is_json( $additional_roles ) ){

					$additional_roles_meta_data = json_decode( $additional_roles, true );
					foreach( $additional_roles_meta_data as $sarole => $sastatus ){

						switch( $sastatus ){
							case 'add':
								$wpwh_current_user->add_role( sanitize_text_field( $sarole ) );
							break;
							case 'remove':
								$wpwh_current_user->remove_role( sanitize_text_field( $sarole ) );
							break;
						}

					}

				} else {

					$additional_roles_data = explode( ';', trim( $additional_roles, ';' ) );
					foreach( $additional_roles_data as $single_additional_role ){

						$additional_roles_data = explode( ':', trim( $single_additional_role, ':' ) );
						if(
							! empty( $additional_roles_data )
							&& is_array( $additional_roles_data )
							&& ! empty( $additional_roles_data[0] )
							&& ! empty( $additional_roles_data[1] )
						){

							switch( $additional_roles_data[1] ){
								case 'add':
									$wpwh_current_user->add_role( sanitize_text_field( $additional_roles_data[0] ) );
								break;
								case 'remove':
									$wpwh_current_user->remove_role( sanitize_text_field( $additional_roles_data[0] ) );
								break;
							}

						}
					}

				}

			}

			//Map additional roles to user data
			$user_data['additional_roles'] = $additional_roles;

			$return_args['msg'] = WPWHPRO()->helpers->translate("User successfully created.", 'action-create-user-success' );

			$return_args['success'] = true;
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['user_data'] = $user_data;

			if( apply_filters( 'wpwhpro/run/create_action_user_email_notification', true ) && $send_email == 'yes' ){
				wp_new_user_notification( $user_id, null, 'both' );
			}
		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("An error occured while creating the user. Please check the response for more details.", 'action-create-user-success' );
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['user_data'] = $user_data;
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $user_data, $user_id );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Delete function for defined action
	 */
	public function action_delete_user() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg'     => '',
            'data' => array(
                'user_deleted' => false,
                'user_id' => 0
            )
		);

		$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
		$user_email  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );
		$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
		$send_email  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
		$remove_from_network  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_from_network' ) == 'yes' ) ? 'yes' : 'no';
		$user = '';

		if( ! empty( $user_id ) ){
			$user = get_user_by( 'id', $user_id );
		} elseif( ! empty( $user_email ) ){
			$user = get_user_by( 'email', $user_email );
		}

		if( ! empty( $user ) ){
			if( ! empty( $user->ID ) ){

				$user_id = $user->ID;

				$delete_administrators = apply_filters( 'wpwhpro/run/delete_action_user_admins', false );
				if ( in_array( 'administrator', $user->roles ) && ! $delete_administrators ) {
					exit;
				}

				require_once( ABSPATH . 'wp-admin/includes/user.php' );

				if( is_multisite() && $remove_from_network == 'yes' ){

					if( ! function_exists( 'wpmu_delete_user' ) ){
						require_once( ABSPATH . 'wp-admin/includes/ms.php' );
					}

					$checkdelete = wpmu_delete_user( $user_id );
				} else {
					$checkdelete = wp_delete_user( $user_id );
				}

				if ( $checkdelete ) {

					$send_admin_notification = apply_filters( 'wpwhpro/run/delete_action_user_notification', true );
					if( $send_admin_notification && $send_email == 'yes' ){
						$blog_name = get_bloginfo( "name" );
						$blog_email = get_bloginfo( "admin_email" );
						$headers = 'From: ' . $blog_name . ' <' . $blog_email . '>' . "\r\n";
						$subject = WPWHPRO()->helpers->translate( 'Your account has been deleted.', 'action-delete-user' );
						$content = sprintf( WPWHPRO()->helpers->translate( "Hello %s,\r\n", 'action-delete-user' ), $user->user_nicename );
						$content .= sprintf( WPWHPRO()->helpers->translate( 'Your account at %s (%d) has been deleted.' . "\r\n", 'action-delete-user' ), $blog_name, home_url() );
						$content .= sprintf( WPWHPRO()->helpers->translate( 'Please contact %s for further questions.', 'action-delete-user' ), $blog_email );

						wp_mail( $user_email, $subject, $content, $headers );
					}

					do_action( 'wpwhpro/run/delete_action_user_deleted' );

					$return_args['msg'] = WPWHPRO()->helpers->translate("User successfully deleted.", 'action-delete-user-error' );
					$return_args['success'] = true;
					$return_args['data']['user_deleted'] = true;
					$return_args['data']['user_id'] = $user_id;
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Error deleting user.", 'action-delete-user-error' );
				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("Could not delete user because the user not given.", 'action-delete-user-error' );
			}
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $user, $user_id, $user_email, $send_email );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Get certain users using WP_User_Query
	 */
	public function action_get_user() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg'     => '',
            'data' => array()
		);

		$user_value     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_value' );
		$value_type     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value_type' );
		$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
		$user = null;

		if( empty( $user_value ) ){
			$return_args['msg'] = WPWHPRO()->helpers->translate( "It is necessary to define the user_value argument. Please define it first.", 'action-get_user-failure' );

			WPWHPRO()->webhook->echo_action_data( $return_args );
			die();
		}

		if( empty( $value_type ) ){
			$value_type = 'id';
		}

		if( ! empty( $user_value ) && ! empty( $value_type ) ){
			$user = get_user_by( $value_type, $user_value );

			if ( is_wp_error( $user ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( $user->get_error_message(), 'action-get_user-failure' );
			} else {

				if( ! empty( $user ) && ! is_wp_error( $user ) ){

					$user_meta = array();
					if( isset( $user->ID ) ){
						$user_meta = get_user_meta( $user->ID );
					}

					$return_args['msg'] = WPWHPRO()->helpers->translate("User was successfully returned.", 'action-get_users-success' );
					$return_args['success'] = true;
					$return_args['data'] = $user;
					$return_args['user_meta'] = $user_meta;
					$return_args['user_posts_url'] = get_author_posts_url( $user->ID );

					if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
						$return_args['acf_meta'] = get_fields( 'user_' . $user->ID );
					}

				} else {
					$return_args['data'] = $user;
					$return_args['msg'] = WPWHPRO()->helpers->translate("No user found.", 'action-get_users-success' );
				}

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("There is an issue with your defined arguments. Please check them first.", 'action-get_user-failure' );
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $user_value, $value_type, $user );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Delete function for defined action
	 */
	public function action_get_users() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg'     => '',
            'data' => array()
		);

		$args     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
		$return_only     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
		$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
		$user_query = null;

		if( empty( $args ) ){
			$return_args['msg'] = WPWHPRO()->helpers->translate("arguments is a required parameter. Please define it.", 'action-get_users-failure' );

			WPWHPRO()->webhook->echo_action_data( $return_args );
			die();
		}

		$serialized_args = null;
		if( WPWHPRO()->helpers->is_json( $args ) ){
			$serialized_args = json_decode( $args, true );
		}

		$return = array( 'get_results' );
		if( ! empty( $return_only ) ){
			$return = array_map( 'trim', explode( ',', $return_only ) );
		}

		if( ! empty( $serialized_args ) && is_array( $serialized_args ) ){
			$user_query = new WP_User_Query( $serialized_args );

			if ( is_wp_error( $user_query ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( $user_query->get_error_message(), 'action-get_users-failure' );
			} else {

				foreach( $return as $single_return ){

					switch( $single_return ){
						case 'all':
							$return_args['data'][ $single_return ] = $user_query;
							break;
						case 'get_results':
							$return_args['data'][ $single_return ] = $user_query->get_results();
							break;
						case 'get_total':
							$return_args['data'][ $single_return ] = $user_query->get_total();
							break;
					}

				}

				//Manually attach additional data to the query
				foreach( $return as $single_return ){

					if( $single_return === 'meta_data' ){
						if( isset( $return_args['data']['get_results'] ) && ! empty( $return_args['data']['get_results'] ) ){
							foreach( $return_args['data']['get_results'] as $user_key => $user_data ){
								if( isset( $user_data->data ) && isset( $user_data->data->ID ) ){
									$return_args['data']['get_results'][ $user_key ]->data->meta_data = get_user_meta( $user_data->data->ID );
								}
							}
						}
					}

					if( $single_return === 'acf_data' ){
						if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
							if( isset( $return_args['data']['get_results'] ) && ! empty( $return_args['data']['get_results'] ) ){
								foreach( $return_args['data']['get_results'] as $user_key => $user_data ){
									if( isset( $user_data->data ) && isset( $user_data->data->ID ) ){
										$return_args['data']['get_results'][ $user_key ]->data->acf_data = get_fields( 'user_' . $user_data->data->ID );
									}
								}
							}
						}
					}

				}

				$return_args['msg'] = WPWHPRO()->helpers->translate("Query was successfully executed.", 'action-get_users-success' );
				$return_args['success'] = true;

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_users-failure' );
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $user_query, $args, $return_only );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Create a post via an action call
     *
     * @param $update - Wether to create or to update the post
	 */
	public function action_create_post(){

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success'   => false,
			'msg'       => '',
            'data'      => array(
	            'post_id' => null,
	            'post_data' => null
            )
		);

		$post_author            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_author' );
		$post_date              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date' ) );
		$post_date_gmt          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date_gmt' ) );
		$post_content           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content' );
		$post_content_filtered  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content_filtered' );
		$post_title             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_title' );
		$post_excerpt           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_excerpt' );
		$post_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_status' ) );
		$post_type              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_type' ) );
		$comment_status         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
		$ping_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
		$post_password          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_password' ) );
		$post_name              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_name' ) );
		$to_ping                = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
		$pinged                 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
		$post_modified          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified' ) );
		$post_modified_gmt      = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified_gmt' ) );
		$post_parent            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_parent' ) );
		$menu_order             = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
		$post_mime_type         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_mime_type' ) );
		$guid                   = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
		$import_id              = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'import_id' ) );
		$post_category          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_category' );
		$tags_input             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
		$tax_input              = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
		$wp_error               = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )     ? true : false;
		$do_action              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

		$post_data = array();

		if( ! empty( $post_author ) ){

			$post_author_id = 0;
			if( is_numeric( $post_author ) ){
				$post_author_id = intval( $post_author );
			} elseif ( is_email( $post_author ) ) {
				$get_user = get_user_by( 'email', $post_author );
				if( ! empty( $get_user ) && ! empty( $get_user->data ) && ! empty( $get_user->data->ID ) ){
					$post_author_id = $get_user->data->ID;
				}
			}

			$post_data['post_author'] = $post_author_id;
		}

		if( ! empty( $post_date ) ){
			$post_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $post_date ) );
		}

		if( ! empty( $post_date_gmt ) ){
			$post_data['post_date_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_date_gmt ) );
		}

		if( ! empty( $post_content ) ){
			$post_data['post_content'] = $post_content;
		}

		if( ! empty( $post_content_filtered ) ){
			$post_data['post_content_filtered'] = $post_content_filtered;
		}

		if( ! empty( $post_title ) ){
			$post_data['post_title'] = $post_title;
		}

		if( ! empty( $post_excerpt ) ){
			$post_data['post_excerpt'] = $post_excerpt;
		}

		if( ! empty( $post_status ) ){
			$post_data['post_status'] = $post_status;
		}

		if( ! empty( $post_type ) ){
			$post_data['post_type'] = $post_type;
		}

		if( ! empty( $comment_status ) ){
			$post_data['comment_status'] = $comment_status;
		}

		if( ! empty( $ping_status ) ){
			$post_data['ping_status'] = $ping_status;
		}

		if( ! empty( $post_password ) ){
			$post_data['post_password'] = $post_password;
		}

		if( ! empty( $post_name ) ){
			$post_data['post_name'] = $post_name;
		}

		if( ! empty( $to_ping ) ){
			$post_data['to_ping'] = $to_ping;
		}

		if( ! empty( $pinged ) ){
			$post_data['pinged'] = $pinged;
		}

		if( ! empty( $post_modified ) ){
			$post_data['post_modified'] = date( "Y-m-d H:i:s", strtotime( $post_modified ) );
		}

		if( ! empty( $post_modified_gmt ) ){
			$post_data['post_modified_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_modified_gmt ) );
		}

		if( ! empty( $post_parent ) ){
			$post_data['post_parent'] = $post_parent;
		}

		if( ! empty( $menu_order ) ){
			$post_data['menu_order'] = $menu_order;
		}

		if( ! empty( $post_mime_type ) ){
			$post_data['post_mime_type'] = $post_mime_type;
		}

		if( ! empty( $guid ) ){
			$post_data['guid'] = $guid;
		}

		if( ! empty( $import_id ) ){
			$post_data['import_id'] = $import_id;
		}

		//Setup post categories
		if( ! empty( $post_category ) ){
			$post_category_data = explode( ',', trim( $post_category, ',' ) );

			if( ! empty( $post_category_data ) ){
				$post_data['post_category'] = $post_category_data;
			}
		}

		//Setup meta tags
		if( ! empty( $tags_input ) ){
			$post_tags_data = explode( ',', trim( $tags_input, ',' ) );

			if( ! empty( $post_tags_data ) ){
				$post_data['tags_input'] = $post_tags_data;
			}
		}

		$post_id = wp_insert_post( $post_data, $wp_error );

		if ( ! is_wp_error( $post_id ) && is_numeric( $post_id ) ) {

			//Setup meta tax
			if( ! empty( $tax_input ) ){
			    $remove_all = false;
			    $tax_append = false; //Default by WP wp_set_object_terms
				$tax_data = array(
					'delete' => array(),
					'create' => array(),
				);

				if( WPWHPRO()->helpers->is_json( $tax_input ) ){
					$post_tax_data = json_decode( $tax_input, true );
					foreach( $post_tax_data as $taxkey => $single_meta ){

						//Validate special values
						if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-append' ){
							$tax_append = true;
							continue;
						}

						if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-remove-all' ){
							$remove_all = true;
							continue;
						}

						$meta_key           = sanitize_text_field( $taxkey );
						$meta_values        = $single_meta;

						if( ! empty( $meta_key ) ){

							if( ! is_array( $meta_values ) ){
								$meta_values = array( $meta_values );
							}

							//separate for deletion and for creation
							foreach( $meta_values as $svalue ){
								if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

									if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
										$tax_data['delete'][ $meta_key ] = array();
									}

									//Replace deletion value to correct original value
									$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
								} else {

									if( ! isset( $tax_data['create'][ $meta_key ] ) ){
										$tax_data['create'][ $meta_key ] = array();
									}

									$tax_data['create'][ $meta_key ][] = $svalue;
								}
							}

						}
					}
				} else {
					$post_tax_data = explode( ';', trim( $tax_input, ';' ) );
					foreach( $post_tax_data as $single_meta ){

						//Validate special values
						if( $single_meta == 'ironikus-append' ){
							$tax_append = true;
							continue;
						}

						if( $single_meta == 'ironikus-remove-all' ){
							$remove_all = true;
							continue;
						}

						$single_meta_data   = explode( ',', $single_meta );
						$meta_key           = sanitize_text_field( $single_meta_data[0] );
						$meta_values        = explode( ':', $single_meta_data[1] );

						if( ! empty( $meta_key ) ){

							if( ! is_array( $meta_values ) ){
								$meta_values = array( $meta_values );
							}

							//separate for deletion and for creation
							foreach( $meta_values as $svalue ){
								if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

									if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
										$tax_data['delete'][ $meta_key ] = array();
									}

									//Replace deletion value to correct original value
									$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
								} else {

									if( ! isset( $tax_data['create'][ $meta_key ] ) ){
										$tax_data['create'][ $meta_key ] = array();
									}

									$tax_data['create'][ $meta_key ][] = $svalue;
								}
							}

						}
					}
				}

				foreach( $tax_data['create'] as $tax_key => $tax_values ){

				    if( $remove_all ){
					    wp_set_object_terms( $post_id, array(), $tax_key, $tax_append );
                    } else {
					    wp_set_object_terms( $post_id, $tax_values, $tax_key, $tax_append );
                    }

				}

				#$post_data['tax_input'] = $tax_data;
			}

			//Map external post data
			$post_data['tax_input'] = $tax_input;

			$return_args['msg'] = WPWHPRO()->helpers->translate("Post successfully created", 'action-create-post-success' );

			$return_args['success'] = true;
			$return_args['data']['post_data'] = $post_data;
			$return_args['data']['post_id'] = $post_id;

		} else {

		    if( is_wp_error( $post_id ) && $wp_error ){

			    $return_args['data']['post_data'] = $post_data;
			    $return_args['data']['post_id'] = $post_id;
			    $return_args['msg'] = WPWHPRO()->helpers->translate("WP Error", 'action-create-post-success' );
            } else {
			    $return_args['msg'] = WPWHPRO()->helpers->translate("Error creating post.", 'action-create-post-success' );
            }
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $post_data, $post_id, $return_args );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * The action for deleting a post
	 */
	public function action_delete_post() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
            'msg' => '',
            'data' => array(
                'post_id' => 0,
                'force_delete' => false
            )
		);

		$post_id         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) : 0;
		$force_delete    = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
		$do_action       = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) : '';
		$post = '';
		$check = '';

		if( ! empty( $post_id ) ){
			$post = get_post( $post_id );
		}

		if( ! empty( $post ) ){
			if( ! empty( $post->ID ) ){

			    $check = wp_delete_post( $post->ID, $force_delete );

				if ( $check ) {

					do_action( 'wpwhpro/run/delete_action_post_deleted' );

					$return_args['msg']     = WPWHPRO()->helpers->translate("Post successfully deleted.", 'action-delete-post-success' );
					$return_args['success'] = true;
				} else {
					$return_args['msg']  = WPWHPRO()->helpers->translate("Error deleting post. Please check wp_delete_post( " . $post->ID . ", " . $force_delete . " ) for more information.", 'action-delete-post-success' );
					$return_args['data']['post_id'] = $post->ID;
					$return_args['data']['force_delete'] = $force_delete;
				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("Could not delete the post: No ID given.", 'action-delete-post-success' );
			}
		} else {
			$return_args['msg']  = WPWHPRO()->helpers->translate("No post found to your specified post id.", 'action-delete-post-success' );
			$return_args['data']['post_id'] = $post_id;
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $post, $post_id, $check, $force_delete );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Grab certain posts using WP_Query
	 */
	public function action_get_posts() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg'     => '',
            'data' => array()
		);

		$args = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
		$return_only = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
		$load_meta = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_meta' ) === 'yes' ) ? true : false;
		$load_acf = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_acf' ) === 'yes' ) ? true : false;
		$load_taxonomies = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_taxonomies' );
		$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
		$post_query = null;

		if( empty( $args ) ){
			$return_args['msg'] = WPWHPRO()->helpers->translate("arguments is a required parameter. Please define it.", 'action-get_posts-failure' );

			WPWHPRO()->webhook->echo_action_data( $return_args );
			die();
		}

		$serialized_args = null;
		if( WPWHPRO()->helpers->is_json( $args ) ){
			$serialized_args = json_decode( $args, true );
		}

		$return = array( 'posts' );
		if( ! empty( $return_only ) ){
			$return = array_map( 'trim', explode( ',', $return_only ) );
		}

		if( is_array( $serialized_args ) ){
			$post_query = new WP_Query( $serialized_args );

			if ( is_wp_error( $post_query ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( $post_query->get_error_message(), 'action-get_posts-failure' );
			} else {

				foreach( $return as $single_return ){

					switch( $single_return ){
						case 'all':
							$return_args['data'][ $single_return ] = $post_query;
							break;
						case 'posts':
							$return_args['data'][ $single_return ] = $post_query->posts;
							break;
						case 'post':
							$return_args['data'][ $single_return ] = $post_query->post;
							break;
						case 'post_count':
							$return_args['data'][ $single_return ] = $post_query->post_count;
							break;
						case 'found_posts':
							$return_args['data'][ $single_return ] = $post_query->found_posts;
							break;
						case 'max_num_pages':
							$return_args['data'][ $single_return ] = $post_query->max_num_pages;
							break;
						case 'current_post':
							$return_args['data'][ $single_return ] = $post_query->current_post;
							break;
						case 'query_vars':
							$return_args['data'][ $single_return ] = $post_query->query_vars;
							break;
						case 'query':
							$return_args['data'][ $single_return ] = $post_query->query;
							break;
						case 'tax_query':
							$return_args['data'][ $single_return ] = $post_query->tax_query;
							break;
						case 'meta_query':
							$return_args['data'][ $single_return ] = $post_query->meta_query;
							break;
						case 'date_query':
							$return_args['data'][ $single_return ] = $post_query->date_query;
							break;
						case 'request':
							$return_args['data'][ $single_return ] = $post_query->request;
							break;
						case 'in_the_loop':
							$return_args['data'][ $single_return ] = $post_query->in_the_loop;
							break;
						case 'current_post':
							$return_args['data'][ $single_return ] = $post_query->current_post;
							break;
					}

				}

				if( $load_meta ){

					//Add the meta data to the posts array
					if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
						foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
							$return_args['data']['posts'][ $single_post_key ]->meta_data = get_post_meta( $single_post->ID );
						}
					}

					//Add the post meta to the single post
					if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
						$return_args['data']['post']->meta_data = get_post_meta( $return_args['data']['post']->ID );
					}
				}

				if( $load_acf && WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){

					//Add the meta data to the posts array
					if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
						foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
							$return_args['data']['posts'][ $single_post_key ]->acf_data = get_fields( $single_post->ID );
						}
					}

					//Add the post meta to the single post
					if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
						$return_args['data']['post']->acf_data = get_fields( $return_args['data']['post']->ID );
					}
				}

				if( ! empty( $load_taxonomies ) ){

					$post_taxonomies_out = array_map( 'trim', explode( ',', $load_taxonomies ) );

					//Add the taxonomies to the posts array
					if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
						foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
							$return_args['data']['posts'][ $single_post_key ]->taxonomies = wp_get_post_terms( $single_post->ID, $post_taxonomies_out );
						}
					}

					//Add the taxonomies to the single post
					if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
						$return_args['data']['post']->taxonomies = wp_get_post_terms( $return_args['data']['post']->ID, $post_taxonomies_out );
					}
				}

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Query was successfully executed.", 'action-get_posts-success' );
				$return_args['success'] = true;

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_posts-failure' );
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $post_query, $args, $return_only );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * Get a single post using get_post
	 */
	public function action_get_post() {

		$response_body = WPWHPRO()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg'     => '',
            'data' => array()
		);

		$post_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
		$return_only     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
		$thumbnail_size     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'thumbnail_size' );
		$post_taxonomies     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_taxonomies' );
		$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

		if( empty( $post_id ) ){
			$return_args['msg'] = WPWHPRO()->helpers->translate( "It is necessary to define the post_id argument. Please define it first.", 'action-get_post-failure' );

			WPWHPRO()->webhook->echo_action_data( $return_args );
			die();
		}

		$return = array( 'all' );
		if( ! empty( $return_only ) ){
			$return = array_map( 'trim', explode( ',', $return_only ) );
		}

		$thumbnail_sizes = 'full';
		if( ! empty( $thumbnail_size ) ){
			$thumbnail_sizes = array_map( 'trim', explode( ',', $thumbnail_size ) );
		}

		$post_taxonomies_out = 'post_tag';
		if( ! empty( $post_taxonomies ) ){
			$post_taxonomies_out = array_map( 'trim', explode( ',', $post_taxonomies ) );
		}

		if( ! empty( $post_id ) ){
			$post = get_post( $post_id );
			$post_thumbnail = get_the_post_thumbnail_url( $post_id, $thumbnail_sizes );
			$post_terms = wp_get_post_terms( $post_id, $post_taxonomies_out );
			$post_meta = get_post_meta( $post_id );
			$permalink = get_permalink( $post_id );

			$acf_data = '';
			if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
				$acf_data = get_fields( $post_id );
			}

			if ( is_wp_error( $post ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( $post->get_error_message(), 'action-get_post-failure' );
			} else {

				foreach( $return as $single_return ){

					switch( $single_return ){
						case 'all':
							$return_args['data'][ 'post' ] = $post;
							$return_args['data'][ 'post_thumbnail' ] = $post_thumbnail;
							$return_args['data'][ 'post_terms' ] = $post_terms;
							$return_args['data'][ 'post_meta' ] = $post_meta;
							$return_args['data'][ 'post_permalink' ] = $permalink;

							if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
								$return_args['data'][ 'acf_data' ] = $acf_data;
							}

							break;
						case 'post':
							$return_args['data'][ $single_return ] = $post;
							break;
						case 'post_thumbnail':
							$return_args['data'][ $single_return ] = $post_thumbnail;
							break;
						case 'post_terms':
							$return_args['data'][ $single_return ] = $post_terms;
							break;
						case 'post_meta':
							$return_args['data'][ $single_return ] = $post_meta;
							break;
						case 'post_permalink':
							$return_args['data'][ $single_return ] = $permalink;
							break;
						case 'acf_data':
							if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
								$return_args['data'][ $single_return ] = $acf_data;
							}
							break;
					}
				}

				$return_args['msg'] = WPWHPRO()->helpers->translate("Post was successfully returned.", 'action-get_post-success' );
				$return_args['success'] = true;

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("There is an issue with your defined arguments. Please check them first.", 'action-get_post-failure' );
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $post_id, $thumbnail_size, $post_taxonomies );
		}

		WPWHPRO()->webhook->echo_action_data( $return_args );
		die();
	}

	/**
	 * ######################
	 * ###
	 * #### TRIGGERS
	 * ###
	 * ######################
	 */

	/**
     * Regsiter all available webhook triggers
     *
	 * @param $triggers - All registered triggers by the current plugin
	 *
	 * @return array - A array of all available triggers
	 */
	public function add_webhook_triggers_content( $triggers ){

		$triggers[] = $this->trigger_create_user_content();
		$triggers[] = $this->trigger_login_user_content();
		$triggers[] = $this->trigger_login_user_update();
		$triggers[] = $this->trigger_user_deleted();
		$triggers[] = $this->trigger_post_create();
		$triggers[] = $this->trigger_post_update();
		$triggers[] = $this->trigger_post_delete();
		$triggers[] = $this->trigger_post_trash();
		$triggers[] = $this->trigger_custom_action_content();

		return $triggers;
	}

	/*
	 * Add the specified webhook triggers logic.
	 * We also add the demo functionality here
	 */
	public function add_webhook_triggers(){

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'create_user' ) ) ){
			add_action( 'user_register', array( $this, 'ironikus_trigger_user_register_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_user_create', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'login_user' ) ) ){
			add_action( 'wp_login', array( $this, 'ironikus_trigger_user_login_init' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_login', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'update_user' ) ) ){
			add_action( 'profile_update', array( $this, 'ironikus_trigger_user_update_init' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_update', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'deleted_user' ) ) ){
			add_action( 'wpmu_delete_user', array( $this, 'ironikus_prepare_user_delete' ), 10, 1 );
			add_action( 'delete_user', array( $this, 'ironikus_prepare_user_delete' ), 10, 1 );
			add_action( 'deleted_user', array( $this, 'ironikus_trigger_deleted_user_init' ), 10, 3 );
			add_filter( 'ironikus_demo_test_deleted_user', array( $this, 'ironikus_send_demo_user_deleted' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_create' ) ) ){
			add_action( 'add_attachment', array( $this, 'ironikus_trigger_post_create_attachment_init' ), 10, 1 );
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_create_init' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_update' ) ) ){
			add_action( 'post_updated', array( $this, 'ironikus_prepare_post_update' ), 10, 3 );
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_update_init' ), 10, 3 );
			add_action( 'attachment_updated', array( $this, 'ironikus_trigger_post_update_attachment_init' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_create' ) ) || ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_update' ) ) ){
			add_filter( 'ironikus_demo_test_post_create', array( $this, 'ironikus_send_demo_post_create' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_delete' ) ) ){
			add_action( 'before_delete_post', array( $this, 'ironikus_prepare_post_delete' ), 10, 1 );
			add_action( 'delete_attachment', array( $this, 'ironikus_prepare_post_delete' ), 10, 1 );
			add_action( 'delete_post', array( $this, 'ironikus_trigger_post_delete_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_post_delete', array( $this, 'ironikus_send_demo_post_delete' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'post_trash' ) ) ){
			add_action( 'trashed_post', array( $this, 'ironikus_trigger_post_trash_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_post_delete', array( $this, 'ironikus_send_demo_post_delete' ), 10, 3 );
        }

		if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'custom_action' ) ) ){
			add_action( 'wp_webhooks_send_to_webhook', array( $this, 'wp_webhooks_send_to_webhook_action' ), 10, 2 );
			add_filter( 'wp_webhooks_send_to_webhook_filter', array( $this, 'wp_webhooks_send_to_webhook_action_filter' ), 10, 4 );
			add_filter( 'ironikus_demo_test_custom_action', array( $this, 'ironikus_send_demo_custom_action' ), 10 );
        }

	}

	/**
	 * CREATE USER
	 */

	/*
	 * Register the trigger as an element
	 */
	public function trigger_create_user_content(){

		$parameter = array(
			'user_object' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-create-user-content' ) ),
			'user_meta'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-create_user.php' );
		$description = ob_get_clean();

		return array(
			'trigger'           => 'create_user',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Register', 'trigger-create-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', '' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user registers.', 'trigger-create-user-content' ),
			'description'       => $description,
            'callback'          => 'test_user_create'
		);

	}

	/*
	 * Register the demo data response
	 *
	 * @param $data - The default data
	 * @param $webhook - The current webhook
	 * @param $webhook_group - The current trigger this webhook belongs to
	 *
	 * @return array - The demo data
	 */
	public function ironikus_send_demo_user_create( $data, $webhook, $webhook_group ){

	    $data = array (
		    'data' =>
			    array (
				    'ID' => '1',
				    'user_login' => 'admin',
				    'user_pass' => '$P$BVbptZxEcZV2yeLyYeN.O4ZeG8225d.',
				    'user_nicename' => 'admin',
				    'user_email' => 'admin@ironikus.dev',
				    'user_url' => '',
				    'user_registered' => '2018-11-06 14:19:18',
				    'user_activation_key' => '',
				    'user_status' => '0',
				    'display_name' => 'admin',
			    ),
		    'ID' => 1,
		    'caps' =>
			    array (
				    'administrator' => true,
			    ),
		    'cap_key' => 'irn_capabilities',
		    'roles' =>
			    array (
				    0 => 'administrator',
			    ),
		    'allcaps' =>
			    array (
				    'switch_themes' => true,
				    'edit_themes' => true,
				    'activate_plugins' => true,
				    'edit_plugins' => true,
				    'edit_users' => true,
				    'edit_files' => true,
				    'manage_options' => true,
				    'moderate_comments' => true,
				    'manage_categories' => true,
				    'manage_links' => true,
				    'upload_files' => true,
				    'import' => true,
				    'unfiltered_html' => true,
				    'edit_posts' => true,
				    'edit_others_posts' => true,
				    'edit_published_posts' => true,
				    'publish_posts' => true,
				    'edit_pages' => true,
				    'read' => true,
				    'level_10' => true,
				    'level_9' => true,
				    'level_8' => true,
				    'level_7' => true,
				    'level_6' => true,
				    'level_5' => true,
				    'level_4' => true,
				    'level_3' => true,
				    'level_2' => true,
				    'level_1' => true,
				    'level_0' => true,
				    'edit_others_pages' => true,
				    'edit_published_pages' => true,
				    'publish_pages' => true,
				    'delete_pages' => true,
				    'delete_others_pages' => true,
				    'delete_published_pages' => true,
				    'delete_posts' => true,
				    'delete_others_posts' => true,
				    'delete_published_posts' => true,
				    'delete_private_posts' => true,
				    'edit_private_posts' => true,
				    'read_private_posts' => true,
				    'delete_private_pages' => true,
				    'edit_private_pages' => true,
				    'read_private_pages' => true,
				    'delete_users' => true,
				    'create_users' => true,
				    'unfiltered_upload' => true,
				    'edit_dashboard' => true,
				    'update_plugins' => true,
				    'delete_plugins' => true,
				    'install_plugins' => true,
				    'update_themes' => true,
				    'install_themes' => true,
				    'update_core' => true,
				    'list_users' => true,
				    'remove_users' => true,
				    'promote_users' => true,
				    'edit_theme_options' => true,
				    'delete_themes' => true,
				    'export' => true,
				    'administrator' => true,
			    ),
		    'filter' => NULL,
            'user_meta' => array (
	            'nickname' =>
		            array (
			            0 => 'admin',
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
			            0 => 'My descriptio ',
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
	            'irn_capabilities' =>
		            array (
			            0 => 'a:1:{s:13:"administrator";b:1;}',
		            ),
	            'irn_user_level' =>
		            array (
			            0 => '10',
		            ),
	            'dismissed_wp_pointers' =>
		            array (
			            0 => 'wp111_privacy',
		            ),
	            'show_welcome_panel' =>
		            array (
			            0 => '1',
		            ),
	            'session_tokens' =>
		            array (
			            0 => 'a:1:{}',
		            ),
	            'irn_dashboard_quick_press_last_post_id' =>
		            array (
			            0 => '4',
		            ),
	            'community-events-location' =>
		            array (
			            0 => 'a:1:{s:2:"ip";s:9:"127.0.0.0";}',
		            ),
	            'show_try_gutenberg_panel' =>
		            array (
			            0 => '0',
		            ),
            )
	    );

	    if( $webhook_group == 'login_user' ){
	        $data['user_login'] = 'myLogin@test.test';
        }

	    if( $webhook_group == 'update_user' ){
	        $data['user_old_data'] = array();
        }

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $data['acf_data'] = array(
                'demo_repeater_field' => array(
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                ),
                'demo_text_field' => 'Some demo text',
                'demo_true_false' => true,
            );
        }

        return $data;
    }

	/*
	 * Register the user register trigger as an element
	 *
	 * @param - §user_id - The id of the current user
	 */
	public function ironikus_trigger_user_register_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_user_register' ), func_get_args() );
	}
	public function ironikus_trigger_user_register( $user_id ){
		$webhooks               = WPWHPRO()->webhook->get_hooks( 'trigger', 'create_user' );
		$user_data              = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta'] = get_user_meta( $user_id );
		$response_data = array();

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $user_data['acf_data'] = get_fields( 'user_' . $user_id );
        }

		foreach( $webhooks as $webhook ){

			$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

			if( $webhook_name !== null ){
				$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			}

        }

        do_action( 'wpwhpro/webhooks/trigger_user_register', $user_id, $user_data, $response_data );
    }

    /**
     * LOGIN USER
     */

	/*
	 * Register the user login trigger as an element
	 */
	public function trigger_login_user_content(){

		$parameter = array(
			'user_object'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-login-user-content' ) ),
			'user_meta'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-login-user-content' ) ),
			'user_login'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user login is included as well. This is the value the user used to make the login. It is also located on the first layoer of the array.', 'trigger-login-user-content' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-login_user.php' );
		$description = ob_get_clean();

		return array(
			'trigger'           => 'login_user',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Login', 'trigger-login-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', 'login_user' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user triggers the login.', 'trigger-login-user-content' ),
			'description'       => $description,
			'callback'          => 'test_user_login'
		);

	}

	/*
	 * Register the user login trigger logic
	 */
	public function ironikus_trigger_user_login_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_user_login' ), func_get_args() );
	}
	public function ironikus_trigger_user_login( $user_login, $user_obj ){

		$user_id = 0;

		if( is_object( $user_obj ) && isset( $user_obj->data ) ){
			if( isset( $user_obj->data->ID ) ){
				$user_id = $user_obj->data->ID;
			}
		}

		$webhooks                = WPWHPRO()->webhook->get_hooks( 'trigger', 'login_user' );
		$user_data               = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta']  = get_user_meta( $user_id );
		$user_data['user_login'] = get_user_meta( $user_login );
		$response_data = array();

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $user_data['acf_data'] = get_fields( 'user_' . $user_id );
        }

		foreach( $webhooks as $webhook ){

			$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

			if( $webhook_name !== null ){
				$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_user_login', $user_id, $user_data, $response_data );
	}

	/**
	 * USER UPDATE
	 */

	/*
	 * Register the user update trigger as an element
	 *
	 * @return array
	 */
	public function trigger_login_user_update(){

		$parameter = array(
			'user_object'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-update-user-content' ) ),
			'user_meta'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-update-user-content' ) ),
			'user_old_data' => array( 'short_description' => WPWHPRO()->helpers->translate( 'This is the object with the previous user object as an array. You can recheck your data on it as well.', 'trigger-update-user-content' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-update_user.php' );
		$description = ob_get_clean();

		return array(
			'trigger'           => 'update_user',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On User Update', 'trigger-update-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', 'update_user' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user updates his profile.', 'trigger-update-user-content' ),
			'description'       => $description,
			'callback'          => 'test_user_update'
		);

	}

	/*
	 * Register the user update trigger logic
	 */
	public function ironikus_trigger_user_update_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_user_update' ), func_get_args() );
	}
	public function ironikus_trigger_user_update( $user_id, $old_data ){
		$webhooks                   = WPWHPRO()->webhook->get_hooks( 'trigger', 'update_user' );
		$user_data                  = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta']     = get_user_meta( $user_id );
		$user_data['user_old_data'] = $old_data;
		$response_data = array();

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $user_data['acf_data'] = get_fields( 'user_' . $user_id );
        }

		foreach( $webhooks as $webhook ){

			$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

			if( $webhook_name !== null ){
				$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_user_update', $user_id, $user_data, $response_data );
	}

	/**
	 * USER DELETED
	 */

	/*
	 * Register the user update trigger as an element
	 *
	 * @return array
	 */
	public function trigger_user_deleted(){

		$parameter = array(
			'user_id'   	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the deleted user', 'trigger-deleted-user-content' ) ),
			'reassign'     	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'ID of the user to reassign posts and links to. Default null, for no reassignment.', 'trigger-deleted-user-content' ) ),
			'user'     		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The full user data from the WP_User object.', 'trigger-deleted-user-content' ) ),
			'user_meta'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'All of the assigned user meta of the given user.', 'trigger-deleted-user-content' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-deleted_user.php' );
		$description = ob_get_clean();

		return array(
			'trigger'           => 'deleted_user',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On User Deletion', 'trigger-deleted-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_user_deleted( array(), '', 'deleted_user' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user was deleted.', 'trigger-deleted-user-content' ),
			'description'       => $description,
			'callback'          => 'test_deleted_user'
		);

	}

	/*
	 * Preserve the user data before deletion
	 *
	 * @since 2.0.2
	 */
	public function ironikus_prepare_user_delete( $user_id ){

		if( ! isset( $this->pre_action_values['delete_user_user_data'] ) ){
			$this->pre_action_values['delete_user_user_data'] = array();
		}

		if( ! isset( $this->pre_action_values['delete_user_user_meta'] ) ){
			$this->pre_action_values['delete_user_user_meta'] = array();
		}

		$this->pre_action_values['delete_user_user_data'][ $user_id ] = get_userdata( $user_id );
		$this->pre_action_values['delete_user_user_meta'][ $user_id ] = get_user_meta( $user_id );

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			$this->pre_action_values['delete_user_user_acf_data'][ $user_id ] = get_fields( 'user_' . $user_id );
        }
	}

	/*
	 * Register the user update trigger logic
	 */
	public function ironikus_trigger_deleted_user_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_user_deleted' ), func_get_args() );
	}
	public function ironikus_trigger_user_deleted( $user_id, $reassign, $user = null ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'deleted_user' );

		$user_data = array(
			'user_id' => $user_id,
			'reassign' => $reassign,
			'user' => null, //Keep it to preserve the order
			'user_meta' => $this->pre_action_values['delete_user_user_meta'][ $user_id ],
		);

		//Adjust fetching of user object based on the WP 5.5 update
		if( ! empty( $user ) ){
			$user_data['user'] = $user;
		} else {
			$user_data['user'] = $this->pre_action_values['delete_user_user_data'][ $user_id ];
		}

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			$user_data['acf_data'] = $this->pre_action_values['delete_user_user_acf_data'][ $user_id ];
        }

		$response_data = array();

		foreach( $webhooks as $webhook ){

			$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

			if( $webhook_name !== null ){
				$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_user_deleted', $user_id, $user_data, $response_data );
	}

	public function ironikus_send_demo_user_deleted( $data, $webhook, $webhook_group ){
		$data = array(
			'user_id' => 1234,
			'reassign' => 1235,
			'user' => array (
				'data' =>
					array (
						'ID' => '1',
						'user_login' => 'admin',
						'user_pass' => '$P$BVbptZxEcZV2yeLyYeN.O4ZeG8225d.',
						'user_nicename' => 'admin',
						'user_email' => 'admin@ironikus.dev',
						'user_url' => '',
						'user_registered' => '2018-11-06 14:19:18',
						'user_activation_key' => '',
						'user_status' => '0',
						'display_name' => 'admin',
					),
				'ID' => 1,
				'caps' =>
					array (
						'administrator' => true,
					),
				'cap_key' => 'irn_capabilities',
				'roles' =>
					array (
						0 => 'administrator',
					),
				'allcaps' =>
					array (
						'switch_themes' => true,
						'edit_themes' => true,
						'activate_plugins' => true,
						'edit_plugins' => true,
						'edit_users' => true,
						'edit_files' => true,
						'manage_options' => true,
						'moderate_comments' => true,
						'manage_categories' => true,
						'manage_links' => true,
						'upload_files' => true,
						'import' => true,
						'unfiltered_html' => true,
						'edit_posts' => true,
						'edit_others_posts' => true,
						'edit_published_posts' => true,
						'publish_posts' => true,
						'edit_pages' => true,
						'read' => true,
						'level_10' => true,
						'level_9' => true,
						'level_8' => true,
						'level_7' => true,
						'level_6' => true,
						'level_5' => true,
						'level_4' => true,
						'level_3' => true,
						'level_2' => true,
						'level_1' => true,
						'level_0' => true,
						'edit_others_pages' => true,
						'edit_published_pages' => true,
						'publish_pages' => true,
						'delete_pages' => true,
						'delete_others_pages' => true,
						'delete_published_pages' => true,
						'delete_posts' => true,
						'delete_others_posts' => true,
						'delete_published_posts' => true,
						'delete_private_posts' => true,
						'edit_private_posts' => true,
						'read_private_posts' => true,
						'delete_private_pages' => true,
						'edit_private_pages' => true,
						'read_private_pages' => true,
						'delete_users' => true,
						'create_users' => true,
						'unfiltered_upload' => true,
						'edit_dashboard' => true,
						'update_plugins' => true,
						'delete_plugins' => true,
						'install_plugins' => true,
						'update_themes' => true,
						'install_themes' => true,
						'update_core' => true,
						'list_users' => true,
						'remove_users' => true,
						'promote_users' => true,
						'edit_theme_options' => true,
						'delete_themes' => true,
						'export' => true,
						'administrator' => true,
					),
				'filter' => NULL,
			),
			'user_meta' => array (
				'nickname' =>
					array (
						0 => 'admin',
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
						0 => 'My descriptio ',
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
				'irn_capabilities' =>
					array (
						0 => 'a:1:{s:13:"administrator";b:1;}',
					),
				'irn_user_level' =>
					array (
						0 => '10',
					),
				'dismissed_wp_pointers' =>
					array (
						0 => 'wp111_privacy',
					),
				'show_welcome_panel' =>
					array (
						0 => '1',
					),
				'session_tokens' =>
					array (
						0 => 'a:1:{}',
					),
				'irn_dashboard_quick_press_last_post_id' =>
					array (
						0 => '4',
					),
				'community-events-location' =>
					array (
						0 => 'a:1:{s:2:"ip";s:9:"127.0.0.0";}',
					),
				'show_try_gutenberg_panel' =>
					array (
						0 => '0',
					),
			),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $data['acf_data'] = array(
                'demo_repeater_field' => array(
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                ),
                'demo_text_field' => 'Some demo text',
                'demo_true_false' => true,
            );
        }

		return $data;
	}

	/**
	 * POST CREATE
	 */

	/*
	 * Register the create post trigger as an element
	 *
	 * @since 1.2
	 */
	public function trigger_post_create(){

	    $validated_post_types = array();
	    foreach( get_post_types() as $name ){

	        $singular_name = $name;
		    $post_type_obj = get_post_type_object( $singular_name );
		    if( ! empty( $post_type_obj->labels->singular_name ) ){
		        $singular_name = $post_type_obj->labels->singular_name;
            } elseif( ! empty( $post_type_obj->labels->name ) ){
		        $singular_name = $post_type_obj->labels->name;
            }

		    $validated_post_types[ $name ] = $singular_name;
        }

		$parameter = array(
			'post_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the created post.', 'trigger-post-create' ) ),
			'post'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'The whole post object with all of its values', 'trigger-post-create' ) ),
			'post_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'An array of the whole post meta data.', 'trigger-post-create' ) ),
			'post_thumbnail' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full featured image/thumbnail URL in the full size.', 'trigger-post-create' ) ),
			'post_permalink' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The permalink of the currently given post.', 'trigger-post-create' ) ),
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-create' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields post meta is also pushed to the post object. You will find it on the first layer of the object as well. ', 'trigger-post-create' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-post_create.php' );
		$description = ob_get_clean();

		$settings = array(
            'load_default_settings' => true,
            'data' => array(
	            'wpwhpro_post_create_trigger_on_post_type' => array(
		            'id'          => 'wpwhpro_post_create_trigger_on_post_type',
		            'type'        => 'select',
		            'multiple'    => true,
		            'choices'      => $validated_post_types,
		            'label'       => WPWHPRO()->helpers->translate('Trigger on selected post types', 'wpwhpro-fields-trigger-on-post-type'),
		            'placeholder' => '',
		            'required'    => false,
		            'description' => WPWHPRO()->helpers->translate('Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wpwhpro-fields-trigger-on-post-type-tip')
	            ),
	            'wpwhpro_post_create_trigger_on_post_status' => array(
		            'id'          => 'wpwhpro_post_create_trigger_on_post_status',
		            'type'        => 'select',
		            'multiple'    => true,
		            'choices'      => WPWHPRO()->settings->get_all_post_statuses(),
		            'label'       => WPWHPRO()->helpers->translate('Trigger on initial post status change', 'wpwhpro-fields-trigger-on-post-type'),
		            'placeholder' => '',
		            'required'    => false,
		            'description' => WPWHPRO()->helpers->translate('Select only the post status you want to fire the trigger on. You can also choose multiple ones. Important: This trigger only fires after the initial post status change. If you change the status after again, it doesn\'t fire anymore. We also need to set a post meta value in the database after you chose the post status functionality.', 'wpwhpro-fields-trigger-on-post-type-tip')
	            ),
            )
        );

		return array(
			'trigger'           => 'post_create',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On New Post', 'trigger-post-create' ),
			'parameter'         => $parameter,
			'settings'          => $settings,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_post_create( array(), '', '' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a new post was created.', 'trigger-post-create' ),
			'description'       => $description,
			'callback'          => 'test_post_create'
		);

	}

	/*
	 * Register the post update trigger as an element
	 *
	 * @since 1.2
	 */
	public function trigger_post_update(){

		$validated_post_types = array();
		foreach( get_post_types() as $name ){

			$singular_name = $name;
			$post_type_obj = get_post_type_object( $singular_name );
			if( ! empty( $post_type_obj->labels->singular_name ) ){
				$singular_name = $post_type_obj->labels->singular_name;
			} elseif( ! empty( $post_type_obj->labels->name ) ){
				$singular_name = $post_type_obj->labels->name;
			}

			$validated_post_types[ $name ] = $singular_name;
		}

		$parameter = array(
			'post_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the updated post.', 'trigger-post-update' ) ),
			'post'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'The whole post object with all of its values', 'trigger-post-update' ) ),
			'post_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'An array of the whole post meta data.', 'trigger-post-update' ) ),
			'post_thumbnail' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full featured image/thumbnail URL in the full size.', 'trigger-post-update' ) ),
			'post_permalink' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The permalink of the currently given post.', 'trigger-post-update' ) ),
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-update' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields post meta is also pushed to the post object. You will find it on the first layer of the object as well. ', 'trigger-post-update' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-post_update.php' );
		$description = ob_get_clean();

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_post_update_trigger_on_post_type' => array(
					'id'          => 'wpwhpro_post_update_trigger_on_post_type',
					'type'        => 'select',
					'multiple'    => true,
					'choices'      => $validated_post_types,
					'label'       => WPWHPRO()->helpers->translate('Trigger on selected post types', 'wpwhpro-fields-trigger-on-post-type'),
					'placeholder' => '',
					'required'    => false,
					'description' => WPWHPRO()->helpers->translate('Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wpwhpro-fields-trigger-on-post-type-tip')
				),
				'wpwhpro_post_update_trigger_on_post_status' => array(
					'id'          => 'wpwhpro_post_update_trigger_on_post_status',
					'type'        => 'text',
					'label'       => WPWHPRO()->helpers->translate('Trigger on post status change', 'wpwhpro-fields-trigger-on-post-type'),
					'placeholder' => '',
					'required'    => false,
					'description' => WPWHPRO()->helpers->translate('Define specifc post statuses that you want to fire the trigger on. In case you want to add multiple once, please comma-separate them (e.g.: publish,draft). If none are set, all are triggered.', 'wpwhpro-fields-trigger-on-post-type-tip')
				),
			)
		);

		return array(
			'trigger'           => 'post_update',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Post Update', 'trigger-post-update' ),
			'parameter'         => $parameter,
			'settings'          => $settings,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_post_create( array(), '', '' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after an existing post is updated.', 'trigger-post-update' ),
			'description'       => $description,
			'callback'          => 'test_post_create'
		);

	}

	/*
	 * Register the post delete trigger as an element
	 *
	 * @since 1.2
	 */
	public function trigger_post_delete(){

		$validated_post_types = array();
		foreach( get_post_types() as $name ){

			$singular_name = $name;
			$post_type_obj = get_post_type_object( $singular_name );
			if( ! empty( $post_type_obj->labels->singular_name ) ){
				$singular_name = $post_type_obj->labels->singular_name;
			} elseif( ! empty( $post_type_obj->labels->name ) ){
				$singular_name = $post_type_obj->labels->name;
			}

			$validated_post_types[ $name ] = $singular_name;
		}

		$parameter = array(
			'post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the deleted post.', 'trigger-post-delete' ) ),
			'post' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Thefull post data from get_post().', 'trigger-post-delete' ) ),
			'post_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full post meta of the post.', 'trigger-post-delete' ) ),
			'post_thumbnail' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full featured image/thumbnail URL in the full size.', 'trigger-post-delete' ) ),
			'post_permalink' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The permalink of the currently given post.', 'trigger-post-delete' ) ),
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-delete' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields post meta is also pushed to the post object. You will find it on the first layer of the object as well. ', 'trigger-post-delete' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-post_delete.php' );
		$description = ob_get_clean();

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_post_delete_trigger_on_post_type' => array(
					'id'          => 'wpwhpro_post_delete_trigger_on_post_type',
					'type'        => 'select',
					'multiple'    => true,
					'choices'      => $validated_post_types,
					'label'       => WPWHPRO()->helpers->translate('Trigger on selected post types', 'wpwhpro-fields-trigger-on-post-type'),
					'placeholder' => '',
					'required'    => false,
					'description' => WPWHPRO()->helpers->translate('Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wpwhpro-fields-trigger-on-post-type-tip')
				),
			)
		);

		return array(
			'trigger'           => 'post_delete',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Post Deletion', 'trigger-post-delete' ),
			'parameter'         => $parameter,
			'settings'          => $settings,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_post_delete( array(), '', '' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a post was deleted.', 'trigger-post-delete' ),
			'description'       => $description,
			'callback'          => 'test_post_delete'
		);

	}

	/*
	 * Register the post trash trigger as an element
	 *
	 * @since 2.0.4
	 */
	public function trigger_post_trash(){

		$validated_post_types = array();
		foreach( get_post_types() as $name ){

			$singular_name = $name;

			//Media is by default not supported by WordPress
			if( $name === 'attachment' ){
				continue;
			}

			$post_type_obj = get_post_type_object( $singular_name );
			if( ! empty( $post_type_obj->labels->singular_name ) ){
				$singular_name = $post_type_obj->labels->singular_name;
			} elseif( ! empty( $post_type_obj->labels->name ) ){
				$singular_name = $post_type_obj->labels->name;
			}

			$validated_post_types[ $name ] = $singular_name;
		}

		$parameter = array(
			'post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the trashed post.', 'trigger-post-trash' ) ),
			'post' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Thefull post data from get_post().', 'trigger-post-trash' ) ),
			'post_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full post meta of the post.', 'trigger-post-trash' ) ),
			'post_thumbnail' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full featured image/thumbnail URL in the full size.', 'trigger-post-trash' ) ),
			'post_permalink' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The permalink of the currently given post.', 'trigger-post-trash' ) ),
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-trash' ) ),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields post meta is also pushed to the post object. You will find it on the first layer of the object as well. ', 'trigger-post-trash' ) );
        }

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-post_trash.php' );
		$description = ob_get_clean();

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_post_trash_trigger_on_post_type' => array(
					'id'          => 'wpwhpro_post_trash_trigger_on_post_type',
					'type'        => 'select',
					'multiple'    => true,
					'choices'      => $validated_post_types,
					'label'       => WPWHPRO()->helpers->translate('Trigger on selected post types', 'wpwhpro-fields-trigger-on-post-type'),
					'placeholder' => '',
					'required'    => false,
					'description' => WPWHPRO()->helpers->translate('Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wpwhpro-fields-trigger-on-post-type-tip')
				),
			)
		);

		return array(
			'trigger'           => 'post_trash',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Post Trash', 'trigger-post-trash' ),
			'parameter'         => $parameter,
			'settings'          => $settings,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_post_delete( array(), '', '' ) ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a post was trashed.', 'trigger-post-trash' ),
			'description'       => $description,
			'callback'          => 'test_post_delete'
		);

	}

	/*
	 * Register the post delete trigger as an element
	 *
	 * @since 1.6.4
	 */
	public function trigger_custom_action_content(){

		$parameter = array();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger-custom_action.php' );
		$description = ob_get_clean();

		$settings = array(
			'load_default_settings' => true
		);

		return array(
			'trigger'           => 'custom_action',
			'name'              => WPWHPRO()->helpers->translate( 'Send Data On Custom Action', 'trigger-custom-action' ),
			'parameter'         => $parameter,
			'settings'          => $settings,
			'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_custom_action() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a custom action was called. For more information, please check the description.', 'trigger-custom-action' ),
			'description'       => $description,
			'callback'          => 'test_custom_action'
		);

	}

	/*
	 * Trigger webhook to fire as well on attachment creation
	 *
	 * This is a related issue to the already mentioned one
	 * here: https://github.com/Ironikus/wp-webhooks/issues/2
	 *
	 * @since 2.1.8
	 */
	public function ironikus_trigger_post_create_attachment_init( $post_id ){

		if( empty(  $post_id ) || ! is_numeric( $post_id ) ){
			return;
		}

		$post = get_post( $post_id );
		if( empty( $post ) ){
			$post = array();
		}

		$this->ironikus_trigger_post_create_init( $post_id, $post, false );
	}

	/*
	 * Register the register post trigger logic
	 *
	 * The webhook needs to be cancelled on a webhook level since the post delay
	 * requires a check on the update hook as well.
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_create_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_post_create' ), func_get_args() );
	}
	public function ironikus_trigger_post_create( $post_id, $post, $update ){

		$was_fired = false;

		$tax_output = array();
		$taxonomies = get_taxonomies( array(),'names' );
		if( ! empty( $taxonomies ) ){
			$tax_terms = wp_get_post_terms( $post_id, $taxonomies );
			foreach( $tax_terms as $sk => $sv ){

				if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
					continue;
				}

				if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
					$tax_output[ $sv->taxonomy ] = array();
				}

				if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
					$tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
				}

				$tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

			}
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_create' );
		$data_array = array(
			'post_id'   => $post_id,
			'post'      => $post,
			'post_meta' => get_post_meta( $post_id ),
			'post_thumbnail' => get_the_post_thumbnail_url( $post_id,'full' ),
			'post_permalink' => get_permalink( $post_id ),
			'taxonomies'=> $tax_output
		);
		$response_data = array();
		$backwards_compatibility = get_post_meta( $post_id, 'wpwhpro_create_post_temp_status', true );

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $data_array['acf_data'] = get_fields( $post_id );
        }

		foreach( $webhooks as $webhook_ident => $webhook ){

			$is_valid = true;
			$temp_post_status_change = get_post_meta( $post_id, 'wpwhpro_create_post_temp_status_' . $webhook_ident, true );

			if( ! empty( $backwards_compatibility ) && empty( $temp_post_status_change ) ){
				$temp_post_status_change = $backwards_compatibility;
			}

			if( $update && empty( $temp_post_status_change ) ){
				continue; //Prevent the webhook from being fired if it is a update
			} else {
				$was_fired = true;
			}

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_post_create_trigger_on_post_type' && ! empty( $settings_data ) ){
						if( ! in_array( $post->post_type, $settings_data ) ){
							$is_valid = false;
						}
					}

					if( $settings_name === 'wpwhpro_post_create_trigger_on_post_status' && ! empty( $settings_data ) && $post->post_status !== 'inherit' ){

						if( ! in_array( $post->post_status, $settings_data ) ){

							update_post_meta( $post_id, 'wpwhpro_create_post_temp_status_' . $webhook_ident, $post->post_status );
							$is_valid = false;

						} else {

							if( ! empty( $temp_post_status_change ) ){
								delete_post_meta( $post_id, 'wpwhpro_create_post_temp_status_' . $webhook_ident );

								do_action( 'wpwhpro/webhooks/trigger_post_create_post_status', $post_id, $post, $response_data );
							}

						}

					}
				}
			}

			if( $is_valid ){
				$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

				if( $webhook_name !== null ){
					$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				}
			}
		}

		if( ! empty( $backwards_compatibility ) ){
			delete_post_meta( $post_id, 'wpwhpro_create_post_temp_status' ); //Backwards compatibility
		}

		if( $was_fired ){
			do_action( 'wpwhpro/webhooks/trigger_post_create', $post_id, $post, $response_data );
		}

	}

	/*
	 * Preserve the post_before on update_post
	 *
	 * @since 2.0.0
	 */
	public function ironikus_prepare_post_update( $post_ID, $post_after, $post_before ){
		$this->pre_action_values['update_post_post_before'] = $post_before;
	}

	/*
	 * Add attachment logic to default post_update funnctionality
	 *
	 * @see https://github.com/Ironikus/wp-webhooks/issues/2
	 * @since 2.1.8
	 */
	public function ironikus_trigger_post_update_attachment_init( $post_ID, $post_after, $post_before ){

		$this->pre_action_values['update_post_post_before'] = $post_before;

		$this->ironikus_trigger_post_update_init( $post_ID, $post_after, true );
	}

	/*
	 * Register the register post trigger logic
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_update_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_post_update' ), func_get_args() );
	}
	public function ironikus_trigger_post_update( $post_id, $post, $update ){

	    if( $update ){

			$tax_output = array();
			$taxonomies = get_taxonomies( array(),'names' );
			if( ! empty( $taxonomies ) ){
				$tax_terms = wp_get_post_terms( $post_id, $taxonomies );
				foreach( $tax_terms as $sk => $sv ){

					if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
						continue;
					}

					if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
						$tax_output[ $sv->taxonomy ] = array();
					}

					if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
						$tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
					}

					$tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

				}
			}

			$post_before = isset( $this->pre_action_values['update_post_post_before'] ) ? $this->pre_action_values['update_post_post_before'] : false;

		    $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_update' );
		    $data_array = array(
			    'post_id'   => $post_id,
			    'post'      => $post,
				'post_meta' => get_post_meta( $post_id ),
				'post_before' => $post_before,
				'post_thumbnail' => get_the_post_thumbnail_url( $post_id,'full' ),
				'post_permalink' => get_permalink( $post_id ),
				'taxonomies'=> $tax_output
		    );
		    $response_data = array();

			if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
				$data_array['acf_data'] = get_fields( $post_id );
			}

		    foreach( $webhooks as $webhook ){

			    $is_valid = true;

			    if( isset( $webhook['settings'] ) ){
				    foreach( $webhook['settings'] as $settings_name => $settings_data ){

					    if( $settings_name === 'wpwhpro_post_update_trigger_on_post_type' && ! empty( $settings_data ) ){
						    if( ! in_array( $post->post_type, $settings_data ) ){
							    $is_valid = false;
						    }
					    }

					    if( $settings_name === 'wpwhpro_post_update_trigger_on_post_status' && ! empty( $settings_data ) ){
						    
							$allowed_statuses = explode( ',', $settings_data );
							if( is_array( $allowed_statuses ) && is_object( $post_before ) ){

								if( $post_before->post_status === $post->post_status || ! in_array( $post->post_status, $allowed_statuses ) ){
									$is_valid = false;
								}
								
							}
							
					    }

				    }
			    }

			    if( $is_valid ){
					$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

					if( $webhook_name !== null ){
						$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
			    }
		    }

		    do_action( 'wpwhpro/webhooks/trigger_post_update', $post_id, $post, $response_data );
        }
	}

	/*
	 * Preserve the post_before on update_post
	 *
	 * @since 2.0.2
	 */
	public function ironikus_prepare_post_delete( $post_ID ){
		if( ! isset( $this->pre_action_values['delete_post_post_data'] ) ){
			$this->pre_action_values['delete_post_post_data'] = array();
		}

		if( ! isset( $this->pre_action_values['delete_post_post_meta'] ) ){
			$this->pre_action_values['delete_post_post_meta'] = array();
		}

		$this->pre_action_values['delete_post_post_data'][ $post_ID ] = get_post( $post_ID );
		$this->pre_action_values['delete_post_post_meta'][ $post_ID ] = get_post_meta( $post_ID );
		$this->pre_action_values['delete_post_post_thumbnail_url'][ $post_ID ] = get_the_post_thumbnail_url( $post_ID,'full' );
		$this->pre_action_values['delete_post_post_permalink'][ $post_ID ] = get_permalink( $post_ID );

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			$this->pre_action_values['delete_post_post_acf_data'][ $post_ID ] = get_fields( $post_ID );
		}

		//add the taxonomy
		$tax_output = array();
		$taxonomies = get_taxonomies( array(),'names' );
		if( ! empty( $taxonomies ) ){
			$tax_terms = wp_get_post_terms( $post_ID, $taxonomies );
			foreach( $tax_terms as $sk => $sv ){

				if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
					continue;
				}

				if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
					$tax_output[ $sv->taxonomy ] = array();
				}

				if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
					$tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
				}

				$tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

			}
		}

		$this->pre_action_values['delete_post_post_taxonomies'][ $post_ID ] = $tax_output;
	}

	/*
	 * Register the post delete trigger logic
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_delete_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_post_delete' ), func_get_args() );
	}
	public function ironikus_trigger_post_delete( $post_id ){

        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_delete' );
		$post = $this->pre_action_values['delete_post_post_data'][ $post_id ];
        $data_array = array(
            'post_id' => $post_id,
            'post'      => $post,
            'post_meta' => $this->pre_action_values['delete_post_post_meta'][ $post_id ],
            'post_thumbnail' => $this->pre_action_values['delete_post_post_thumbnail_url'][ $post_id ],
            'post_permalink' => $this->pre_action_values['delete_post_post_permalink'][ $post_id ],
            'taxonomies' => $this->pre_action_values['delete_post_post_taxonomies'][ $post_id ],
        );
		$response_data = array();

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			$data_array['acf_data'] = $this->pre_action_values['delete_post_post_acf_data'][ $post_id ];
		}

        foreach( $webhooks as $webhook ){

	        $is_valid = true;

	        if( isset( $webhook['settings'] ) ){
		        foreach( $webhook['settings'] as $settings_name => $settings_data ){

			        if( $settings_name === 'wpwhpro_post_delete_trigger_on_post_type' && ! empty( $settings_data ) ){
				        if( ! in_array( $post->post_type, $settings_data ) ){
					        $is_valid = false;
				        }
			        }

		        }
	        }

	        if( $is_valid ) {
				$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

				if( $webhook_name !== null ){
					$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				}
	        }
        }

        do_action( 'wpwhpro/webhooks/trigger_post_delete', $post_id, $response_data );
	}

	/**
     * Register the demo post create trigger callback
	 *
	 * @since 1.2
     *
	 * @param $data - The default data
	 * @param $webhook - The current webhook
	 * @param $webhook_group - The current webhook group (trigger-)
	 *
	 * @return array
	 */
	public function ironikus_send_demo_post_create( $data, $webhook, $webhook_group ) {

		$data = array(
		        'post_id' => 1234,
		        'post' => array (
			        'ID' => 1,
			        'post_author' => '1',
			        'post_date' => '2018-11-06 14:19:18',
			        'post_date_gmt' => '2018-11-06 14:19:18',
			        'post_content' => 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
			        'post_title' => 'Hello world!',
			        'post_excerpt' => '',
			        'post_status' => 'publish',
			        'comment_status' => 'open',
			        'ping_status' => 'open',
			        'post_password' => '',
			        'post_name' => 'hello-world',
			        'to_ping' => '',
			        'pinged' => '',
			        'post_modified' => '2018-11-06 14:19:18',
			        'post_modified_gmt' => '2018-11-06 14:19:18',
			        'post_content_filtered' => '',
			        'post_parent' => 0,
			        'guid' => 'https://mydomain.dev/?p=1',
			        'menu_order' => 0,
			        'post_type' => 'post',
			        'post_mime_type' => '',
			        'comment_count' => '1',
			        'filter' => 'raw',
		        ),
		        'post_meta' => array (
			        'key_0' =>
				        array (
					        0 => '0.00',
				        ),
			        'key_1' =>
				        array (
					        0 => '0',
				        ),
			        'key_2' =>
				        array (
					        0 => '1',
				        ),
			        'key_3' =>
				        array (
					        0 => '148724528:1',
				        ),
			        'key_4' =>
				        array (
					        0 => '10.00',
				        ),
			        'key_5' =>
				        array (
					        0 => 'a:0:{}',
				        ),
				),
				'post_thumbnail' => 'https://mydomain.com/images/image.jpg',
				'post_permalink' => 'https://mydomain.com/the-post/permalink',
				'taxonomies' => array (
					'category' =>
					array (
					  'uncategorized' =>
					  array (
						'term_id' => 1,
						'name' => 'Uncategorized',
						'slug' => 'uncategorized',
						'term_group' => 0,
						'term_taxonomy_id' => 1,
						'taxonomy' => 'category',
						'description' => '',
						'parent' => 10,
						'count' => 7,
						'filter' => 'raw',
					  ),
					  'secondcat' =>
					  array (
						'term_id' => 2,
						'name' => 'Second Cat',
						'slug' => 'secondcat',
						'term_group' => 0,
						'term_taxonomy_id' => 2,
						'taxonomy' => 'category',
						'description' => '',
						'parent' => 1,
						'count' => 1,
						'filter' => 'raw',
					  ),
					),
				),
        );

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			$data['acf_data'] = array(
				'demo_repeater_field' => array(
					array(
						'demo_field_1' => 'Demo Value 1',
						'demo_field_2' => 'Demo Value 2',
					),
					array(
						'demo_field_1' => 'Demo Value 1',
						'demo_field_2' => 'Demo Value 2',
					),
				),
				'demo_text_field' => 'Some demo text',
				'demo_true_false' => true,
			);
		}

		return $data;
	}

	/*
	 * Register the post delete trigger logic
	 *
	 * @since 2.0.4
	 */
	public function ironikus_trigger_post_trash_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_post_trash' ), func_get_args() );
	}
	public function ironikus_trigger_post_trash( $post_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_trash' );

		$tax_output = array();
		$taxonomies = get_taxonomies( array(),'names' );
		if( ! empty( $taxonomies ) ){
			$tax_terms = wp_get_post_terms( $post_id, $taxonomies );
			foreach( $tax_terms as $sk => $sv ){

				if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
					continue;
				}

				if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
					$tax_output[ $sv->taxonomy ] = array();
				}

				if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
					$tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
				}

				$tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

			}
		}

		$post = get_post( $post_id );
        $data_array = array(
            'post_id' => $post_id,
            'post'      => $post,
			'post_meta' => get_post_meta( $post_id ),
			'post_thumbnail' => get_the_post_thumbnail_url( $post_id, 'full' ),
			'post_permalink' => get_permalink( $post_id ),
			'taxonomies' => $tax_output
        );
		$response_data = array();

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $data_array['acf_data'] = get_fields( $post_id );
        }

        foreach( $webhooks as $webhook ){

	        $is_valid = true;

	        if( isset( $webhook['settings'] ) ){
		        foreach( $webhook['settings'] as $settings_name => $settings_data ){

			        if( $settings_name === 'wpwhpro_post_trash_trigger_on_post_type' && ! empty( $settings_data ) ){
				        if( ! in_array( $post->post_type, $settings_data ) ){
					        $is_valid = false;
				        }
			        }

		        }
	        }

	        if( $is_valid ) {
				$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

				if( $webhook_name !== null ){
					$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
				}
	        }
        }

        do_action( 'wpwhpro/webhooks/trigger_post_trash', $post_id, $response_data );
	}

	/*
	 * Register the demo post delete trigger callback
	 *
	 * @since 1.2
	 */
	public function ironikus_send_demo_post_delete( $data, $webhook, $webhook_group ) {

		$data = array(
			'post_id' => 12345,
			'post' => array (
				'ID' => 1,
				'post_author' => '1',
				'post_date' => '2018-11-06 14:19:18',
				'post_date_gmt' => '2018-11-06 14:19:18',
				'post_content' => 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
				'post_title' => 'Hello world!',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'open',
				'post_password' => '',
				'post_name' => 'hello-world',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2018-11-06 14:19:18',
				'post_modified_gmt' => '2018-11-06 14:19:18',
				'post_content_filtered' => '',
				'post_parent' => 0,
				'guid' => 'https://mydomain.dev/?p=1',
				'menu_order' => 0,
				'post_type' => 'post',
				'post_mime_type' => '',
				'comment_count' => '1',
				'filter' => 'raw',
			),
			'post_meta' => array (
				'key_0' =>
					array (
						0 => '0.00',
					),
				'key_1' =>
					array (
						0 => '0',
					),
				'key_2' =>
					array (
						0 => '1',
					),
				'key_3' =>
					array (
						0 => '148724528:1',
					),
				'key_4' =>
					array (
						0 => '10.00',
					),
				'key_5' =>
					array (
						0 => 'a:0:{}',
					),
			),
			'post_thumbnail' => 'https://mydomain.com/images/image.jpg',
			'post_permalink' => 'https://mydomain.com/the-post/permalink',
			'taxonomies' => array (
				'category' =>
				array (
				  'uncategorized' =>
				  array (
					'term_id' => 1,
					'name' => 'Uncategorized',
					'slug' => 'uncategorized',
					'term_group' => 0,
					'term_taxonomy_id' => 1,
					'taxonomy' => 'category',
					'description' => '',
					'parent' => 10,
					'count' => 7,
					'filter' => 'raw',
				  ),
				  'secondcat' =>
				  array (
					'term_id' => 2,
					'name' => 'Second Cat',
					'slug' => 'secondcat',
					'term_group' => 0,
					'term_taxonomy_id' => 2,
					'taxonomy' => 'category',
					'description' => '',
					'parent' => 1,
					'count' => 1,
					'filter' => 'raw',
				  ),
				),
			),
		);

		if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
            $data['acf_data'] = array(
                'demo_repeater_field' => array(
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                    array(
                        'demo_field_1' => 'Demo Value 1',
                        'demo_field_2' => 'Demo Value 2',
                    ),
                ),
                'demo_text_field' => 'Some demo text',
                'demo_true_false' => true,
            );
        }

		return $data;
	}

	/*
	 * Register the post delete trigger logic (DEPRECATED)
	 *
	 * @since 1.6.4
	 */
	public function wp_webhooks_send_to_webhook_action( $data, $webhook_names = array() ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'custom_action' );
		$response_data = array();

		foreach( $webhooks as $webhook_key => $webhook ){

			if( ! empty( $webhook_names ) ){
				if( ! empty( $webhook_key ) ){
					if( ! in_array( $webhook_key, $webhook_names ) ){
						continue;
					}
				}
			}

			$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : null;

			if( $webhook_name !== null ){
				$response_data[ $webhook_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
			}
		}

		do_action( 'wpwhpro/webhooks/trigger_custom_action', $data, $response_data );
	}

	/*
	 * Register the custom action trigger logic
	 *
	 * @since 2.0.5
	 */
	public function wp_webhooks_send_to_webhook_action_filter( $response_data, $data, $webhook_names = array(), $http_args = array() ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'custom_action' );

		if( ! is_array( $response_data ) ){
			$response_data = array();
		}

		foreach( $webhooks as $webhook_key => $webhook ){

			if( ! empty( $webhook_names ) ){
				if( ! empty( $webhook_key ) ){
					if( ! in_array( $webhook_key, $webhook_names ) ){
						continue;
					}
				}
			}

			$response_data[ $webhook_key ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data, $http_args );
		}

		do_action( 'wpwhpro/webhooks/trigger_custom_action', $data, $response_data );

		return $response_data;
	}

	/*
	 * Register the demo post delete trigger callback
	 *
	 * @since 1.6.4
	 */
	public function ironikus_send_demo_custom_action() {

		return array( WPWHPRO()->helpers->translate( 'Your very own data construct.', 'trigger-custom-action' ) ); // Custom content from the action
	}

}
