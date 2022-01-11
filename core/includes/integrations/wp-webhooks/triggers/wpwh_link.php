<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_link' ) ) :

	/**
	 * Load the wpwh_link trigger
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_link {

        /*
        * Register the user login trigger as an element
        */
        public function get_details(){

            $translation_ident = "trigger-wpwh_link-description";
            $wpwh_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks', 'wpwh_helpers' );
            $parameter = array(
                'custom_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Your custom data construct build out of the shortcode arguments, as well as the data mapping.', $translation_ident ) ),
            );

            ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( "The trigger will be fired whenever someone clicks the link that was added with the shortcode <code>[wpwh_link]</code>", $translation_ident ); ?>
</p>
<p>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note:</strong> If the shortcode is not executed and you see the shortcode itself within the frontend, please add a webhook URL first withn this trigger. We only load the shortcode if a trigger URL is added to save performance for your site.", $translation_ident ); ?>
</p>
<?php echo WPWHPRO()->helpers->translate( "While the shortcode itself does not do much except of displaying a custom link on your page, you might want to add some data to it. To do that, you have two different ways of doing so:", $translation_ident ); ?>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "You can add the data using the data mapping feature ba assigning a data mapping template to your webhook URL.", $translation_ident ); ?></li>
    <li>
        <?php echo WPWHPRO()->helpers->translate( "You can also add the data using the shortcode parameters. E.g. <code>[wpwh_link param=\"some value\"]</code>", $translation_ident ); ?>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "While <strong>param</strong> is the key within the data response, <strong>some value</strong> is the value. The example above will cause an output similar to:", $translation_ident ); ?>
        <pre>
{
    "param": "some value"
}
</pre>

<?php echo WPWHPRO()->helpers->translate( "We also support a variety os special attributes within the shortcode tag (e.g. <code>[wpwh_link wpwh_new_window=\"yes\"]</code> ). Down below you will find a list of those, as well as a short description of what they are good for.", $translation_ident ); ?>
<table class="wpwh-table wpwh-text-small">
    <thead>
        <tr>
            <td><?php echo WPWHPRO()->helpers->translate( "Special tag name", $translation_ident ); ?></td>
            <td><?php echo WPWHPRO()->helpers->translate( "Special tag description", $translation_ident ); ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>wpwh_id</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Use this attribute to set a custom id for the link.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_class</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Set custom CSS classes for the link. If you want to set multiple ones, simply leave a space in between: class-1 class-2", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_link_label</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Customizes the text of the link.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_new_window</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Set this argument to \"yes\" if you want the link click to open within a new window.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_trigger_names</td>
            <td><?php echo WPWHPRO()->helpers->translate( "If you want this link to only fire specific webhook URLs, you can define their names here. If you want to use multiple ones, simply separate them via a comma: webhook-1,url-2,demo-3", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_do_redirect</td>
            <td><?php echo WPWHPRO()->helpers->translate( "By default we redirect the link click back to the same page to keep the URL format clean. If you do not want that, set this argument to \"no\".", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_raw_url</td>
            <td><?php echo WPWHPRO()->helpers->translate( "If you prefer to display the URL and not the full link, set this argument to \"yes\".", $translation_ident ); ?></td>
        </tr>
    </tbody>
</table>

        <?php echo WPWHPRO()->helpers->translate( "We do also support custom tags, meaning you can add dynamic values from the currently given data. E.g. <code>email=\"%user_email%\"</code> - This will add the email of the currently logged in user. For a full list of the dynamic arguments, please take a look at the list down below.", $translation_ident ); ?>

        <table class="wpwh-table wpwh-text-small">
            <thead>
                <tr>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag name", $translation_ident ); ?></td>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag description", $translation_ident ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $wpwh_helpers->get_shortcode_tags() as $tag ) : 
                
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
<?php
            $how_to = ob_get_clean();

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Custom link clicked',
				'webhook_slug' => 'wpwh_link',
				'post_delay' => false,
				'how_to' => $how_to,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'init',
                        'url' => 'https://developer.wordpress.org/reference/hooks/init/',
                    ),
				)
			) );

            return array(
                'trigger'           => 'wpwh_link',
                'name'              => WPWHPRO()->helpers->translate( 'Custom link clicked', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a custom link was clicked', $translation_ident ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a custom link was clicked.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wp-webhooks',
                'premium'           => true,
            );

        }

        /*
        * Register the demo data response
        *
        * @param $data - The default options
        *
        * @return array - The demo data
        */
        public function get_demo( $options = array() ){

            $data = array (
                'your custom data construct'
            );

            return $data;
        }

    }

endif; // End if class_exists check.