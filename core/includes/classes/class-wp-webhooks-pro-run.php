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
		add_action( 'wp_ajax_ironikus_save_main_settings',  array( $this, 'ironikus_save_main_settings' ) );
		add_action( 'wp_ajax_ironikus_manage_extensions',  array( $this, 'ironikus_manage_extensions' ) );

		// Load admin page tabs
		add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
		add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );

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

		//Load just active ones
		add_action( 'wpwhpro/webhooks/get_webhooks_triggers', array( $this, 'filter_active_webhooks_triggers' ), PHP_INT_MAX - 100, 2 );
		add_action( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'filter_active_webhooks_actions' ), PHP_INT_MAX - 100, 2 );
	
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
			wp_enqueue_style( 'wpwhpro-admin-styles', WPWH_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles.min.css', array(), WPWH_VERSION, 'all' );
			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_script( 'wpwhpro-admin-scripts', WPWH_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts.min.js', array( 'jquery' ), WPWH_VERSION, true );
			wp_localize_script( 'wpwhpro-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
			));
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

        $webhook_url            = isset( $_REQUEST['webhook_url'] ) ? sanitize_text_field( $_REQUEST['webhook_url'] ) : '';
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
     * Functionality to save the main settings of the settings page
     */
	public function ironikus_save_main_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $main_settings    = isset( $_REQUEST['main_settings'] ) ? $_REQUEST['main_settings'] : '';
		$response           = array( 
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('An error occured saving your data.', 'ajax-settings')
		);
		
		parse_str( $main_settings, $main_settings_data );	

		if( ! empty( $main_settings_data ) ){
		    $check = WPWHPRO()->settings->save_settings( $main_settings_data );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
		        $response['msg'] = WPWHPRO()->helpers->translate('Settings successfully saved.', 'ajax-settings');
            } else {
				$response['msg'] = WPWHPRO()->helpers->translate('Your settings couldn\'t be saved.', 'ajax-settings');
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
				$response['old_class'] = 'btn-warning';
				$response['new_class'] = 'btn-success';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_deactivate( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully deactivated.', 'ajax-settings');
				break;
			case 'deactivated': //runs when the "Activate" button was clicked
				$response['old_class'] = 'btn-success';
				$response['new_class'] = 'btn-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_activate( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully activated.', 'ajax-settings');
				break;
			case 'uninstalled': //runs when the "Install" button was clicked
				$response['old_class'] = 'btn-primary';
				$response['new_class'] = 'btn-success';
				$response['new_status'] = 'deactivated';
				$response['delete_name'] = WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-extensions' );
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_install( $extension_slug, $extension_download, $extension_id, $extension_version );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully installed.', 'ajax-settings');
				break;
			case 'update_active': //runs when the "Update" button was clicked and the previous status was active
				$response['old_class'] = 'btn-dark';
				$response['new_class'] = 'btn-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_update( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'update_deactive': //runs when the "Update" button was clicked and the previous status was inactive
				$response['old_class'] = 'btn-dark';
				$response['new_class'] = 'btn-success';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->manage_extensions_update( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'delete': //runs when the "Delete" link was clicked
				$response['old_class'] = 'btn-success';
				$response['new_class'] = 'btn-primary';
				$response['new_status'] = 'uninstalled';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
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
		$tabs['send-data']      = WPWHPRO()->helpers->translate( 'Send Data', 'admin-menu' );
		$tabs['recieve-data']   = WPWHPRO()->helpers->translate( 'Receive Data', 'admin-menu' );

		if( WPWHPRO()->auth->is_active() ){
			$tabs['authentication']  = WPWHPRO()->helpers->translate( 'Authentication', 'admin-menu' );
		}

		$tabs['extensions']       = WPWHPRO()->helpers->translate( 'Extensions', 'admin-menu' );
		$tabs['settings']       = WPWHPRO()->helpers->translate( 'Settings', 'admin-menu' );
		$tabs['pro']            = WPWHPRO()->helpers->translate( 'Pro', 'admin-menu' );

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
			case 'recieve-data':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/recieve-data.php' );
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
	 * #### FILTER ACTIVE WEBHOOK FILTERS
	 * ###
	 * ######################
	 */

	/*
	 * Return all available triggersm filtered by the currently specified status
	 *
	 * @since 1.2
	 * @param $triggers - The webhook triggers
	 * @param $active_webhooks - All active webhooks
	 */
	public function filter_active_webhooks_triggers( $triggers, $active_webhooks ){

	    if( ! $active_webhooks ){
	        return $triggers;
        }

		$active_webhooks = WPWHPRO()->settings->get_active_webhooks();

	    foreach( $triggers as $key => $trigger ){
	        if( ! isset( $active_webhooks['triggers'][ $trigger['trigger'] ] ) ){
	            unset($triggers[$key]);
            }
        }

        return $triggers;
    }

	/**
	 * ######################
	 * ###
	 * #### FILTER ACTIVE WEBHOOK ACTIONS
	 * ###
	 * ######################
	 */

	/*
	 * Filter all available webhook actions based on the currently specified status.
	 *
	 * @since 1.2
	 */
	public function filter_active_webhooks_actions( $actions, $active_webhooks ){

	    if( ! $active_webhooks ){
	        return $actions;
        }

		$active_webhooks = WPWHPRO()->settings->get_active_webhooks();

	    foreach( $actions as $key => $action ){
	        if( ! isset( $active_webhooks['actions'][ $action['action'] ] ) ){
	            unset($actions[$key]);
            }
        }

        return $actions;
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

		$active_webhooks = WPWHPRO()->settings->get_active_webhooks();
		$default_return = array(
            'success' => false
        );

		if( empty( $active_webhooks ) || empty( $active_webhooks['actions'] ) ){
			$default_return['msg'] = WPWHPRO()->helpers->translate("You currently don't have any actions available.", 'action-add-webhook-actions' );

			WPWHPRO()->webhook->echo_response_data( $default_return );
			die();
        }

		$available_triggers = $active_webhooks['actions'];

		switch( $action ){
			case 'create_user':
			    if( isset( $available_triggers['create_user'] ) ){
				    $this->action_create_user();
                }
				break;
			case 'delete_user':
				if( isset( $available_triggers['delete_user'] ) ){
					$this->action_delete_user();
				}
				break;
			case 'get_users':
				if( isset( $available_triggers['get_users'] ) ){
					$this->action_get_users();
				}
				break;
			case 'get_user':
				if( isset( $available_triggers['get_user'] ) ){
					$this->action_get_user();
				}
				break;
			case 'create_post':
				if( isset( $available_triggers['create_post'] ) ){
					$this->action_create_post();
				}
				break;
			case 'delete_post':
				if( isset( $available_triggers['delete_post'] ) ){
					$this->action_delete_post();
				}
				break;
			case 'get_posts':
				if( isset( $available_triggers['get_posts'] ) ){
					$this->action_get_posts();
				}
				break;
			case 'get_post':
				if( isset( $available_triggers['get_post'] ) ){
					$this->action_get_post();
				}
				break;
			case 'custom_action':
				if( isset( $available_triggers['custom_action'] ) ){
					$this->action_custom_action();
				}
				break;
			case 'ironikus_test':
				if( isset( $available_triggers['ironikus_test'] ) ){
					$this->action_ironikus_test();
				}
				break;
		}

		$default_return['data'] = $action;
		$default_return['msg'] = WPWHPRO()->helpers->translate("It looks like your current webhook call as no action argument defined, it is deactivated or it does not have any action function.", 'action-add-webhook-actions' );

		WPWHPRO()->webhook->echo_response_data( $default_return );
		die();
	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_user_content(){

		$parameter = array(
			'user_email'        => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'This field is required. Include the email for the user.', 'action-create-user-content' ) ),
			'first_name'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the user.', 'action-create-user-content' ) ),
			'last_name'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the user.', 'action-create-user-content' ) ),
			'nickname'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'The nickname. Please note that the nickname will be sanitized by WordPress automatically.', 'action-create-user-content' ) ),
			'user_login'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'A string with which the user can log in to your site.', 'action-create-user-content' ) ),
			'display_name'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'The name that will be seen on the frontend of your site.', 'action-create-user-content' ) ),
			'user_nicename'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'A URL-friendly name. Default is user\' username.', 'action-create-user-content' ) ),
			'description'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'A description for the user that will be available on the profile page.', 'action-create-user-content' ) ),
			'rich_editing'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether the user should be able to use the Rich editor. Set it to "yes" or "no". Default "no".', 'action-create-user-content' ) ),
			'user_registered'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date the user gets registered. Date structure: Y-m-d H:i:s', 'action-create-user-content' ) ),
			'user_url'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'Include a website url.', 'action-create-user-content' ) ),
			'role'              => array( 'short_description' => WPWHPRO()->helpers->translate( 'The main user role. If not set, default is subscriber.', 'action-create-user-content' ) ),
			'additional_roles'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'This allows to add multiple roles to a user. For more information, please read the description.', 'action-create-user-content' ) ),
			'user_pass'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user password. If not defined, we generate a 32 character long password dynamically.', 'action-create-user-content' ) ),
			'send_email'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user with the data.', 'action-create-user-content' ) ),
			'do_action'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-create-user-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-create-user-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user data with the key "user_data". E.g. array( \'data\' => array(...) )', 'action-create-user-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-user-content' ) ),
        );

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'data' => array(
        'user_id' => 0,
        'user_data' => array()
    )
);
        </pre>
        <?php
		$returns_code = ob_get_clean();

		ob_start();
			include( WPWH_PLUGIN_DIR . 'core/includes/partials/descriptions/action-create_user.php' );
		$description = ob_get_clean();

		return array(
			'action'            => 'create_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Create a new user via webhooks.', 'action-create-user-content' ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to delete a specified user
	 */
	public function action_delete_user_content(){

		$parameter = array(
			'user_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the numeric id of the user.', 'action-delete-user-content' ) ),
			'user_email'    => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the assigned email of the user.', 'action-delete-user-content' ) ),
			'send_email'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user that the account got deleted.', 'action-delete-user-content' ) ),
			'remove_from_network'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to delete a user from the whole network. WARNING: This will delete all posts authored by the user. Default: "no"', 'action-delete-user-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-delete-user-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-delete-user-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user delete success boolean with the key "user_deleted". E.g. array( \'data\' => array(...) )', 'action-delete-user-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-delete-user-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Delete a user via WP Webhooks.', 'action-create-user-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function action_get_users_content(){

		$parameter = array(
			'arguments'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_User_Query notation. Please check the description for more information.', 'action-get_users-content' ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: get_results', 'action-get_users-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-get_users-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-get_users-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user query. This depends on the parameters you send.', 'action-get_users-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-get_users-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Search for users on your WordPress website', 'action-get_users-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to get the user
	 */
	public function action_get_user_content(){

		$parameter = array(
			'user_value'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id of the user. You can also use certain other values. Please check the descripton for more details.', 'action-get_user-content' ) ),
			'value_type'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'You can choose between certain value types. Possible: id, slug, email, login', 'action-get_user-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-get_user-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-get_user-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user qury. This depends on the parameters you send.', 'action-get_user-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-get_user-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Returns the object of a user', 'action-get_user-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_post_content(){

		$parameter = array(
			'post_author'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', 'action-create-post-content' ) ),
			'post_date'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', 'action-create-post-content' ) ),
			'post_date_gmt'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', 'action-create-post-content' ) ),
			'post_content'          => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post content. Default empty.', 'action-create-post-content' ) ),
			'post_content_filtered' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The filtered post content. Default empty.', 'action-create-post-content' ) ),
			'post_title'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post title. Default empty.', 'action-create-post-content' ) ),
			'post_excerpt'          => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post excerpt. Default empty.', 'action-create-post-content' ) ),
			'post_status'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post status. Default \'draft\'.', 'action-create-post-content' ) ),
			'post_type'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post type. Default \'post\'.', 'action-create-post-content' ) ),
			'comment_status'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', 'action-create-post-content' ) ),
			'ping_status'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', 'action-create-post-content' ) ),
			'post_password'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The password to access the post. Default empty.', 'action-create-post-content' ) ),
			'post_name'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', 'action-create-post-content' ) ),
			'to_ping'               => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', 'action-create-post-content' ) ),
			'pinged'                => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', 'action-create-post-content' ) ),
			'post_parent'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', 'action-create-post-content' ) ),
			'menu_order'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', 'action-create-post-content' ) ),
			'post_mime_type'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The mime type of the post. Default empty.', 'action-create-post-content' ) ),
			'guid'                  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', 'action-create-post-content' ) ),
			'post_category'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of category names, slugs, or IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', 'action-create-post-content' ) ),
			'tags_input'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', 'action-create-post-content' ) ),
			'tax_input'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty. More details within the description.', 'action-update-post-content' ) ),
			'wp_error'              => array( 'short_description' => WPWHPRO()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', 'action-create-post-content' ) ),
			'do_action'             => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-create-post-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-create-post-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', 'action-create-post-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-post-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Insert/Create a post. You have all functionalities available from wp_insert_post', 'action-create-post-content' ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to delete a specified user
	 */
	public function action_delete_post_content(){

		$parameter = array(
			'post_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of your specified post. This field is required.', 'action-delete-post-content' ) ),
			'force_delete'  => array( 'short_description' => WPWHPRO()->helpers->translate( '(optional) Whether to bypass trash and force deletion (added in WordPress 2.9). Possible values: "yes" and "no". Default: "no". Please note that soft deletion just works for the "post" and "page" post type.', 'action-delete-post-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-delete-post-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-delete-post-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Post related data as an array. We return the post id with the key "post_id" and the force delete boolean with the key "force_delete". E.g. array( \'data\' => array(...) )', 'action-delete-post-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-delete-post-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Delete a post via WP Webhooks.', 'action-create-post-content' ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function action_get_posts_content(){

		$parameter = array(
			'arguments'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_Query notation. Please check the description for more information.', 'action-get_posts-content' ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: posts', 'action-get_posts-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-get_posts-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-get_posts-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the post query. This depends on the parameters you send.', 'action-get_posts-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-get_posts-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Search for posts on your WordPress website', 'action-get_posts-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to get a single post
	 */
	public function action_get_post_content(){

		$parameter = array(
			'post_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the post you want to fetch.', 'action-get_post-content' ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Select the values you want to return. Default is all. Please see the description for more details.', 'action-get_post-content' ) ),
			'thumbnail_size'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Pass the size of the thumbnail of your given post id. Default is full. Please see the description for more details.', 'action-get_post-content' ) ),
			'post_taxonomies'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Single value or comma separated list of the taxonomies you want to return. Default: post_tag. Please see the description for more details.', 'action-get_post-content' ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'action-get_post-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-get_post-content' ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the single post. This depends on the parameters you send.', 'action-get_post-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-get_post-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Returns the object of a user', 'action-get_post-content' ),
            'description'       => $description
		);

	}

	/*
	 * The core logic to use a custom action webhook
	 */
	public function action_custom_action_content(){

		$parameter = array(
			'wpwh_identifier'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'This value is send over within the WordPress hooks to identify the incoming action. You can use it to fire your customizatios only on specific webhooks. For more information, please check the description.', 'action-ironikus-custom_action-content' ) )
		);

		$returns = array(
			'custom'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'This webhook returns whatever you define withiin the filters. Please check the description for more detials.', 'action-ironikus-custom_action-content' ) ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Do whatever you like with the incoming data by defining this custom action.', 'action-ironikus-custom_action-content' ),
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
			'short_description' => WPWHPRO()->helpers->translate( 'Test a webhooks functionality. (Advanced)', 'action-ironikus-test-content' ),
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

		$rich_editing     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'rich_editing' ) == 'yes' ) ? true : false;
		$send_email         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
		$additional_roles   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'additional_roles' );

		if ( empty( $user_email ) ) {
			$return_args['msg'] = WPWHPRO()->helpers->translate("An email is required to create a user.", 'action-create-user-success' );

			WPWHPRO()->webhook->echo_response_data( $return_args );
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

		if( ! empty( $username ) ){
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

			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['user_data'] = $user_data;
			$return_args['success'] = true;

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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

			WPWHPRO()->webhook->echo_response_data( $return_args );
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

				if( ! empty( $user ) ){
					$return_args['msg'] = WPWHPRO()->helpers->translate("User was successfully returned.", 'action-get_users-success' );
					$return_args['success'] = true;
					$return_args['data'] = $user;
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

			WPWHPRO()->webhook->echo_response_data( $return_args );
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

				$return_args['msg'] = WPWHPRO()->helpers->translate("Query was successfully executed.", 'action-get_users-success' );
				$return_args['success'] = true;

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_users-failure' );
		}
		
		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $user_query, $args, $return_only );
		}

		WPWHPRO()->webhook->echo_response_data( $return_args );
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
			$post_data['post_modified'] = $post_modified;
		}

		if( ! empty( $post_modified_gmt ) ){
			$post_data['post_modified_gmt'] = $post_modified_gmt;
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

				if( ! empty( $tax_data['delete'] ) ){
					foreach( $tax_data['delete'] as $tax_key => $tax_values ){
						wp_remove_object_terms( $post_id, $tax_values, $tax_key );
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
			$return_args['data']['post_data'] = $post_data;
			$return_args['data']['post_id'] = $post_id;
			$return_args['success'] = true;

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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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
					$return_args['msg']  = WPWHPRO()->helpers->translate("Error deleting post. Please check wp_delete_post( ' . $post->ID . ', ' . $force_delete . ' ) for more information.", 'action-delete-post-success' );
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

		$args     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
		$return_only     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
		$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
		$post_query = null;
		
		if( empty( $args ) ){
			$return_args['msg'] = WPWHPRO()->helpers->translate("arguments is a required parameter. Please define it.", 'action-get_posts-failure' );

			WPWHPRO()->webhook->echo_response_data( $return_args );
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

		if( ! empty( $serialized_args ) && is_array( $serialized_args ) ){
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

				$return_args['msg'] = WPWHPRO()->helpers->translate("Query was successfully executed.", 'action-get_posts-success' );
				$return_args['success'] = true;

			}

		} else {
			$return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_posts-failure' );
		}
		
		if( ! empty( $do_action ) ){
			do_action( $do_action, $return_args, $post_query, $args, $return_only );
		}

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

			WPWHPRO()->webhook->echo_response_data( $return_args );
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

		WPWHPRO()->webhook->echo_response_data( $return_args );
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

		$active_webhooks = WPWHPRO()->settings->get_active_webhooks();
		$available_triggers = $active_webhooks['triggers'];

		if( isset( $available_triggers['create_user'] ) ){
			add_action( 'user_register', array( $this, 'ironikus_trigger_user_register_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_user_create', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['login_user'] ) ){
			add_action( 'wp_login', array( $this, 'ironikus_trigger_user_login_init' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_login', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['update_user'] ) ){
			add_action( 'profile_update', array( $this, 'ironikus_trigger_user_update_init' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_update', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['deleted_user'] ) ){
			add_action( 'wpmu_delete_user', array( $this, 'ironikus_prepare_user_delete' ), 10, 1 );
			add_action( 'delete_user', array( $this, 'ironikus_prepare_user_delete' ), 10, 1 );
			add_action( 'deleted_user', array( $this, 'ironikus_trigger_deleted_user_init' ), 10, 2 );
			add_filter( 'ironikus_demo_test_deleted_user', array( $this, 'ironikus_send_demo_user_deleted' ), 10, 3 );
        }

		if( isset( $available_triggers['post_create'] ) ){
			add_action( 'add_attachment', array( $this, 'ironikus_trigger_post_create_attachment_init' ), 10, 1 );
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_create_init' ), 10, 3 );
        }

		if( isset( $available_triggers['post_update'] ) ){
			add_action( 'post_updated', array( $this, 'ironikus_prepare_post_update' ), 10, 3 );
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_update_init' ), 10, 3 );
			add_action( 'attachment_updated', array( $this, 'ironikus_trigger_post_update_attachment_init' ), 10, 3 );
        }

		if( isset( $available_triggers['post_create'] ) || isset( $available_triggers['post_update'] ) ){
			add_filter( 'ironikus_demo_test_post_create', array( $this, 'ironikus_send_demo_post_create' ), 10, 3 );
        }

		if( isset( $available_triggers['post_delete'] ) ){
			add_action( 'before_delete_post', array( $this, 'ironikus_prepare_post_delete' ), 10, 1 );
			add_action( 'delete_attachment', array( $this, 'ironikus_prepare_post_delete' ), 10, 1 );
			add_action( 'delete_post', array( $this, 'ironikus_trigger_post_delete_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_post_delete', array( $this, 'ironikus_send_demo_post_delete' ), 10, 3 );
		}
		
		if( isset( $available_triggers['post_trash'] ) ){
			add_action( 'trashed_post', array( $this, 'ironikus_trigger_post_trash_init' ), 10, 1 );
			add_filter( 'ironikus_demo_test_post_delete', array( $this, 'ironikus_send_demo_post_delete' ), 10, 3 );
        }

		if( isset( $available_triggers['custom_action'] ) ){
			add_action( 'wp_webhooks_send_to_webhook', array( $this, 'wp_webhooks_send_to_webhook_action' ), 10, 2 );
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
	 * @param $webbhook_group - The current trigger this webhook belongs to
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
            'meta_data' => array (
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

		foreach( $webhooks as $webhook ){
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
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

		foreach( $webhooks as $webhook ){
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
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

		foreach( $webhooks as $webhook ){
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
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
			'user_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the deleted user', 'trigger-deleted-user-content' ) ),
			'reassign'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'ID of the user to reassign posts and links to. Default null, for no reassignment.', 'trigger-deleted-user-content' ) ),
		);

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
	}

	/*
	 * Register the user update trigger logic
	 */
	public function ironikus_trigger_deleted_user_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_user_deleted' ), func_get_args() );
	}
	public function ironikus_trigger_user_deleted( $user_id, $reassign ){
		$webhooks                   = WPWHPRO()->webhook->get_hooks( 'trigger', 'deleted_user' );
		$user_data                  = array(
			'user_id' => $user_id,
			'reassign' => $reassign,
			'user' => $this->pre_action_values['delete_user_user_data'][ $user_id ],
			'user_meta' => $this->pre_action_values['delete_user_user_meta'][ $user_id ],
		);
		$response_data = array();

		foreach( $webhooks as $webhook ){
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
		}

		do_action( 'wpwhpro/webhooks/trigger_user_deleted', $user_id, $user_data, $response_data );
	}

	public function ironikus_send_demo_user_deleted( $data, $webhook, $webhook_group ){
		$data = array(
			'user_id' => 1234,
			'reassign' => 1235
		);

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
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-create' ) ),
		);

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
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-update' ) ),
		);

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
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-delete' ) ),
		);

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
			'taxonomies' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'trigger-post-trash' ) ),
		);

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
	 * @since 1.2
	 */
	public function ironikus_trigger_post_create_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'ironikus_trigger_post_create' ), func_get_args() );
	}
	public function ironikus_trigger_post_create( $post_id, $post, $update ){

		$temp_post_status_change = get_post_meta( $post_id, 'wpwhpro_create_post_temp_status', true );

	    if( ! $update || ! empty( $temp_post_status_change ) ){

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
				'taxonomies'=> $tax_output
		    );
		    $response_data = array();

		    foreach( $webhooks as $webhook ){

		        $is_valid = true;

		        if( isset( $webhook['settings'] ) ){
			        foreach( $webhook['settings'] as $settings_name => $settings_data ){

				        if( $settings_name === 'wpwhpro_post_create_trigger_on_post_type' && ! empty( $settings_data ) ){
					        if( ! in_array( $post->post_type, $settings_data ) ){
						        $is_valid = false;
					        }
				        }

				        if( $settings_name === 'wpwhpro_post_create_trigger_on_post_status' && ! empty( $settings_data ) ){

					        if( ! in_array( $post->post_status, $settings_data ) ){

								update_post_meta( $post_id, 'wpwhpro_create_post_temp_status', $post->post_status );
								$is_valid = false;
								
					        } else {

								if( ! empty( $temp_post_status_change ) ){
									delete_post_meta( $post_id, 'wpwhpro_create_post_temp_status' );

									do_action( 'wpwhpro/webhooks/trigger_post_create_post_status', $post_id, $post, $response_data );
								}

							}

				        }
			        }
                }

                if( $is_valid ){
	                $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
                }
		    }

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

		//Make sure we only fire the create_post on status function not within the update_post webhook
		$temp_post_status_change = get_post_meta( $post_id, 'wpwhpro_create_post_temp_status', true );

		//Only call if the create_post function wasn'T called before
	    if( $update && ( empty( $temp_post_status_change ) && ! did_action( 'wpwhpro/webhooks/trigger_post_create_post_status' ) ) ){

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

		    $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_update' );
		    $data_array = array(
			    'post_id'   => $post_id,
			    'post'      => $post,
				'post_meta' => get_post_meta( $post_id ),
				'post_before' => isset( $this->pre_action_values['update_post_post_before'] ) ? $this->pre_action_values['update_post_post_before'] : false,
				'post_thumbnail' => get_the_post_thumbnail_url( $post_id,'full' ),
				'taxonomies'=> $tax_output
		    );
		    $response_data = array();

		    foreach( $webhooks as $webhook ){

			    $is_valid = true;

			    if( isset( $webhook['settings'] ) ){
				    foreach( $webhook['settings'] as $settings_name => $settings_data ){

					    if( $settings_name === 'wpwhpro_post_update_trigger_on_post_type' && ! empty( $settings_data ) ){
						    if( ! in_array( $post->post_type, $settings_data ) ){
							    $is_valid = false;
						    }
					    }

				    }
			    }

			    if( $is_valid ){
				    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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
			'taxonomies' => $this->pre_action_values['delete_post_post_taxonomies'][ $post_id ],
        );
		$response_data = array();

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
		        $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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

		return $data;
	}

	/*
	 * Register the post trash trigger logic
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
			'taxonomies' => $tax_output
        );
		$response_data = array();

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
		        $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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

		return array( 
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
	}

	/*
	 * Register the post delete trigger logic
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

			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
		}

		do_action( 'wpwhpro/webhooks/trigger_custom_action', $data, $response_data );
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
