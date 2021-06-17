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
				'source_path'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the file you want to move. For example: wp-content/uploads/demo-folder/demo-image.jpg', $translation_ident ) ),
				'destination_path'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path of the destination. For example: wp-content/uploads/new-folder/demo-file.jpg (See the main description for more information)', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The file data, as well as the single successful actions of moving the file.', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
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

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to move a local file inside of your WordPress folder structure. (The previous file will be removed)', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'For security reasons, we restrict moving of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'Please note: In case the destination folder doesn\'t exist, it will not be generated. You have to create it first.', $translation_ident ); ?></p>
            <br>
                <pre>wp-content/uploads/new-folder/demo-file.jpg</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'In case you want to move a file into the WordPress root folder, just set the following:', $translation_ident ); ?></p>
                <br>
                <pre>demo-file.jpg</pre>
                <br>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'It is also possible to rename the file while you move it. Just set a custom file name for the destination_path:', $translation_ident ); ?></p>
                <br>
                <pre>wp-content/uploads/new-folder/new-demo-file.png</pre>
                <br>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $return_args, $source_path, $destination_path', $translation_ident ); ?></p>

			<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'move_file',
                'name'              => WPWHPRO()->helpers->translate( 'Move file', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Move a local file via a webhook inside of your WordPress folder structure.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> true,
            );

        }

    }

endif; // End if class_exists check.