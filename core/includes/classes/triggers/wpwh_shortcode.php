<?php
if ( ! class_exists( 'WP_Webhooks_Trigger_wpwh_shortcode' ) ) :

	/**
	 * Load the wpwh_shortcode trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Trigger_wpwh_shortcode {

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

			$triggers['wpwh_shortcode'] = $this->trigger_wpwh_shortcode_content();

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

			if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'wpwh_shortcode' ) ) ){
				add_shortcode( 'wpwh_shortcode', array( $this, 'ironikus_trigger_wpwh_shortcode' ), 10, 2 );
			    add_filter( 'ironikus_demo_test_wpwh_shortcode', array( $this, 'ironikus_send_demo_test_wpwh_shortcode' ), 10, 3 );
			}

		}

        /*
        * Register the user login trigger as an element
        */
        public function trigger_wpwh_shortcode_content(){

            $translation_ident = "trigger-wpwh_shortcode-description";

            $parameter = array(
                'custom_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Your custom data construct build out of the shortcode arguments, as well as the data mapping.', $translation_ident ) ),
            );

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data once the <strong>[wpwh_shortcode]</strong> shortcode was called, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Shortcode</strong> (wpwh_shortcode) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Login</strong> (wpwh_shortcode)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you can receive data on the URL once the shortcode is called.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "How to fire the trigger?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "The trigger will be fired whenever the following shortcode is called: <code>[wpwh_shortcode]</code>", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "While the shortcode itself does not do much except of firing the trigger, you might want to add some data to it. To do that, you have two different ways of doing so:", $translation_ident ); ?>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "You can add the data using the data mapping feature ba assigning a data mapping template to your webhook URL.", $translation_ident ); ?></li>
    <li>
        <?php echo WPWHPRO()->helpers->translate( "You can also add the data using the shortcode parameters. E.g. <code>[wpwh_shortcode param=\"some value\"]</code>", $translation_ident ); ?>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "While <strong>param</strong> is the key within the data response, <strong>some value</strong> is the value. The example above will cause an output similar to:", $translation_ident ); ?>
        <pre>
{
    "param": "some value"
}
</pre>
        <?php echo WPWHPRO()->helpers->translate( "We do also support custom tags, meaning you can add dynamic values from the currently given data. E.g. <code>email=\"%user_email%\"</code> - This will add the email of the currently logged in user. For a full list of the dynamic arguments, please take a look at the able down below.", $translation_ident ); ?>

        <table class="wpwh-table wpwh-text-small">
            <thead>
                <tr>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag name", $translation_ident ); ?></td>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag description", $translation_ident ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->get_shortcode_tags() as $tag ) : 
                
                if( ! isset( $tag['tag_name'] ) ){
                    continue;
                }

                $title = '';
                if( isset( $tag['title'] ) ){
                    $title = '<strong>' . $tag['title'] . '</strong><br>';
                }

                $description = '';
                if( isset( $tag['description'] ) ){
                    $description = $tag['description'];
                }
                
                ?>
                <tr>
                    <td><?php echo '%' . $tag['tag_name'] . '%'; ?></td>
                    <td><?php echo $title . $description; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </li>
</ol>
<br>
<br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>add_shortcode</strong> shortcode function:", $translation_ident ); ?> 
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/add_shortcode/">https://developer.wordpress.org/reference/functions/add_shortcode/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_shortcode( 'wpwh_shortcode', array( $this, 'ironikus_trigger_wpwh_shortcode' ), 10, 2 );</pre>
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
                'trigger'           => 'wpwh_shortcode',
                'name'              => WPWHPRO()->helpers->translate( 'Send Data On Shortcode', 'trigger-login-user-content' ),
                'parameter'         => $parameter,
                'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_test_wpwh_shortcode( array(), '', 'wpwh_shortcode' ) ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as the [wpwh_shortcode] shortcode was triggered.', 'trigger-login-user-content' ),
                'description'       => $description,
                'callback'          => 'test_wpwh_shortcode'
            );

        }

        public function ironikus_trigger_wpwh_shortcode( $attr = array(), $content = '' ){
   
            $response_data = array();
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpwh_shortcode' );
            $special_arguments = array(
                'wpwh_trigger_names' => 'all',
                'wpwh_debug' => 'no',
            );

            foreach( $special_arguments as $ak => $dv ){
                if( isset( $attr[ $ak ] ) ){
                    $special_arguments[ $ak ] = $attr[ $ak ];
                    unset( $attr[ $ak ] );
                }
            }

            $shortcode_tags = $this->get_shortcode_tags();
            $attr_validated = $this->validate_data( $attr, $shortcode_tags );

            $trigger_name_whitelist = array();
            if( $special_arguments['wpwh_trigger_names'] !== 'all' ){
                $trigger_names_array = explode( ',', $special_arguments['wpwh_trigger_names'] );
                if( is_array( $trigger_names_array ) ){
                    foreach( $trigger_names_array as $single_trigger ){
                        $trigger_name_whitelist[] = trim( $single_trigger );
                    }
                }
            } 

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                
                if( ! empty( $trigger_name_whitelist ) && ! in_array( $webhook_url_name, $trigger_name_whitelist ) ){
                    continue;
                }

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                }

            }

            do_action( 'wpwhpro/webhooks/wpwh_shortcode', $attr_validated, $attr, $response_data );

            if( $special_arguments['wpwh_debug'] === 'yes' ){
                return $response_data;
            } else {
                return '';
            }
            
        }

        public function validate_data( $attr, $shortcode_tags ){

            if( is_array( $attr ) ){
                foreach( $attr as $ak => $av ){
                    $attr[ $ak ] = call_user_func( array( $this, 'validate_data' ), $av, $shortcode_tags );
                }
            } elseif( is_string( $attr ) ) {
                $attr = $this->validate_shortcode_tags( $attr, $shortcode_tags );
            }

            return $attr;
        }

        /**
         * This function validates all necessary tags for the shortcode.
         *
         * @param $content - The validated content
         * @since 1.4
         * @return mixed
         */
        public function validate_shortcode_tags( $content, $shortcode_tags ){
            $tags = array();
            $values = array();

            foreach( $shortcode_tags as $st ){
                if( isset( $st['tag_name'] ) && isset( $st['value'] ) ){
                    $fulltag = '%' . $st['tag_name'] . '%';
                    $tvalue = ( is_array( $st['value'] ) ) ? call_user_func_array( $st['value'], array( 'content' => $content ) ) : $st['value'];

                    //pre-return single content tags to also allow arrays and objects
                    if( strlen( str_replace( $fulltag, '', $content  ) ) === 0 ){
                        return $tvalue;
                    }

                    //Make sure to only allow strings here
                    if( is_string( $tvalue ) ){
                        $tags[] = $fulltag ;
                        $values[] = $tvalue;
                    }
                    
                }
            }

            $content = str_replace(
                $tags,
                $values,
                $content
            );

            return $content;
        }

        public function get_shortcode_tags(){
            $tags = array(

                'home_url' => array(
                    'tag_name' => 'home_url',
                    'title' => WPWHPRO()->helpers->translate( 'Home URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the home URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_home_url' ),
                ),

                'admin_url' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Admin URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the admin URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_admin_url' ),
                ),

                'date' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Date and Time', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The date and time in mySQL format: Y-m-d H:i:s', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_date' ),
                ),

                'user_id' => array(
                    'tag_name' => 'user_id',
                    'title' => WPWHPRO()->helpers->translate( 'User ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The ID of the currenty logged in user. 0 if none.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_id' ),
                ),

                'user' => array(
                    'tag_name' => 'user',
                    'title' => WPWHPRO()->helpers->translate( 'Full User', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user data of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user' ),
                ),

                'user_email' => array(
                    'tag_name' => 'user_email',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_email' ),
                ),

                'display_name' => array(
                    'tag_name' => 'display_name',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_display_name' ),
                ),

                'user_login' => array(
                    'tag_name' => 'user_login',
                    'title' => WPWHPRO()->helpers->translate( 'User Login Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The login name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_login' ),
                ),

                'user_nicename' => array(
                    'tag_name' => 'user_nicename',
                    'title' => WPWHPRO()->helpers->translate( 'User Nicename', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The nicename of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_nicename' ),
                ),

                'user_roles' => array(
                    'tag_name' => 'user_roles',
                    'title' => WPWHPRO()->helpers->translate( 'User Roles', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The roles of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_roles' ),
                ),

                'user_meta' => array(
                    'tag_name' => 'user_meta',
                    'title' => WPWHPRO()->helpers->translate( 'User Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user meta of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_meta' ),
                ),

                'post_id' => array(
                    'tag_name' => 'post_id',
                    'title' => WPWHPRO()->helpers->translate( 'Post ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post id of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_id' ),
                ),

                'post' => array(
                    'tag_name' => 'post',
                    'title' => WPWHPRO()->helpers->translate( 'Post Data', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post data of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post' ),
                ),

                'post_title' => array(
                    'tag_name' => 'post_title',
                    'title' => WPWHPRO()->helpers->translate( 'Post Title', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post title of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_title' ),
                ),

                'post_excerpt' => array(
                    'tag_name' => 'post_excerpt',
                    'title' => WPWHPRO()->helpers->translate( 'Post Excerpt', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post excerpt of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_excerpt' ),
                ),

                'post_content' => array(
                    'tag_name' => 'post_content',
                    'title' => WPWHPRO()->helpers->translate( 'Post Content', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post content of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_content' ),
                ),

                'post_author' => array(
                    'tag_name' => 'post_author',
                    'title' => WPWHPRO()->helpers->translate( 'Post Author', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post author of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_author' ),
                ),

                'post_type' => array(
                    'tag_name' => 'post_type',
                    'title' => WPWHPRO()->helpers->translate( 'Post Type', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post type of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_type' ),
                ),

                'post_status' => array(
                    'tag_name' => 'post_status',
                    'title' => WPWHPRO()->helpers->translate( 'Post Status', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post status of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_status' ),
                ),

                'post_date' => array(
                    'tag_name' => 'post_date',
                    'title' => WPWHPRO()->helpers->translate( 'Post Date', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post date of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_date' ),
                ),

                'post_meta' => array(
                    'tag_name' => 'post_meta',
                    'title' => WPWHPRO()->helpers->translate( 'Post Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post meta of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_meta' ),
                ),

            );

            return apply_filters( 'wpwhpro/triggers/wpwh_shortcode/tags', $tags );
        }

        public function tag_get_home_url(){
            return home_url();
        }

        public function tag_get_admin_url(){
            return admin_url();
        }

        public function tag_get_date(){
            return date("Y-m-d H:i:s");
        }

        public function tag_get_user_id(){
            return get_current_user_id();
        }

        public function tag_get_user(){
            return $this->get_user();
        }

        public function tag_get_user_email(){
            return $this->get_user('user_email');
        }

        public function tag_get_display_name(){
            return $this->get_user('display_name');
        }

        public function tag_get_user_login(){
            return $this->get_user('user_login');
        }

        public function tag_get_user_nicename(){
            return $this->get_user('user_nicename');
        }

        public function tag_get_user_roles(){
            return $this->get_user('user_roles');
        }

        public function tag_get_user_meta(){
            $return = array();
            $user_id = get_current_user_id();

            if( ! empty( $user_id ) ){
                $return = get_user_meta( $user_id );
            }
            
            return $return;
        }

        public function get_user( $single_val = false ){

            $return = false;
            $user = get_user_by( 'id', get_current_user_id() );

            if( $single_val && ! empty( $user ) ){

                switch( $single_val ){
                    case 'user_email': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_email ) ){
                            $return = $user->data->user_email;
                        }
                        break;
                    case 'display_name': 
                        if( ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
                            $return = $user->data->display_name;
                        }
                        break;
                    case 'user_login': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_login ) ){
                            $return = $user->data->user_login;
                        }
                        break;
                    case 'user_nicename': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_nicename ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                    case 'user_roles': 
                        if( isset( $user->roles ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                }
                
            } else {
                $return = $user;
            }

            return $return;
        }

        public function tag_get_post_id(){
            return get_the_ID();
        }

        public function tag_get_post(){
            return $this->get_post();
        }

        public function tag_get_post_title(){
            return $this->get_post('post_title');
        }

        public function tag_get_post_excerpt(){
            return $this->get_post('post_excerpt');
        }
        
        public function tag_get_post_content(){
            return $this->get_post('post_content');
        }

        public function tag_get_post_author(){
            return $this->get_post('post_author');
        }

        public function tag_get_post_type(){
            return $this->get_post('post_type');
        }

        public function tag_get_post_status(){
            return $this->get_post('post_status');
        }

        public function tag_get_post_date(){
            return $this->get_post('post_date');
        }

        public function tag_get_post_meta(){
            $return = array();
            $post_id = get_the_ID();

            if( ! empty( $post_id ) ){
                $return = get_post_meta( $post_id );
            }
            
            return $return;
        }

        public function get_post( $single_val = false ){

            $return = false;
            $post = get_post( get_the_ID() );

            if( $single_val && ! empty( $post ) ){

                switch( $single_val ){
                    case 'post_title': 
                        if( isset( $post->post_title ) && ! empty( $post->post_title ) ){
                            $return = $post->post_title;
                        }
                        break;
                    case 'post_excerpt': 
                        if( isset( $post->post_excerpt ) && ! empty( $post->post_excerpt ) ){
                            $return = $post->post_excerpt;
                        }
                        break;
                    case 'post_content': 
                        if( isset( $post->post_content ) && ! empty( $post->post_content ) ){
                            $return = $post->post_content;
                        }
                        break;
                    case 'post_author': 
                        if( isset( $post->post_author ) && ! empty( $post->post_author ) ){
                            $return = $post->post_author;
                        }
                        break;
                    case 'post_type': 
                        if( isset( $post->post_type ) && ! empty( $post->post_type ) ){
                            $return = $post->post_type;
                        }
                        break;
                    case 'post_status': 
                        if( isset( $post->post_status ) && ! empty( $post->post_status ) ){
                            $return = $post->post_status;
                        }
                        break;
                    case 'post_date': 
                        if( isset( $post->post_date ) && ! empty( $post->post_date ) ){
                            $return = $post->post_date;
                        }
                        break;
                }
                
            } else {
                $return = $post;
            }

            return $return;
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
        public function ironikus_send_demo_test_wpwh_shortcode( $data, $webhook, $webhook_group ){

            $data = array (
                'your custom data'
            );

            return $data;
        }

    }

endif; // End if class_exists check.