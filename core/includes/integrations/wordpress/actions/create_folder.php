<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_folder' ) ) :

	/**
	 * Load the create_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_folder {

        public function is_active(){

            //Backwards compatibility for the "Comments" integration
            if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "action-create_folder-description";

			$parameter = array(
				'folder'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The relative path as well as the folder name. For example: wp-content/themes/demo-theme/demo-folder (See the main description for more information)', $translation_ident ) ),
				'mode'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'The mode is 0777 by default, which means the widest possible access.', $translation_ident ) ),
				'recursive'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'Allows the creation of nested directories specified in the pathname. Possible values: "yes" and "no". Default: "no" (See the main description for more information)', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'Folder successfully created.',
            );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to create a local folder inside of your WordPress folder structure.', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'For security reasons, we restrict the creation of folders to the WordPress root folder and its sub folders. This means, that you have to define the path in a relative way. Here is an example:', $translation_ident ); ?></p>
                <br>
                <pre>wp-content/themes/demo-theme/demo-folder</pre>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'In case you want to create a folder within the WordPress root folder, just declare the folder itself:', $translation_ident ); ?></p>
                <br>
                <pre>demo-folder</pre>
                <br>
                <br>
                <p><?php echo WPWHPRO()->helpers->translate( 'If you set recursive to "yes", all in your path mentioned folders will be created if they don\'t exist.', $translation_ident ); ?></p>
                <br><br>
                <p><?php echo WPWHPRO()->helpers->translate( 'With the do_action parameter, you can fire a custom action at the end of the process. Just add your custom action via wordpress hook. We pass the following parameters with the action: $return_args, $folder', $translation_ident ); ?></p>

			<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'create_folder',
                'name'              => WPWHPRO()->helpers->translate( 'Create a folder', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Create a folder via a webhook inside of your WordPress folder structure.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> true,
            );

        }

    }

endif; // End if class_exists check.