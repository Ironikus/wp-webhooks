<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_post_create' ) ) :

	/**
	 * Load the post_create trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_post_create {

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
                    'hook' => 'add_attachment',
                    'callback' => array( $this, 'ironikus_trigger_post_create_attachment_init' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'wp_insert_post',
                    'callback' => array( $this, 'ironikus_trigger_post_create' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => true,
                ),
            );

		}

        /*
        * Register the create post trigger as an element
        *
        * @since 1.2
        */
        public function get_details(){

            $translation_ident = "trigger-create-post-description";

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
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on the creation of a post, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On New Post</strong> (post_create) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On New Post</strong> (post_create)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you can receive data on the URL once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>add_attachment</strong> hook and the <strong>wp_insert_post</strong> hook:", $translation_ident ); ?> 
<br>
<strong><?php echo WPWHPRO()->helpers->translate( "Add attachment", $translation_ident ); ?></strong>: <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/hooks/add_attachment/">https://developer.wordpress.org/reference/hooks/add_attachment/</a>
<br>
<strong><?php echo WPWHPRO()->helpers->translate( "Insert post", $translation_ident ); ?></strong>: <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/hooks/wp_insert_post/">https://developer.wordpress.org/reference/hooks/wp_insert_post/</a>
<br><br>
<?php echo WPWHPRO()->helpers->translate( "This webhook fires on two different hooks since attachments (which are technically posts as well), use a custom logic.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here are the calls within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'add_attachment', array( $this, 'ironikus_trigger_post_create_attachment_init' ), 10, 1 );
add_action( 'wp_insert_post', array( $this, 'ironikus_trigger_post_create' ), 10, 3 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (wp_insert_post/add_attachment) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the demo webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
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
                'name'              => WPWHPRO()->helpers->translate( 'Post created', 'trigger-post-create' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a new post was created.', 'trigger-post-create' ),
                'description'       => $description,
                'callback'          => 'test_post_create',
                'integration'       => 'wordpress',
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

            $this->ironikus_trigger_post_create( $post_id, $post, false );
        }

        /*
        * Register the register post trigger logic
        *
        * The webhook needs to be cancelled on a webhook level since the post delay
        * requires a check on the update hook as well.
        *
        * @since 1.2
        */
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
                    $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                    if( $webhook_url_name !== null ){
                        $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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
        public function get_demo( $options = array() ) {

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

    }

endif; // End if class_exists check.