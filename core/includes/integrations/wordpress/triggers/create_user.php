<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_create_user' ) ) :

	/**
	 * Load the create_user trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_create_user {

		
		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'user_register',
                    'callback' => array( $this, 'ironikus_trigger_user_register' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                )
            );

		}

        public function get_details(){

            $translation_ident = "trigger-create-user-description";

            $parameter = array(
                'user_object' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-create-user-content' ) ),
                'user_meta'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) ),
            );

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
            }

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'New user',
				'webhook_slug' => 'create_user',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'user_register',
                        'url' => 'https://developer.wordpress.org/reference/hooks/user_register/',
                    ),
				)
			) );

            return array(
                'trigger'           => 'create_user',
                'name'              => WPWHPRO()->helpers->translate( 'User created', 'trigger-create-user-content' ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a user was created', 'trigger-create-user-content' ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user registers.', 'trigger-create-user-content' ),
                'description'       => $description,
                'callback'          => 'test_user_create',
                'integration'       => 'wordpress',
            );

        }

        public function ironikus_trigger_user_register( $user_id ){
            $webhooks               = WPWHPRO()->webhook->get_hooks( 'trigger', 'create_user' );
            $user_data              = (array) get_user_by( 'id', $user_id );

            //Make sure we only return the clean, private site variable
            if( ! empty( $user_data ) ){
                foreach( $user_data as $udk => $udv ){
                    if( strpos( $udk, 'site_id' ) !== FALSE ){
                        $user_data['site_id'] = $udv;
                        unset( $user_data[ $udk ] );
                    }
                }
            }

            $user_data['user_meta'] = get_user_meta( $user_id );
            $response_data = array();

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $user_data['acf_data'] = get_fields( 'user_' . $user_id );
            }

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                }

            }

            do_action( 'wpwhpro/webhooks/trigger_user_register', $user_id, $user_data, $response_data );
        }

        /*
        * Register the demo data response
        *
        * @param $options - The available options
        *
        * @return array - The demo data
        */
        public function get_demo( $options = array() ){

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