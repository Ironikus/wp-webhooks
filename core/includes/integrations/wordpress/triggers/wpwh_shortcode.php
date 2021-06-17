<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wpwh_shortcode' ) ) :

	/**
	 * Load the wpwh_shortcode trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wpwh_shortcode {

        /*
        * Register the user login trigger as an element
        */
        public function get_details(){

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
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
            $description = ob_get_clean();

            return array(
                'trigger'           => 'wpwh_shortcode',
                'name'              => WPWHPRO()->helpers->translate( 'Shortcode called', 'trigger-login-user-content' ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as the [wpwh_shortcode] shortcode was triggered.', 'trigger-login-user-content' ),
                'description'       => $description,
                'callback'          => 'test_wpwh_shortcode',
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function get_shortcode_tags(){
            $tags = array(

                'home_url' => array(
                    'tag_name' => 'home_url',
                    'title' => WPWHPRO()->helpers->translate( 'Home URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the home URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'admin_url' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Admin URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the admin URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'date' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Date and Time', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The date and time in mySQL format: Y-m-d H:i:s', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_id' => array(
                    'tag_name' => 'user_id',
                    'title' => WPWHPRO()->helpers->translate( 'User ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The ID of the currenty logged in user. 0 if none.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user' => array(
                    'tag_name' => 'user',
                    'title' => WPWHPRO()->helpers->translate( 'Full User', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user data of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_email' => array(
                    'tag_name' => 'user_email',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'display_name' => array(
                    'tag_name' => 'display_name',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_login' => array(
                    'tag_name' => 'user_login',
                    'title' => WPWHPRO()->helpers->translate( 'User Login Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The login name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_nicename' => array(
                    'tag_name' => 'user_nicename',
                    'title' => WPWHPRO()->helpers->translate( 'User Nicename', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The nicename of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_roles' => array(
                    'tag_name' => 'user_roles',
                    'title' => WPWHPRO()->helpers->translate( 'User Roles', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The roles of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'user_meta' => array(
                    'tag_name' => 'user_meta',
                    'title' => WPWHPRO()->helpers->translate( 'User Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user meta of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_id' => array(
                    'tag_name' => 'post_id',
                    'title' => WPWHPRO()->helpers->translate( 'Post ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post id of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post' => array(
                    'tag_name' => 'post',
                    'title' => WPWHPRO()->helpers->translate( 'Post Data', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post data of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_title' => array(
                    'tag_name' => 'post_title',
                    'title' => WPWHPRO()->helpers->translate( 'Post Title', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post title of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_excerpt' => array(
                    'tag_name' => 'post_excerpt',
                    'title' => WPWHPRO()->helpers->translate( 'Post Excerpt', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post excerpt of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_content' => array(
                    'tag_name' => 'post_content',
                    'title' => WPWHPRO()->helpers->translate( 'Post Content', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post content of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_author' => array(
                    'tag_name' => 'post_author',
                    'title' => WPWHPRO()->helpers->translate( 'Post Author', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post author of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_type' => array(
                    'tag_name' => 'post_type',
                    'title' => WPWHPRO()->helpers->translate( 'Post Type', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post type of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_status' => array(
                    'tag_name' => 'post_status',
                    'title' => WPWHPRO()->helpers->translate( 'Post Status', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post status of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_date' => array(
                    'tag_name' => 'post_date',
                    'title' => WPWHPRO()->helpers->translate( 'Post Date', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post date of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

                'post_meta' => array(
                    'tag_name' => 'post_meta',
                    'title' => WPWHPRO()->helpers->translate( 'Post Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post meta of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => null,
                ),

            );

            return apply_filters( 'wpwhpro/triggers/wpwh_shortcode/tags', $tags );
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
        public function get_demo( $options = array() ){

            $data = array (
                'your custom data'
            );

            return $data;
        }

    }

endif; // End if class_exists check.