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
				'source_path'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the folder you want to rename (and the file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/demo-folder or for a file wp-content/themes/demo-theme/demo-file.php', $translation_ident ) ),
				'destination_path'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path with the new folder name (or the new file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/new-demo-folder or for a file wp-content/themes/demo-theme/new-demo-file.php', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The file/folder was successfully renamed.',
            );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook allows you to rename a folder or a file inside of your WordPress folder structure.', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'For security reasons, we restrict renaming of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ); ?></p>
                <br>
                <pre>wp-content/uploads/demo-file.php</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'In case you want to rename a file or a folder inside the WordPress root folder, just declare the file/folder itself:', $translation_ident ); ?></p>
                <br>
                <pre>demo-file.php</pre>
                <br>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'It is also possible to change the extension of a file. just change it for the destination path.', $translation_ident ); ?></p>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $return_args, $source_path, $destination_path', $translation_ident ); ?></p>

			<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'rename_file_folder',
                'name'              => WPWHPRO()->helpers->translate( 'Rename file or folder', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Rename a local file or folder via a webhook inside of your WordPress folder structure.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> true,
            );

        }

    }

endif; // End if class_exists check.