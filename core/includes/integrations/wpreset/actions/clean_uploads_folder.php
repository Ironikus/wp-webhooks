<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_clean_uploads_folder' ) ) :

	/**
	 * Load the clean_uploads_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_clean_uploads_folder {

        /*
        * The core logic to test a webhook
        */
        public function get_details(){

            $translation_ident = "action-clean_uploads_folder-content";

            $parameter = array(
                'confirm'            => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Please set this value to "yes". If not set, the upload folder doesn\'t get cleaned.', $translation_ident ) ),
                'do_action'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
            );

            $returns = array(
                'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
                'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) Count of all the deleted transients.', 'action-create_url_attachment-content' ) ),
                'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
            );

            $returns_code = array (
                'success' => true,
                'msg' => 'Uploads folder successfully cleaned.',
                'data' => 
                array (
                  'count' => 7,
                ),
            );

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to clean the uploads folder of your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "It uses the WP Reset function <strong>do_delete_uploads()</strong> to clean the folder.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>clean_uploads_folder</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>clean_uploads_folder</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>clean_uploads_folder</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>confirm</strong>, which is needed to confirm that you really want to clear the uploads folder as the action is irreversible.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the functionality of the action.", $translation_ident ); ?></li>
</ol>
<?php
            $description = ob_get_clean();

            return array(
                'action'            => 'clean_uploads_folder', //required
                'name'               => WPWHPRO()->helpers->translate( 'Clean uploads folder', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Clean your upload directory on your website using webhooks.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wpreset'
            );


        }

        public function execute( $return_data, $response_body ){

            $reset_helpers = WPWHPRO()->integrations->get_helper( 'wpreset', 'reset_helpers' );
			$return_args = array(
				'success' => false,
                'msg' => '',
                'data' => array(
                    'count' => 0
                )
			);

			$confirm        = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$do_action      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				$count = $reset_helpers->get_wp_reset()->do_delete_uploads();

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Uploads folder successfully cleaned.", 'action-clean_uploads_folder-success' );
				$return_args['data']['count'] = $count;

            } else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: upload directory not cleared. You did not set the confirmation parameter.", 'action-clean_uploads_folder-success' );

            }

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $count );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.