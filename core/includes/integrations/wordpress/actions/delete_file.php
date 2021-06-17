<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_file' ) ) :

	/**
	 * Load the delete_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_file {

        public function is_active(){

            //Backwards compatibility for the "Comments" integration
            if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "action-delete_file-description";

			$parameter = array(
				'file'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The path as well as the file name and extension. For example: wp-content/themes/demo-theme/index.php (See the main description for more information)', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'File successfully deleted.',
            );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to delete a local file inside of your WordPress folder structure.', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'For security reasons, we restrict the deletion of files to the WordPress root folder and its sub folders. This means, that you have to define the path in a relative way. Here is an example:', $translation_ident ); ?></p>
                <br>
                <pre>wp-content/themes/demo-theme/index.php</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'In case you want to delete a file within the WordPress root folder, just declare the file:', $translation_ident ); ?></p>
                <br>
                <pre>demo.php</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $return_args, $file', $translation_ident ); ?></p>

			<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'delete_file',
                'name'              => WPWHPRO()->helpers->translate( 'Delete a file', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Delete a file via a webhook inside of your WordPress folder structure.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> true,
            );

        }

    }

endif; // End if class_exists check.