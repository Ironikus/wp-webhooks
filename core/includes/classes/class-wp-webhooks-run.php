<?php

/**
 * Class WP_Webhooks_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 1.0.0
 * @package WPWH
 * @author Ironikus <info@ironikus.com>
 */

class WP_Webhooks_Run{

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
	 * Our WP_Webhooks_Run constructor.
	 */
	function __construct(){
		$this->page_name    = WPWH()->settings->get_page_name();
		$this->page_title   = WPWH()->settings->get_page_title();
		$this->add_hooks();
	}

	/**
	 * Define all of our necessary hooks
	 */
	private function add_hooks(){

		add_action( 'plugin_action_links_' . WPWH_PLUGIN_BASE, array($this, 'plugin_action_links') );

		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_user_submenu' ), 150 );
		add_action( 'wp_ajax_wpwh_ironikus_add_webhook_trigger',  array( $this, 'ironikus_add_webhook_trigger' ) );
		add_action( 'wp_ajax_wpwh_ironikus_remove_webhook_trigger',  array( $this, 'ironikus_remove_webhook_trigger' ) );
		add_action( 'wp_ajax_wpwh_ironikus_test_webhook_trigger',  array( $this, 'ironikus_test_webhook_trigger' ) );

		// Load admin page tabs
		add_filter( 'wpwh/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
		add_action( 'wpwh/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );

		// Setup actions
		add_filter( 'wpwh/webhooks/get_webhooks_actions', array( $this, 'add_webhook_actions_content' ), 10 );
		add_action( 'wpwh/webhooks/add_webhooks_actions', array( $this, 'add_webhook_actions' ), 1000, 3 );

		// Setup triggers
		add_action( 'plugins_loaded', array( $this, 'add_webhook_triggers' ), 10 );
		add_filter( 'wpwh/webhooks/get_webhooks_triggers', array( $this, 'add_webhook_triggers_content' ), 10 );

		//Load just active ones
		add_action( 'wpwh/webhooks/get_webhooks_triggers', array( $this, 'filter_active_webhooks_triggers' ), PHP_INT_MAX - 100, 2 );
		add_action( 'wpwh/webhooks/get_webhooks_actions', array( $this, 'filter_active_webhooks_actions' ), PHP_INT_MAX - 100, 2 );
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
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->page_name ), WPWH()->helpers->translate('Settings', 'plugin-page') );

		array_unshift( $links, $settings_link );

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://ironikus.com/products/?utm_source=wp-webhooks&utm_medium=plugin-overview-shop-button&utm_campaign=WP%20Webhooks%20Pro', WPWH()->helpers->translate('Go Pro', 'plugin-page') );

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
		if( WPWH()->helpers->is_page( $this->page_name ) && is_admin() ) {
			wp_enqueue_style( 'wpwh-admin-styles', WPWH_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles.min.css', array(), WPWH_VERSION, 'all' );
			wp_enqueue_script( 'wpwh-admin-scripts', WPWH_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts.min.js', array( 'jquery' ), WPWH_VERSION, true );
			wp_localize_script( 'wpwh-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
			));
		}
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
        $webhook_current_url    = isset( $_REQUEST['current_url'] ) ? sanitize_text_field( $_REQUEST['current_url'] ) : '';
        $webhook_group          = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback       = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks               = WPWH()->webhook->get_hooks( 'trigger', $webhook_group );
		$response               = array( 'success' => false );
		$url_parts              = parse_url( $webhook_current_url );
		parse_str($url_parts['query'], $query_params);
		$clean_url              = strtok( $webhook_current_url, '?' );
        $new_webhook            = strtotime( date( 'Y-n-d H:i:s' ) );

        if( ! isset( $webhooks[ $new_webhook ] ) ){
            WPWH()->webhook->create( $new_webhook, 'trigger', array( 'group' => $webhook_group, 'webhook_url' => $webhook_url ) );

	        $response['success']            = true;
	        $response['webhook']            = $new_webhook;
	        $response['webhook_group']      = $webhook_group;
	        $response['webhook_url']        = $webhook_url;
	        $response['webhook_callback']   = $webhook_callback;
	        $response['delete_url']         = WPWH()->helpers->built_url( $clean_url, array_merge( $query_params, array( 'wpwh_delete' => $new_webhook, ) ) );
        }


        echo json_encode( $response );
		die();
    }

    /*
     * Remove the trigger via ajax
     */
	public function ironikus_remove_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? intval( $_REQUEST['webhook'] ) : '';
        $webhook_group  = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$webhooks       = WPWH()->webhook->get_hooks( 'trigger', $webhook_group );
		$response       = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$check = WPWH()->webhook->unset_hooks( $webhook, 'trigger', $webhook_group );
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

        $webhook            = isset( $_REQUEST['webhook'] ) ? intval( $_REQUEST['webhook'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback   = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks           = WPWH()->webhook->get_hooks( 'trigger', $webhook_group );
        $response           = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			if( ! empty( $webhook_callback ) ){
				$data = apply_filters( 'ironikus_demo_' . $webhook_callback, array(), $webhook, $webhook_group, $webhooks[ $webhook ] );

				$response_data = WPWH()->webhook->post_to_webhook( $webhooks[ $webhook ]['webhook_url'], $data, array( 'blocking' => true ) );

				if ( ! empty( $response_data ) ) {
					$response['data']       = $response_data;
					$response['success']    = true;
				}
			}
		}

        echo json_encode( $response );
		die();
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
		add_submenu_page( 'options-general.php', WPWH()->helpers->translate( $this->page_title, 'admin-add-submenu-page-title' ), WPWH()->helpers->translate( $this->page_title, 'admin-add-submenu-page-site-title' ), WPWH()->settings->get_admin_cap( 'admin-add-submenu-page-item' ), $this->page_name, array( $this, 'render_admin_submenu_page' ) );
	}

	/**
	 * Render the admin submenu page
	 *
	 * You need the specified capability to edit it.
	 */
	public function render_admin_submenu_page(){
		if( ! current_user_can( WPWH()->settings->get_admin_cap('admin-submenu-page') ) ){
			wp_die( WPWH()->helpers->translate( WPWH()->settings->get_default_string( 'sufficient-permissions' ), 'admin-submenu-page-sufficient-permissions' ) );
		}

		include( WPWH_PLUGIN_DIR . 'core/includes/partials/wpwh-page-display.php' );

	}

	/**
	 * Register all of our default tabs to our plugin page
	 *
	 * @param $tabs - The previous tabs
	 *
	 * @return array - Return the array of all available tabs
	 */
	public function add_main_settings_tabs( $tabs ){

		$tabs['home']           = WPWH()->helpers->translate( 'Home', 'admin-menu' );
		$tabs['send-data']      = WPWH()->helpers->translate( 'Send Data', 'admin-menu' );
		$tabs['recieve-data']   = WPWH()->helpers->translate( 'Recieve Data', 'admin-menu' );
		$tabs['settings']       = WPWH()->helpers->translate( 'Settings', 'admin-menu' );
		$tabs['pro']       = WPWH()->helpers->translate( 'Pro', 'admin-menu' );

		return $tabs;

	}

	/**
	 * Load the content for our plugin page based on a specific tab
	 *
	 * @param $tab - The currently active tab
	 */
	public function add_main_settings_content( $tab ){

		switch($tab){
			case 'send-data':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/send-data.php' );
				break;
			case 'recieve-data':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/recieve-data.php' );
				break;
			case 'settings':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/settings.php' );
				break;
			case 'home':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/home.php' );
				break;
			case 'pro':
				include( WPWH_PLUGIN_DIR . 'core/includes/partials/tabs/pro.php' );
				break;
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

		$active_webhooks = WPWH()->settings->get_active_webhooks();

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

		$active_webhooks = WPWH()->settings->get_active_webhooks();

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

		//Post actions
		$actions[] = $this->action_create_post_content();

		//Testing actions
		$actions[] = $this->action_ironikus_test_content();

		return $actions;

	}

	/*
	 * Add the callback function for a defined action
	 */
	public function add_webhook_actions( $action, $webhook, $api_key ){

		$active_webhooks = WPWH()->settings->get_active_webhooks();
		$default_return = array(
            'success' => false
        );

		if( empty( $active_webhooks ) || empty( $active_webhooks['actions'] ) ){
			$default_return['msg'] = WPWH()->helpers->translate("You currently don't have any actions available.", 'action-add-webhook-actions' );

			WPWH()->webhook->echo_response_data( $default_return );
			die();
        }

		$available_triggers = $active_webhooks['actions'];

		switch( $action ){
			case 'create_user':
			    if( isset( $available_triggers['create_user'] ) ){
				    $this->action_create_user();
                }
				break;
			case 'create_post':
				if( isset( $available_triggers['create_post'] ) ){
					$this->action_create_post();
				}
				break;
			case 'ironikus_test':
				if( isset( $available_triggers['ironikus_test'] ) ){
					$this->action_ironikus_test();
				}
				break;
		}

		$default_return['data'] = $action;
		$default_return['msg'] = WPWH()->helpers->translate("It looks like your current action is deactivated or it does not have any action function.", 'action-add-webhook-actions' );

		WPWH()->webhook->echo_response_data( $default_return );
		die();
	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_user_content(){

		$parameter = array(
			'user_email'        => array( 'required' => true, 'short_description' => WPWH()->helpers->translate( 'This field is required. Include the email for the user.', 'action-create-user-content' ) ),
			'first_name'        => array( 'short_description' => WPWH()->helpers->translate( 'The first name of the user.', 'action-create-user-content' ) ),
			'last_name'         => array( 'short_description' => WPWH()->helpers->translate( 'The last name of the user.', 'action-create-user-content' ) ),
			'nickname'          => array( 'short_description' => WPWH()->helpers->translate( 'The nickname. Please note that the nickname will be sanitized by WordPress automatically.', 'action-create-user-content' ) ),
			'user_login'        => array( 'short_description' => WPWH()->helpers->translate( 'A string with which the user can log in to your site.', 'action-create-user-content' ) ),
			'display_name'      => array( 'short_description' => WPWH()->helpers->translate( 'The name that will be seen on the frontend of your site.', 'action-create-user-content' ) ),
			'user_nicename'     => array( 'short_description' => WPWH()->helpers->translate( 'A URL-friendly name. Default is user\' username.', 'action-create-user-content' ) ),
			'description'       => array( 'short_description' => WPWH()->helpers->translate( 'A description for the user that will be available on the profile page.', 'action-create-user-content' ) ),
			'rich_editing'      => array( 'short_description' => WPWH()->helpers->translate( 'Wether the user should be able to use the Rich editor. Set it to "yes" or "no". Default "no".', 'action-create-user-content' ) ),
			'user_registered'   => array( 'short_description' => WPWH()->helpers->translate( 'The date the user gets registered. Date structure: Y-m-d H:i:s', 'action-create-user-content' ) ),
			'user_url'          => array( 'short_description' => WPWH()->helpers->translate( 'Include a website url.', 'action-create-user-content' ) ),
			'role'              => array( 'short_description' => WPWH()->helpers->translate( 'The user role. If not set, default is subscriber.', 'action-create-user-content' ) ),
			'user_pass'         => array( 'short_description' => WPWH()->helpers->translate( 'The user password. If not defined, we generate a 32 character long password dynamically.', 'action-create-user-content' ) ),
			'send_email'        => array( 'short_description' => WPWH()->helpers->translate( 'Set this field to "yes" to send a email to the user with the data.', 'action-create-user-content' ) ),
			'do_action'         => array( 'short_description' => WPWH()->helpers->translate( 'Advanced: Register a custom action after Webhooks fires this webhook. More infos are in the description.', 'action-create-user-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWH()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-create-user-content' ) ),
			'data'        => array( 'short_description' => WPWH()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user data with the key "user_data". E.g. array( \'data\' => array(...) )', 'action-create-user-content' ) ),
			'msg'        => array( 'short_description' => WPWH()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-user-content' ) ),
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
		?>
		<p><?php echo WPWH()->helpers->translate( "To get started, you need to set an email address. All the other values are optional and just extend the creation of the user. We would still recommend to set the attribute <strong>user_login</strong>, since this will be the name a user can log in with.", "action-create-user-content" ); ?></p>
        <br><br>
		<p><?php echo WPWH()->helpers->translate( 'With the send_email parameter set to "yes", you can send a user notification mail to the defined user_email.', 'action-update-user-content' ); ?></p>
		<p><?php echo WPWH()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $user_data, $user_id, $user_meta', 'action-create-user-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'action'            => 'create_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWH()->helpers->translate( 'Create a new user via Webhooks.', 'action-create-user-content' ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_post_content(){

		$parameter = array(
			'post_author'           => array( 'short_description' => WPWH()->helpers->translate( '(int) The ID of the user who added the post. Default is the current user ID.', 'action-create-post-content' ) ),
			'post_date'             => array( 'short_description' => WPWH()->helpers->translate( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', 'action-create-post-content' ) ),
			'post_date_gmt'         => array( 'short_description' => WPWH()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', 'action-create-post-content' ) ),
			'post_content'          => array( 'short_description' => WPWH()->helpers->translate( '(string) The post content. Default empty.', 'action-create-post-content' ) ),
			'post_content_filtered' => array( 'short_description' => WPWH()->helpers->translate( '(string) The filtered post content. Default empty.', 'action-create-post-content' ) ),
			'post_title'            => array( 'short_description' => WPWH()->helpers->translate( '(string) The post title. Default empty.', 'action-create-post-content' ) ),
			'post_excerpt'          => array( 'short_description' => WPWH()->helpers->translate( '(string) The post excerpt. Default empty.', 'action-create-post-content' ) ),
			'post_status'           => array( 'short_description' => WPWH()->helpers->translate( '(string) The post status. Default \'draft\'.', 'action-create-post-content' ) ),
			'post_type'             => array( 'short_description' => WPWH()->helpers->translate( '(string) The post type. Default \'post\'.', 'action-create-post-content' ) ),
			'comment_status'        => array( 'short_description' => WPWH()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', 'action-create-post-content' ) ),
			'ping_status'           => array( 'short_description' => WPWH()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', 'action-create-post-content' ) ),
			'post_password'         => array( 'short_description' => WPWH()->helpers->translate( '(string) The password to access the post. Default empty.', 'action-create-post-content' ) ),
			'post_name'             => array( 'short_description' => WPWH()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', 'action-create-post-content' ) ),
			'to_ping'               => array( 'short_description' => WPWH()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', 'action-create-post-content' ) ),
			'pinged'                => array( 'short_description' => WPWH()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', 'action-create-post-content' ) ),
			'post_modified'         => array( 'short_description' => WPWH()->helpers->translate( '(string) The date when the post was last modified. Default is the current time.', 'action-create-post-content' ) ),
			'post_modified_gmt'     => array( 'short_description' => WPWH()->helpers->translate( '(string) The date when the post was last modified in the GMT timezone. Default is the current time.', 'action-create-post-content' ) ),
			'post_parent'           => array( 'short_description' => WPWH()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', 'action-create-post-content' ) ),
			'menu_order'            => array( 'short_description' => WPWH()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', 'action-create-post-content' ) ),
			'post_mime_type'        => array( 'short_description' => WPWH()->helpers->translate( '(string) The mime type of the post. Default empty.', 'action-create-post-content' ) ),
			'guid'                  => array( 'short_description' => WPWH()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', 'action-create-post-content' ) ),
			'post_category'         => array( 'short_description' => WPWH()->helpers->translate( '(string) A comma separated list of category names, slugs, or IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', 'action-create-post-content' ) ),
			'tags_input'            => array( 'short_description' => WPWH()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', 'action-create-post-content' ) ),
			'tax_input'             => array( 'short_description' => WPWH()->helpers->translate( '(string) A comma, semicolon and double point separated list of taxonomy terms keyed by their taxonomy name. Default empty. More details within the description.', 'action-create-post-content' ) ),
			'wp_error'              => array( 'short_description' => WPWH()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', 'action-create-post-content' ) ),
			'do_action'             => array( 'short_description' => WPWH()->helpers->translate( 'Advanced: Register a custom action after Webhooks fires this webhook. More infos are in the description.', 'action-create-post-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWH()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-create-post-content' ) ),
			'data'        => array( 'short_description' => WPWH()->helpers->translate( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', 'action-create-post-content' ) ),
			'msg'        => array( 'short_description' => WPWH()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-post-content' ) ),
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
		?>
        <p><?php echo WPWH()->helpers->translate( "This hook enables you to create posts with all of their settings, taxonomies and meta values. Custom post types are supported as well.", "action-create-post-content" ); ?></p>
        <br><br>
        <strong><?php echo WPWH()->helpers->translate( 'Create custom taxonomy values', 'action-create-post-content' ); ?></strong>
        <pre>term_name_1,value_1:value_2:value_3;term_name_2,value_1:value_2:value_3</pre>
		<?php echo WPWH()->helpers->translate( 'To separate the taxonomy key from the values, you can use a comma ",". In case you have multiple values per taxonomy, you can separate them via a double point ":". To separate multiple taxonomy settings from each other, easily separate them with a semicolon ";" (It is not necessary to set a semicolon at the end of the last one)', 'action-create-post-content' ); ?>
        <br><br>
        <strong><?php echo WPWH()->helpers->translate( 'Delete whole related taxonomy', 'action-create-post-content' ); ?></strong>
        <pre>ironikus-remove-all;term_name_1;term_name_2</pre>
		<?php echo WPWH()->helpers->translate( 'To delete all related taxonomy values of a single taxonomy, just add "ironikus-remove-all;" at the beginning. After that, you can define the specified taxonomy ids or slugs, separated with a semicolon ";".', 'action-create-post-content' ); ?>
        <br><br>
        <strong><?php echo WPWH()->helpers->translate( 'Delete single related taxonomy value', 'action-create-post-content' ); ?></strong>
        <pre>ironikus-append;term_name_1,value_1:value_2-ironikus-delete:value_3;term_name_2,value_1:value_2:value_3-ironikus-delete</pre>
		<?php echo WPWH()->helpers->translate( 'You can delete a single value by setting "ironikus-append" in the beginning, separated with a semicolon, followed by the taxonomy name and the values, in which the value that should be deleted contains a "-ironikus-delete" at the end. This will trigger the deletion of that specific value.', 'action-create-post-content' ); ?>
        <br><br>
        <strong><?php echo WPWH()->helpers->translate( 'Append existing taxonomies', 'action-create-post-content' ); ?></strong>
        <pre>ironikus-append;term_name_1,value_1:value_2:value_3;term_name_2,value_1:value_2:value_3</pre>
		<?php echo WPWH()->helpers->translate( 'With adding a "ironikus-append" at the beginning, you will append the set taxonomies by your new ones without deleting the old ones. (Affects only post updates).', 'action-create-post-content' ); ?>
        <br><br>
        <p><?php echo WPWH()->helpers->translate( 'With the wp_error parameter set to "true", it will send a wp_error object back in case something went wrong with the post.', 'action-update-post-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $post_data, $post_id, $meta_input', 'action-create-post-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'action'            => 'create_post',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWH()->helpers->translate( 'Insert/Create a post. You have all functionalities available from wp_insert_post', 'action-create-post-content' ),
			'description'       => $description
		);

	}

	/*
	 * The core logic to test a webhook
	 */
	public function action_ironikus_test_content(){

		$parameter = array(
			'test_var'       => array( 'required' => true, 'short_description' => WPWH()->helpers->translate( 'A test var. Include the following value to get a success message back: test-value123', 'action-ironikus-test-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWH()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-ironikus-test-content' ) ),
			'test_var'        => array( 'short_description' => WPWH()->helpers->translate( '(string) The variable that was set for the request.', 'action-ironikus-test-content' ) ),
			'msg'        => array( 'short_description' => WPWH()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-ironikus-test-content' ) ),
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
		?>
        <p><?php echo WPWH()->helpers->translate( 'This webhook is for testing purposes only. It does not manipulate any data within the system.', 'action-ironikus-test-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'action'            => 'ironikus_test',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWH()->helpers->translate( 'Test a webhooks functionality. (Advanced)', 'action-ironikus-test-content' ),
			'description'       => $description
		);

	}

	public function action_ironikus_test(){

		$response_body = WPWH()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
			'msg' => '',
			'test_var' => ''
		);

		$test_var = sanitize_title( WPWH()->helpers->validate_request_value( $response_body['content'], 'test_var' ) );

		if( $test_var == 'test-value123' ){
			$return_args['success'] = true;
			$return_args['msg'] = WPWH()->helpers->translate("Test value successfully filled.", 'action-test-success' );
        } else {
			$return_args['msg'] = WPWH()->helpers->translate("test_var was not filled.", 'action-test-success' );
        }

        $return_args['test_var'] = $test_var;

		WPWH()->webhook->echo_response_data( $return_args );
		die();

    }

	/**
	 * Create a user via a action call
     *
     * @param $update - Wether the user gets created or updated
	 */
	public function action_create_user(){

		$response_body = WPWH()->helpers->get_response_body();
		$return_args = array(
			'success' => false,
            'msg' => '',
            'data' => array(
	            'user_id' => 0,
	            'user_data' => array()
            )
		);

		$nickname           = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'nickname' ) );
		$user_login         = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_login' ) );
		$user_nicename      = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_nicename' ) );
		$description        = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'description' ) );
		$user_registered    = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_registered' ) );
		$user_url           = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_url' ) );
		$display_name       = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'display_name' ) );
		$user_email         = sanitize_email( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_email' ) );
		$first_name         = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'first_name' ) );
		$last_name          = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'last_name' ) );
		$role               = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'role' ) );
		$user_pass          = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'user_pass' ) );
		$do_action          = sanitize_email( WPWH()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

		$rich_editing     = ( WPWH()->helpers->validate_request_value( $response_body['content'], 'rich_editing' ) == 'yes' ) ? true : false;
		$send_email         = ( WPWH()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';

		if ( empty( $user_email ) ) {
			$return_args['msg'] = WPWH()->helpers->translate("An email is required to create a user.", 'action-create-user-success' );

			WPWH()->webhook->echo_response_data( $return_args );
			die();
		}

		$user_data = array(
			'user_email' => $user_email
		);

		$dynamic_user_login = apply_filters( 'wpwh/run/create_action_user_login', false );
		if ( empty( $user_login ) && $dynamic_user_login ) {
			$user_login = WPWH()->helpers->create_random_unique_username( $user_email, 'user_' );
		}

		//Define on new user
		if( ! empty( $role ) ){
			$user_data['role'] = 'subscriber';
		}

		//Auto generate on new user
		if( ! empty( $user_pass ) ){
			$user_data['user_pass'] = wp_generate_password( 32, true, false );
		}

		if( ! empty( $username ) ){
			$user_data['nickname'] = $nickname;
		}

		if( ! empty( $user_login ) ){
			$user_data['user_login'] = $user_login;
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

            $return_args['msg'] = WPWH()->helpers->translate("User successfully created.", 'action-create-user-success' );

			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['user_data'] = $user_data;
			$return_args['success'] = true;

			if( apply_filters( 'wpwh/run/create_action_user_email_notification', true ) && $send_email == 'yes' ){
				wp_new_user_notification( $user_id, null, 'both' );
			}
		} else {
			$return_args['msg'] = WPWH()->helpers->translate("An error occured while creating the user. Please check the response for more details.", 'action-create-user-success' );
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['user_data'] = $user_data;
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $user_data, $user_id );
		}

		WPWH()->webhook->echo_response_data( $return_args );
		die();
	}

	/**
	 * Create a post via an action call
     *
     * @param $update - Wether to create or to update the post
	 */
	public function action_create_post(){

		$response_body = WPWH()->helpers->get_response_body();
		$return_args = array(
			'success'   => false,
			'msg'       => '',
            'data'      => array(
	            'post_id' => null,
	            'post_data' => null
            )
		);

		$post_author            = intval( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_author' ) );
		$post_date              = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_date' ) );
		$post_date_gmt          = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_date_gmt' ) );
		$post_content           = WPWH()->helpers->validate_request_value( $response_body['content'], 'post_content' );
		$post_content_filtered  = WPWH()->helpers->validate_request_value( $response_body['content'], 'post_content_filtered' );
		$post_title             = WPWH()->helpers->validate_request_value( $response_body['content'], 'post_title' );
		$post_excerpt           = WPWH()->helpers->validate_request_value( $response_body['content'], 'post_excerpt' );
		$post_status            = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_status' ) );
		$post_type              = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_type' ) );
		$comment_status         = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
		$ping_status            = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
		$post_password          = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_password' ) );
		$post_name              = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_name' ) );
		$to_ping                = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
		$pinged                 = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
		$post_modified          = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_modified' ) );
		$post_modified_gmt      = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_modified_gmt' ) );
		$post_parent            = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_parent' ) );
		$menu_order             = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
		$post_mime_type         = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'post_mime_type' ) );
		$guid                   = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
		$post_category          = WPWH()->helpers->validate_request_value( $response_body['content'], 'post_category' );
		$tags_input             = WPWH()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
		$tax_input              = WPWH()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
		$wp_error               = ( WPWH()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )     ? true : false;
		$do_action              = sanitize_text_field( WPWH()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

		$post_data = array();

		if( ! empty( $post_author ) ){
			$post_data['post_author'] = $post_author;
		}

		if( ! empty( $post_date ) ){
			$post_data['post_date'] = $post_date;
		}

		if( ! empty( $post_date_gmt ) ){
			$post_data['post_date_gmt'] = $post_date_gmt;
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

				foreach( $tax_data['create'] as $tax_key => $tax_values ){

				    if( $remove_all ){
					    wp_set_object_terms( $post_id, array(), $tax_key, $tax_append );
                    } else {
					    wp_set_object_terms( $post_id, $tax_values, $tax_key, $tax_append );
                    }

				}

			}
			$post_data['tax_input'] = $tax_input;

            $return_args['msg'] = WPWH()->helpers->translate("Post successfully created", 'action-create-post-success' );

			$return_args['data']['post_data'] = $post_data;
			$return_args['data']['post_id'] = $post_id;
			$return_args['success'] = true;

		} else {

		    if( is_wp_error( $post_id ) && $wp_error ){

			    $return_args['data']['post_data'] = $post_data;
			    $return_args['data']['post_id'] = $post_id;
			    $return_args['msg'] = WPWH()->helpers->translate("WP Error", 'action-create-post-success' );
            } else {
			    $return_args['msg'] = WPWH()->helpers->translate("Error creating post.", 'action-create-post-success' );
            }
		}

		if( ! empty( $do_action ) ){
			do_action( $do_action, $post_data, $post_id );
		}

		WPWH()->webhook->echo_response_data( $return_args );
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
		$triggers[] = $this->trigger_post_create();
		$triggers[] = $this->trigger_post_update();
		$triggers[] = $this->trigger_post_delete();

		return $triggers;
	}

	/*
	 * Add the specified webhook triggers logic.
	 * We also add the demo functionality here
	 */
	public function add_webhook_triggers(){

		$active_webhooks = WPWH()->settings->get_active_webhooks();
		$available_triggers = $active_webhooks['triggers'];

		if( isset( $available_triggers['create_user'] ) ){
			add_action( 'user_register', array( $this, 'ironikus_trigger_user_register' ), 10, 1 );
			add_filter( 'ironikus_demo_test_user_create', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['login_user'] ) ){
			add_action( 'wp_login', array( $this, 'ironikus_trigger_user_login' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_login', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['update_user'] ) ){
			add_action( 'profile_update', array( $this, 'ironikus_trigger_user_update' ), 10, 2 );
			add_filter( 'ironikus_demo_test_user_update', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
        }

		if( isset( $available_triggers['post_create'] ) ){
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_create' ), 10, 3 );
        }

		if( isset( $available_triggers['post_update'] ) ){
			add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_update' ), 10, 3 );
        }

		if( isset( $available_triggers['post_create'] ) || isset( $available_triggers['post_update'] ) ){
			add_filter( 'ironikus_demo_test_post_create', array( $this, 'ironikus_send_demo_post_create' ), 10, 3 );
        }

		if( isset( $available_triggers['post_delete'] ) ){
			add_action( 'after_delete_post', array( $this, 'ironikus_trigger_post_delete' ), 10, 3 );
			add_filter( 'ironikus_demo_test_post_delete', array( $this, 'ironikus_send_demo_post_delete' ), 10, 3 );
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
			'user_object' => array( 'short_description' => WPWH()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-create-user-content' ) ),
			'user_meta'   => array( 'short_description' => WPWH()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-create-user-content" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve a full response of the user object, as well as the user meta, so everything you need will be there.', 'trigger-create-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-create-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the webhook response on a demo request, just open your browser console and you will see the object.', 'trigger-create-user-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'create_user',
			'name'              => WPWH()->helpers->translate( 'Send Data On Register', 'trigger-create-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', '' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires as soon as a user registered.', 'trigger-create-user-content' ),
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
	public function ironikus_trigger_user_register( $user_id ){
		$webhooks               = WPWH()->webhook->get_hooks( 'trigger', 'create_user' );
		$user_data              = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta'] = get_user_meta( $user_id );

		foreach( $webhooks as $webhook ){
			$response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $user_data );
        }

        do_action( 'wpwh/webhooks/trigger_user_register', $user_id, $user_data, $response_data );
    }

    /**
     * LOGIN USER
     */

	/*
	 * Register the user login trigger as an element
	 */
	public function trigger_login_user_content(){

		$parameter = array(
			'user_object'   => array( 'short_description' => WPWH()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-login-user-content' ) ),
			'user_meta'     => array( 'short_description' => WPWH()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-login-user-content' ) ),
			'user_login'    => array( 'short_description' => WPWH()->helpers->translate( 'The user login is included as well. This is the value the user used to make the login. It is also located on the first layoer of the array.', 'trigger-login-user-content' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-login-user-content" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve a full response of the user object, as well as the user meta, so everything you need will be there.', 'trigger-login-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-login-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the webhook response on a demo request, just open your browser console and you will see the object.', 'trigger-login-user-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'login_user',
			'name'              => WPWH()->helpers->translate( 'Send Data On Login', 'trigger-login-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', 'login_user' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires as soon as a user triggers the login.', 'trigger-login-user-content' ),
			'description'       => $description,
			'callback'          => 'test_user_login'
		);

	}

	/*
	 * Register the user login trigger logic
	 */
	public function ironikus_trigger_user_login( $user_login, $user_id ){
		$webhooks                = WPWH()->webhook->get_hooks( 'trigger', 'login_user' );
		$user_data               = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta']  = get_user_meta( $user_id );
		$user_data['user_login'] = get_user_meta( $user_login );

		foreach( $webhooks as $webhook ){
			$response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $user_data );
		}

		do_action( 'wpwh/webhooks/trigger_user_login', $user_id, $user_data );
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
			'user_object'   => array( 'short_description' => WPWH()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-update-user-content' ) ),
			'user_meta'     => array( 'short_description' => WPWH()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-update-user-content' ) ),
			'user_old_data' => array( 'short_description' => WPWH()->helpers->translate( 'This is the object with the previous user object as an array. You can recheck your data on it as well.', 'trigger-update-user-content' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-update-user-content" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve a full response of the user object, as well as the user meta, so everything you need will be there.', 'trigger-update-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-create-user-content' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the webhook response on a demo request, just open your browser console and you will see the object.', 'trigger-update-user-content' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'update_user',
			'name'              => WPWH()->helpers->translate( 'Send Data On User Update', 'trigger-update-user-content' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_user_create( array(), '', 'update_user' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires as soon as a user updates his profile.', 'trigger-update-user-content' ),
			'description'       => $description,
			'callback'          => 'test_user_update'
		);

	}

	/*
	 * Register the user update trigger logic
	 */
	public function ironikus_trigger_user_update( $user_id, $old_data ){
		$webhooks                   = WPWH()->webhook->get_hooks( 'trigger', 'update_user' );
		$user_data                  = (array) get_user_by( 'id', $user_id );
		$user_data['user_meta']     = get_user_meta( $user_id );
		$user_data['user_old_data'] = $old_data;

		foreach( $webhooks as $webhook ){
			$response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $user_data );
		}

		do_action( 'wpwh/webhooks/trigger_user_update', $user_id, $user_data );
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

		$parameter = array(
			'post_id'   => array( 'short_description' => WPWH()->helpers->translate( 'The post id of the created post.', 'trigger-post-create' ) ),
			'post'      => array( 'short_description' => WPWH()->helpers->translate( 'The whole post object with all of its values', 'trigger-post-create' ) ),
			'post_meta' => array( 'short_description' => WPWH()->helpers->translate( 'An array of the whole post meta data.', 'trigger-post-create' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-post-create" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve a full response of the user post id, the full post object, as well as the post meta, so everything you need will be there.', 'trigger-post-create' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-post-create' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the Webhooks response on a demo request, just open your browser console and you will see the object.', 'trigger-post-create' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'post_create',
			'name'              => WPWH()->helpers->translate( 'Send Data On New Post', 'trigger-post-create' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_post_create( array(), '', '' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires after a new post was created.', 'trigger-post-create' ),
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

		$parameter = array(
			'post_id'   => array( 'short_description' => WPWH()->helpers->translate( 'The post id of the updated post.', 'trigger-post-update' ) ),
			'post'      => array( 'short_description' => WPWH()->helpers->translate( 'The whole post object with all of its values', 'trigger-post-update' ) ),
			'post_meta' => array( 'short_description' => WPWH()->helpers->translate( 'An array of the whole post meta data.', 'trigger-post-update' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-post-update" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve a full response of the user post id, the full post object, as well as the post meta, so everything you need will be there.', 'trigger-post-update' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-post-update' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the Webhooks response on a demo request, just open your browser console and you will see the object.', 'trigger-post-update' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'post_update',
			'name'              => WPWH()->helpers->translate( 'Send Data On Post Update', 'trigger-post-update' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_post_create( array(), '', '' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires after an existing post is updated.', 'trigger-post-update' ),
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

		$parameter = array(
			'post_id' => array( 'short_description' => WPWH()->helpers->translate( 'The post id of the deleted post.', 'trigger-post-delete' ) ),
		);

		ob_start();
		?>
        <p><?php echo WPWH()->helpers->translate( "Please copy your Webhooks webhook URL into the provided input field. After that you can test your data via the Send demo button.", "trigger-post-delete" ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You will recieve the deleted post id with a successful deletion.', 'trigger-post-delete' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'You can also filter the demo request by using a custom WordPress filter.', 'trigger-post-delete' ); ?></p>
        <p><?php echo WPWH()->helpers->translate( 'To check the Webhooks response on a demo request, just open your browser console and you will see the object.', 'trigger-post-delete' ); ?></p>
		<?php
		$description = ob_get_clean();

		return array(
			'trigger'           => 'post_delete',
			'name'              => WPWH()->helpers->translate( 'Send Data On Post Deletion', 'trigger-post-delete' ),
			'parameter'         => $parameter,
			'returns_code'      => WPWH()->helpers->display_var( $this->ironikus_send_demo_post_delete( array(), '', '' ) ),
			'short_description' => WPWH()->helpers->translate( 'This webhook fires after a post was deleted.', 'trigger-post-delete' ),
			'description'       => $description,
			'callback'          => 'test_post_delete'
		);

	}

	/*
	 * Register the register post trigger logic
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_create( $post_id, $post, $update ){

	    if( ! $update ){
		    $webhooks = WPWH()->webhook->get_hooks( 'trigger', 'post_create' );
		    $data_array = array(
			    'post_id'   => $post_id,
			    'post'      => $post,
			    'post_meta' => get_post_meta( $post_id ),
		    );

		    foreach( $webhooks as $webhook ){
			    $response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $data_array );
		    }

		    do_action( 'wpwh/webhooks/trigger_post_create', $post_id, $post );
        }
	}

	/*
	 * Register the register post trigger logic
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_update( $post_id, $post, $update ){

	    if( $update ){
		    $webhooks = WPWH()->webhook->get_hooks( 'trigger', 'post_update' );
		    $data_array = array(
			    'post_id'   => $post_id,
			    'post'      => $post,
			    'post_meta' => get_post_meta( $post_id ),
		    );

		    foreach( $webhooks as $webhook ){
			    $response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $data_array );
		    }

		    do_action( 'wpwh/webhooks/trigger_post_update', $post_id, $post, $response_data );
        }
	}

	/*
	 * Register the post delete trigger logic
	 *
	 * @since 1.2
	 */
	public function ironikus_trigger_post_delete( $post_id ){

        $webhooks = WPWH()->webhook->get_hooks( 'trigger', 'post_delete' );
        $data_array = array(
                'post_id' => $post_id
        );

        foreach( $webhooks as $webhook ){
            $response_data = WPWH()->webhook->post_to_webhook( $webhook['webhook_url'], $data_array );
        }

        do_action( 'wpwh/webhooks/trigger_post_delete', $post_id, $response_data );
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
        );

		return $data;
	}

	/*
	 * Register the demo post delete trigger callback
	 *
	 * @since 1.2
	 */
	public function ironikus_send_demo_post_delete( $data, $webhook, $webhook_group ) {

		return array( 'post_id' => 12345 ); // the deleted demo post id
	}

}
