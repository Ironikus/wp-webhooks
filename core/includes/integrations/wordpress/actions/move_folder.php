<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_move_folder' ) ) :

	/**
	 * Load the move_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_move_folder {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-move_folder-description";

			$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the folder you want to move. For example: wp-content/uploads/demo-folder', $translation_ident ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the destination. For example: wp-content/uploads/new-folder', $translation_ident ) ),
				'mode'	   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The mode is 0777 by default, which means the widest possible access.', $translation_ident ) ),
				'recursive'	   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Allows the creation of nested directories specified in the pathname. Possible values: "yes" and "no". Default: "no". If set to yes, all in your path mentioned folders will be created if they don\'t exist.', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			ob_start();
			?>
<p><?php echo WPWHPRO()->helpers->translate( 'In case you want to move a folder into the WordPress root folder, just set the following:', $translation_ident ); ?></p>
<br>
<pre>demo-folder</pre>
<br>
<br>
<p><?php echo WPWHPRO()->helpers->translate( 'It is also possible to rename the folder while you move it. Just set a custom folder name for the destination_path:', $translation_ident ); ?></p>
<br>
<pre>wp-content/uploads/new-folder</pre>
			<?php
			$parameter['destination_path']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>manage_term_meta</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $source_path, $destination_path ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", $translation_ident ); ?>
	</li>
	<li>
		<strong>$source_path</strong> (string)
		<?php echo WPWHPRO()->helpers->translate( "The path of the folder you moved.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)
		<?php echo WPWHPRO()->helpers->translate( "The new folder path after the folder was moved.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The folder data, as well as the single successful actions of moving the folder.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'data' => 
				array (
				  'success' => true,
				  'data' => 
				  array (
				  ),
				),
				'msg' => 'The folder was successfully moved.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Move folder',
				'webhook_slug' => 'move_folder',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>source_path</strong> argument. Please set it to the path of the folder you want to move.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'The third required argument is <strong>destination_path</strong>. Please set it to the path you want to move the folder to.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'For security reasons, we restrict moving of folders to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ) . '<code>wp-content/uploads/demo-folder</code>',
				),
			) );

			return array(
				'action'			=> 'move_folder',
				'name'			  => WPWHPRO()->helpers->translate( 'Move folder', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'move a folder', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Move a local folder via a webhook inside of your WordPress folder structure.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.