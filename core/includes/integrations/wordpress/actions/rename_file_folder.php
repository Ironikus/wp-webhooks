<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_rename_file_folder' ) ) :

	/**
	 * Load the rename_file_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_rename_file_folder {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-rename_file_folder-description";

			$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the folder you want to rename (and the file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/demo-folder or for a file wp-content/themes/demo-theme/demo-file.php', $translation_ident ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path with the new folder name (or the new file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/new-demo-folder or for a file wp-content/themes/demo-theme/new-demo-file.php', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			ob_start();
			?>
<p><?php echo WPWHPRO()->helpers->translate( 'In case you want to rename a file or a folder inside the WordPress root folder, just declare the file/folder itself:', $translation_ident ); ?></p>
<br>
<pre>demo-file.php</pre>
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
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success", $translation_ident ); ?>
	</li>
	<li>
		<strong>$source_path</strong> (string)
		<?php echo WPWHPRO()->helpers->translate( "The path of the folder/file you moved.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)
		<?php echo WPWHPRO()->helpers->translate( "The new path after the folder/file was moved.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The file/folder was successfully renamed.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Rename file or folder',
				'webhook_slug' => 'rename_file_folder',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>source_path</strong> argument. Please set it to the path of the folder/file you want to move.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'The third required argument is <strong>destination_path</strong>. Please set it to the path you want to move the folder/file to.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'For security reasons, we restrict renaming of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ) . ' <code>wp-content/uploads/demo-file.php</code>',
					WPWHPRO()->helpers->translate( 'It is also possible to change the extension of a file. just change it for the destination path.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'rename_file_folder',
				'name'			  => WPWHPRO()->helpers->translate( 'Rename file or folder', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'rename a file or a folder', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Rename a local file or folder via a webhook inside of your WordPress folder structure.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.