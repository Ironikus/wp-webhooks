<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_post_trash' ) ) :

	/**
	 * Load the post_trash trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_post_trash {

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
                    'hook' => 'trashed_post',
                    'callback' => array( $this, 'ironikus_trigger_post_trash' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                ),
            );

		}

        /*
        * Register the post trash trigger as an element
        *
        * @since 3.0.4
        */
        public function get_details(){

            $translation_ident = "trigger-trash-post-description";

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
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on trashing a post, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Post Trash</strong> (post_trash) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Post Trash</strong> (post_trash)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>trashed_post</strong> hook:", $translation_ident ); ?> 
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/hooks/trashed_post/">https://developer.wordpress.org/reference/hooks/trashed_post/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'trashed_post', array( $this, 'ironikus_trigger_post_trash' ), 10, 1 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (trashed_post) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "<strong>Important</strong>: Please note that in case this trigger does not fire, either your post type does not support the trash status, or automatic cleaning time is reached.", $translation_ident ); ?></li>
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
                'name'              => WPWHPRO()->helpers->translate( 'Post trashed', 'trigger-post-trash' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a post was trashed.', 'trigger-post-trash' ),
                'description'       => $description,
                'callback'          => 'test_post_delete',
                'integration'       => 'wordpress',
            );

        }

        /*
        * Register the post delete trigger logic
        *
        * @since 3.0.4
        */
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
                    $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                    if( $webhook_url_name !== null ){
                        $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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