<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_delete_comment' ) ) :

	/**
	 * Load the delete_comment trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_delete_comment {

        public function is_active(){

            //Backwards compatibility for the "Comments" integration
            if( class_exists( 'WP_Webhooks_Comments' ) ){
                return false;
            }

            return true;
        }

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'deleted_comment',
                    'callback' => array( $this, 'ironikus_trigger_delete_comment' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

            $translation_ident = "trigger-delete_comment-description";

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
				'comment_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The comment id of the currently deleted comment.', $translation_ident ) ),
				'comment_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data object of the comment.', $translation_ident ) ),
				'current_post_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the post the comment was deleted on.', $translation_ident ) ),
				'current_post_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data of the current post.', $translation_ident ) ),
				'user_id'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the user who posted the comment (In case it is given).', $translation_ident ) ),
				'user_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data of the user of the comment (In case a user is given).', $translation_ident ) ),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Comment deleted',
				'webhook_slug' => 'delete_comment',
				'post_delay' => false,
                'tipps' => array(
                    sprintf( WPWHPRO()->helpers->translate( 'By default, we don\'t send the user password within the request. To active it, please use the following WordPress filter: wpwhpro/webhooks/trigger_delete_comment_restrict_user_values (More details within our docs at <a title="Go to our plugin documentation" target="_blank" href="%s">wp-webhooks.com/docs</a>', $translation_ident ), 'https://wp-webhooks.com/docs/?utm_source=wp-webhooks-comments&utm_medium=send-data-documentation&utm_campaign=WP%20Webhooks%20Pro'),
                ),
				'trigger_hooks' => array(
					array( 
                        'hook' => 'deleted_comment',
                        'url' => 'https://developer.wordpress.org/reference/hooks/deleted_comment/',
                     ),
				)
			) );

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_delete_comment_trigger_on_post_type' => array(
						'id'          => 'wpwhpro_delete_comment_trigger_on_post_type',
						'type'        => 'select',
						'multiple'    => true,
						'choices'      => $validated_post_types,
						'label'       => WPWHPRO()->helpers->translate('Trigger on selected post types', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident)
					),
				)
			);

            return array(
                'trigger'           => 'delete_comment',
                'name'              => WPWHPRO()->helpers->translate( 'Comment deleted', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a comment was deleted', $translation_ident ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a comment was deleted.', $translation_ident ),
                'description'       => $description,
                'callback'          => 'test_delete_comment',
                'integration'       => 'wordpress',
                'premium'           => false,
            );

        }

        public function ironikus_trigger_delete_comment( $comment_id, $comment ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'delete_comment' );
			$data_array = array(
				'comment_id'   => $comment_id,
				'comment_data'  => $comment,
				'current_post_id' => 0,
				'current_post_data' => array(),
				'current_post_data_meta' => array(),
				'user_id' => 0,
				'user_data' => array(),
				'user_data_meta' => array()
			);
			$response_data = array();

			if( isset( $comment->comment_post_ID ) ){
				$post_id = $comment->comment_post_ID;
				if( ! empty( $post_id ) ){
					$data_array['current_post_id'] = $post_id;
					$data_array['current_post_data'] = get_post( $post_id );
					$data_array['current_post_data_meta'] = get_post_meta( $post_id );
				}
			}

			if( isset( $comment->comment_author_email ) && is_email( $comment->comment_author_email ) ){
				$user = get_user_by( 'email', sanitize_email( $comment->comment_author_email ) );
				if( ! empty( $user ) && ! is_wp_error( $user ) ){
					$data_array['user_id'] = $user->data->ID;
					$data_array['user_data'] = $user;

					//Restrict password
					$restrict = apply_filters( 'wpwhpro/webhooks/trigger_delete_comment_restrict_user_values', array( 'user_pass' ) );
					if( is_array( $restrict ) && ! empty( $restrict ) ){

						foreach( $restrict as $data_key ){
							if( ! empty( $data_array['user_data'] ) && isset( $data_array['user_data']->data ) && isset( $data_array['user_data']->data->{$data_key} )){
								unset( $data_array['user_data']->data->{$data_key} );
							}
						}
						
					}

				}
			}

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_delete_comment_trigger_on_post_type' && ! empty( $settings_data ) ){
							if( ! empty( $data_array['current_post_data'] ) ){
								if( ! in_array( $data_array['current_post_data']->post_type, $settings_data ) ){
									$is_valid = false;
								}
							}
						}
					}
				}

				if( $is_valid ){
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_delete_comment', $comment_id, $comment, $data_array, $response_data );

		}

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array (
				'comment_id' => 9,
				'comment_data' => 
				array (
				  'comment_ID' => '9',
				  'comment_post_ID' => '375',
				  'comment_author' => 'admin',
				  'comment_author_email' => 'admin@xxx.dev',
				  'comment_author_url' => '',
				  'comment_author_IP' => '127.0.0.1',
				  'comment_date' => '2019-08-14 14:08:53',
				  'comment_date_gmt' => '2019-08-14 14:08:53',
				  'comment_content' => 'My test',
				  'comment_karma' => '0',
				  'comment_approved' => '1',
				  'comment_agent' => 'Mozilla/5.0 xxx',
				  'comment_type' => '',
				  'comment_parent' => '0',
				  'user_id' => '1',
				),
				'comment_meta' => 
				array (
				  'demo_key_1' => array( 375 ),
				  'demo_key_2' => array( 'test' ),
				),
				'current_post_id' => '375',
				'current_post_data' => 
				array (
				  'ID' => 375,
				  'post_author' => '1',
				  'post_date' => '2019-08-11 15:03:31',
				  'post_date_gmt' => '2019-08-11 15:03:31',
				  'post_content' => '',
				  'post_title' => 'Test Custom Comment 2',
				  'post_excerpt' => '',
				  'post_status' => 'publish',
				  'comment_status' => 'open',
				  'ping_status' => 'open',
				  'post_password' => '',
				  'post_name' => 'test-custom-comment-2',
				  'to_ping' => '',
				  'pinged' => '',
				  'post_modified' => '2019-08-14 11:53:24',
				  'post_modified_gmt' => '2019-08-14 11:53:24',
				  'post_content_filtered' => '',
				  'post_parent' => 0,
				  'guid' => 'https://xxx.dev/?p=375',
				  'menu_order' => 0,
				  'post_type' => 'post',
				  'post_mime_type' => '',
				  'comment_count' => '3',
				  'filter' => 'raw',
				),
				'current_post_data_meta' => 
				array (
				  'demo_key_1' => array( 375 ),
				  'demo_key_2' => array( 'test' ),
				),
				'user_id' => '1',
				'user_data' => 
				array (
				  'data' => 
				  array (
					'ID' => '1',
					'user_login' => 'admin',
					'user_nicename' => 'admin',
					'user_email' => 'admin@xxx.dev',
					'user_url' => '',
					'user_registered' => '2017-07-27 23:58:11',
					'user_activation_key' => '',
					'user_status' => '0',
					'display_name' => 'admin',
					'spam' => '0',
					'deleted' => '0',
				  ),
				  'ID' => 1,
				  'caps' => 
				  array (
					'administrator' => true,
				  ),
				  'cap_key' => 'XXX_capabilities',
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
				  ),
				  'filter' => NULL,
				),
				'user_data_meta' => 
				array (
				  'demo_key_1' => array( 375 ),
				  'demo_key_2' => array( 'test' ),
				),
            );

            return $data;
        }

    }

endif; // End if class_exists check.