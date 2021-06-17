<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_file' ) ) :

	/**
	 * Load the create_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_file {

        public function is_active(){

            //Backwards compatibility for the "Comments" integration
            if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "action-create_file-description";

			$parameter = array(
				'file'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The path as well as the file name and extension. For example: wp-content/themes/demo-theme/index.php (See the main description for more information)', $translation_ident ) ),
				'content'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The content for your file.', $translation_ident ) ),
				'mode'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'The mode of the file. Default "w" (Write)', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'File successfully created.',
            );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to create a file inside of your WordPress folder structure.', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'For security reasons, we restrict the creation of files to the WordPress root folder and its sub folders. This means, that you have to define the path in a relative way. Here is an example:', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'Please note: The folder structure must exist before you can create the file. Otherwise this webhook will return an error.', $translation_ident ); ?></p>
                <br>
                <pre>wp-content/themes/demo-theme/index.php</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'In case you want to create a file within the WordPress root folder, just declare the file:', $translation_ident ); ?></p>
                <br>
                <pre>demo.php</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $return_args, $file, $content, $mode', $translation_ident ); ?></p>

			<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'create_file',
                'name'              => WPWHPRO()->helpers->translate( 'Create a file', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Create a file via a webhook inside of your WordPress folder structure.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> true,
            );

        }

    }

endif; // End if class_exists check.