<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_post_delete' ) ) :

	/**
	 * Load the post_delete trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_post_delete {

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
                    'hook' => 'before_delete_post',
                    'callback' => array( $this, 'ironikus_prepare_post_delete' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'delete_attachment',
                    'callback' => array( $this, 'ironikus_prepare_post_delete' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'delete_post',
                    'callback' => array( $this, 'ironikus_trigger_post_delete' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

            $translation_ident = "trigger-delete-post-description";

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

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Post deleted',
				'webhook_slug' => 'post_delete',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'delete_post',
                        'url' => 'https://developer.wordpress.org/reference/hooks/delete_post/',
                     ),
				)
			) );

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
                'name'              => WPWHPRO()->helpers->translate( 'Post deleted', 'trigger-post-delete' ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a post was deleted', 'trigger-post-delete' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a post was deleted.', 'trigger-post-delete' ),
                'description'       => $description,
                'callback'          => 'test_post_delete',
                'integration'       => 'wordpress',
            );

        }

        /*
        * Preserve the post_before on update_post
        *
        * @since 3.0.2
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
                    $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                    if( $webhook_url_name !== null ){
                        $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
                    } else {
                        $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
                    }
                }
            }

            do_action( 'wpwhpro/webhooks/trigger_post_delete', $post_id, $response_data );
        }

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.2
        */
        public function get_demo( $options = array() ) {

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

    }

endif; // End if class_exists check.