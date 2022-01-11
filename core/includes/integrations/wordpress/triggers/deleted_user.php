<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_deleted_user' ) ) :

	/**
	 * Load the deleted_user trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_deleted_user {

        /**
         * Preserver certain values
         *
         * @var array
         * @since 2.0.5
         */
        private $pre_action_values = array();

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wpmu_delete_user',
                    'callback' => array( $this, 'ironikus_prepare_user_delete' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'delete_user',
                    'callback' => array( $this, 'ironikus_prepare_user_delete' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'deleted_user',
                    'callback' => array( $this, 'ironikus_trigger_user_deleted' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => true,
                ),
            );
		}

        public function get_details(){

            $translation_ident = "trigger-deleted-user-description";

            $parameter = array(
                'user_id'   	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the deleted user', 'trigger-deleted-user-content' ) ),
                'reassign'     	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'ID of the user to reassign posts and links to. Default null, for no reassignment.', 'trigger-deleted-user-content' ) ),
                'user'     		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The full user data from the WP_User object.', 'trigger-deleted-user-content' ) ),
                'user_meta'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'All of the assigned user meta of the given user.', 'trigger-deleted-user-content' ) ),
            );
    
            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
            }

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'User deleted',
				'webhook_slug' => 'deleted_user',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'deleted_user',
                        'url' => 'https://developer.wordpress.org/reference/hooks/deleted_user/',
                     ),
				)
			) );
    
            return array(
                'trigger'           => 'deleted_user',
                'name'              => WPWHPRO()->helpers->translate( 'User deleted', 'trigger-deleted-user-content' ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a user was deleted', 'trigger-deleted-user-content' ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user was deleted.', 'trigger-deleted-user-content' ),
                'description'       => $description,
                'callback'          => 'test_deleted_user',
                'integration'       => 'wordpress',
            );
    
        }

        /*
        * Preserve the user data before deletion
        *
        * @since 3.0.2
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

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                }

            }

            do_action( 'wpwhpro/webhooks/trigger_user_deleted', $user_id, $user_data, $response_data );
        }

        public function get_demo( $options = array() ){
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

    }

endif; // End if class_exists check.