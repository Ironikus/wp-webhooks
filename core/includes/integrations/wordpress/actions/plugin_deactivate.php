<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_plugin_deactivate' ) ) :

	/**
	 * Load the plugin_deactivate action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_plugin_deactivate {

		public function is_active(){

			//Backwards compatibility for the "Manage Plugins" integration
			if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-plugin_deactivate-description";

			$parameter = array(
				'plugin_slug'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) The plugin slug of the plugin you want to deactivate. Please check the description for further details.', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks Pro fires this webhook. Please check the description for further details.', $translation_ident ) )
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains the slug of a plugin. This isually is the folder name of the plugin, followed by a slash and the plugin file name + file extension. Down below is an example.", $translation_ident ); ?>
<pre>wpwh-comments/wpwh-plugin-file.php</pre>
<?php echo WPWHPRO()->helpers->translate( "The above slug is defined based on the plugin setup. <strong>wpwh-comments</strong> is the name of the plugin folder. <strong>wpwh-plugin-file.php</strong> is the file name of the plugin file within the folder (The file where you defined your plugin details within the comment).", $translation_ident ); ?>
<?php
			$parameter['plugin_slug']['description'] = ob_get_clean();

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>plugin_deactivate</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $plugin_slug, $check, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$plugin_slug</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The currently given slug (+ filename) of the plugin. (The given data from the plugin_slug argument)", $translation_ident ); ?>
	</li>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "True if the plugin was successfully deactivated, false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
<?php
			$parameter['do_action']['description'] = ob_get_clean();

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Plugin successfully deactivated.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Deactivate a plugin',
				'webhook_slug' => 'plugin_deactivate',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'The second argument is plugin_slug, which contains the slug of the given plugin.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'plugin_deactivate',
				'name'			  => WPWHPRO()->helpers->translate( 'Deactivate plugin', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'deactivate a plugin', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to deactivate a plugin within your WordPress website.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.