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
	 * @since 2.0.5
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

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=plugin-overview-shop-button&utm_campaign=WP%20Webhooks%20Pro', WPWHPRO()->helpers->translate('Our Shop', 'plugin-page') );

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

			// wp_enqueue_style( 'wpwhpro-admin-styles-old', WPWH_PLUGIN_URL . 'core/includes/assets/dist/css/styles.min.css', array(), WPWH_VERSION, 'all' );
			wp_enqueue_style( 'wpwhpro-admin-styles', WPWH_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWH_VERSION, 'all' );

			wp_enqueue_script( 'jquery-ui-sortable');

			wp_enqueue_script( 'wpwhpro-admin-vendors', WPWH_PLUGIN_URL . 'core/includes/assets/dist/js/admin-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWH_VERSION, true );
			wp_enqueue_script( 'wpwhpro-admin-scripts', WPWH_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWH_VERSION, true );
			wp_localize_script( 'wpwhpro-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
				'plugin_url' => WPWH_PLUGIN_URL,
			));
			// wp_enqueue_script( 'wpwhpro-admin-scripts-old', WPWH_PLUGIN_URL . 'core/includes/assets-old/dist/js/admin-scripts.js', array( 'jquery' ), WPWH_VERSION, true );
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
			$data = WPWHPRO()->integrations->get_trigger_demo( $webhook_group, array(
				'webhook' => $webhook,
				'webhooks' => $webhooks,
				'webhook_group' => $webhook_group,
			) );	

			if( ! empty( $webhook_callback ) ){
				$data = apply_filters( 'ironikus_demo_' . $webhook_callback, $data, $webhook, $webhook_group, $webhooks[ $webhook ] );
			}

			$response_data = WPWHPRO()->webhook->post_to_webhook( $webhooks[ $webhook ], $data, array( 'blocking' => true ), true );

			if ( ! empty( $response_data ) ) {
				$response['data']       = $response_data;
				$response['success']    = true;
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
		$menu_position = get_option( 'wpwhpro_show_main_menu' );

		if( ! empty( $menu_position ) && $menu_position == 'yes' ){
			add_menu_page(
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-menu-page-title' ),
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-menu-page-site-title' ),
				WPWHPRO()->settings->get_admin_cap( 'admin-add-menu-page-item' ),
				$this->page_name,
				array( $this, 'render_admin_submenu_page' ) ,
				WPWH_PLUGIN_URL . 'core/includes/assets/img/logo-menu-wp-webhooks.svg',
				'81.025'
			);
		} else {
			add_submenu_page( 
				'options-general.php', 
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-title' ), 
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-site-title' ), 
				WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ), 
				$this->page_name, 
				array( $this, 'render_admin_submenu_page' ) 
			);
		}
		
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
			),
		);

		$tabs['settings']['items']['extensions'] = WPWHPRO()->helpers->translate( 'Extensions', 'admin-menu' );

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
			case 'data-mapping':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/pro.php' );
				break;
			case 'logs':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/pro.php' );
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

}
