<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_move_file' ) ) :

	/**
	 * Load the move_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_move_file {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-move_file-description";

			$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the file you want to move. For example: wp-content/uploads/demo-folder/demo-image.jpg', $translation_ident ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the destination. For example: wp-content/uploads/new-folder/demo-file.jpg', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			ob_start();
			?>
<p><?php echo WPWHPRO()->helpers->translate( 'In case you want to move a file into the WordPress root folder, just set the following:', $translation_ident ); ?></p>
<br>
<pre>demo-file.jpg</pre>
<br>
<br>
<p><?php echo WPWHPRO()->helpers->translate( 'It is also possible to rename the file while you move it. Just set a custom file name for the destination_path:', $translation_ident ); ?></p>
<br>
<pre>wp-content/uploads/new-folder/new-demo-file.png</pre>
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
		<?php echo WPWHPRO()->helpers->translate( "The path of the file you moved.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)
		<?php echo WPWHPRO()->helpers->translate( "The new file path after the file was moved.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The file data, as well as the single successful actions of moving the file.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The file was successfully moved.',
				'data' => 
				array (
				  'success' => true,
				  'origin_delete' => true,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Move file',
				'webhook_slug' => 'move_file',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>source_path</strong> argument. Please set it to the path of the file you want to move.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'The third required argument is <strong>destination_path</strong>. Please set it to the path you want to move the file to (Do also include the file name within the path).', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'For security reasons, we restrict moving of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ) . '<code>wp-content/uploads/new-folder/demo-file.jpg</code>',
					WPWHPRO()->helpers->translate( 'Please note: In case the destination folder doesn\'t exist, it will not be generated. You have to create it first.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'move_file',
				'name'			  => WPWHPRO()->helpers->translate( 'Move file', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'move a file', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Move a local file via a webhook inside of your WordPress folder structure.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.