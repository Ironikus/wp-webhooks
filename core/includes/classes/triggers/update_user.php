<?php
if ( ! class_exists( 'WP_Webhooks_Trigger_update_user' ) ) :

	/**
	 * Load the update_user trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Trigger_update_user {

		function __construct(){
			add_filter( 'wpwhpro/webhooks/get_webhooks_triggers', array( $this, 'add_trigger_details' ), 10 );
			add_action( 'plugins_loaded', array( $this, 'add_trigger_callback' ), 10 );
        }

        /**
         * Register the webhook details
         *
         * @param array $triggers
         * @return array The adjusted webhook details
         */
		public function add_trigger_details( $triggers ){

			$triggers['update_user'] = $this->trigger_update_user_content();

			return $triggers;
		}

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function add_trigger_callback(){

			if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'update_user' ) ) ){
				add_action( 'profile_update', array( $this, 'ironikus_trigger_user_update_init' ), 10, 2 );
			    add_filter( 'ironikus_demo_test_user_update', array( $this, 'ironikus_send_demo_user_create' ), 10, 3 );
			}

		}

        /*
        * Register the user update trigger as an element
        *
        * @return array
        */
        public function trigger_update_user_content(){

            $translation_ident = "trigger-update-user-description";

            $parameter = array(
                'user_object'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The request will send the full user object as an array. Please see https://codex.wordpress.org/Class_Reference/WP_User for more details.', 'trigger-update-user-content' ) ),
                'user_meta'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-update-user-content' ) ),
                'user_old_data' => array( 'short_description' => WPWHPRO()->helpers->translate( 'This is the object with the previous user object as an array. You can recheck your data on it as well.', 'trigger-update-user-content' ) ),
            );

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $parameter['acf_data'] = array( 'short_description' => WPWHPRO()->helpers->translate( 'The Advanced Custom Fields user meta is also pushed to the user object. You will find it on the first layer of the object as well. ', 'trigger-create-user-content' ) );
            }

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on the update of a user, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On User Update</strong> (update_user) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On User Update</strong> (update_user)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>profile_update</strong> hook:", $translation_ident ); ?> 
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/hooks/profile_update/">https://developer.wordpress.org/reference/hooks/profile_update/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'profile_update', array( $this, 'ironikus_trigger_user_update_init' ), 10, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (profile_update) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
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
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<?php
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

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $user_data );
                }

            }

            do_action( 'wpwhpro/webhooks/trigger_user_update', $user_id, $user_data, $response_data );
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

    }

endif; // End if class_exists check.