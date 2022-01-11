<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_copy_file' ) ) :

	/**
	 * Load the copy_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_copy_file {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-copy_file-description";

			$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the file you want to copy. This can be a relative path or an external url. For example: wp-content/themes/demo-theme/demo-file.php or a full url like https://my-domain/image.jpg', $translation_ident ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path as well as file name. For example: wp-content/uploads/demo-image.jpg (See the main description for more information)', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			ob_start();
			?>
<p><?php echo WPWHPRO()->helpers->translate( 'Please note: If destination_path is a URL, the copy operation may fail if the wrapper does not support overwriting of existing files. If the destination file already exists, it will be overwritten.', $translation_ident ); ?></p>
<br>
<pre>wp-content/uploads/demo-image.jpg</pre>
<br>
<p><?php echo WPWHPRO()->helpers->translate( 'In case you want to copy a file into the WordPress root folder, just declare the file itself:', $translation_ident ); ?></p>
<br>
<pre>demo-image.jpg</pre>
<br>
<br>
<p><?php echo WPWHPRO()->helpers->translate( 'It is also possible to rename the file while you copy it. Just set a custom file name for the destination_path:', $translation_ident ); ?></p>
<br>
<pre>wp-content/uploads/another-demo-image.jpg</pre>
			<?php
			$parameter['destination_path']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", $translation_ident ); ?>
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
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$source_path</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The source path you set within the webhook request.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The destination path you set within the webhook request.", $translation_ident ); ?>
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
				'msg' => 'The file was successfully copied.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Copy file',
				'webhook_slug' => 'copy_file',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>source_path</strong> argument. Please set it to the relative path or a full URL as mentioned in the argument description.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'The third required argument is <strong>destination_path</strong>. Please set it to the relative path, including the new file name.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'This webhook enables you to copy a local or remote file inside of your WordPress folder structure. (The previous file will not be removed)', $translation_ident ),
					WPWHPRO()->helpers->translate( 'For security reasons, we restrict copying of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ) . '<code>wp-content/uploads/demo-image.jpg</code>',
				)
			) );

			return array(
				'action'			=> 'copy_file',
				'name'			  => WPWHPRO()->helpers->translate( 'Copy file', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'copy a file', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Copy a local or remote file via a webhook inside of your WordPress folder structure.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.