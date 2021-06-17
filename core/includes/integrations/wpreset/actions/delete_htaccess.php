<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_delete_htaccess' ) ) :

	/**
	 * Load the delete_htaccess action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_delete_htaccess {

        /*
        * The core logic to test a webhook
        */
        public function get_details(){

            $translation_ident = "action-delete_htaccess-content";

            $parameter = array(
				'confirm'            => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Please set this value to "yes". If not set, the htaccess file will not be deleted.', $translation_ident ) ),
				'do_action'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) True if successful or error on failure.', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'htaccess file successfully deleted.',
                'data' => 
                array (
                  'response' => true,
                ),
              );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to delete the main htaccess file within your WordPress root directory.', $translation_ident ); ?></p>
                <p><?php echo WPWHPRO()->helpers->translate( 'The do_action parameter includes the following attributes: $return_args, $confirm, $response', $translation_ident ); ?></p>
            <?php
			$description = ob_get_clean();

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete the main .htaccess file of your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "It uses the WP Reset function <strong>do_delete_htaccess()</strong> to delete the .htaccess file.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>delete_htaccess</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>delete_htaccess</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>delete_htaccess</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>confirm</strong>, which is needed to confirm that you really want to delete the .htaccess file as the action is irreversible.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the functionality of the action.", $translation_ident ); ?></li>
</ol>
<?php
            $description = ob_get_clean();

            return array(
                'action'            => 'delete_htaccess', //required
                'name'               => WPWHPRO()->helpers->translate( 'Delete .htaccess file', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Delete the main .htaccess file on your website using webhooks.', $translation_ident ),
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
                    'response' => null
                )
			);

			$confirm            = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				$response = $reset_helpers->get_wp_reset()->do_delete_htaccess();

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "htaccess file successfully deleted.", 'action-delete_htaccess-success' );
				$return_args['data']['response'] = $response;

            } else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: The htaccess file was not deleted. You did not set the confirmation parameter.", 'action-delete_htaccess-success' );

            }

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $response );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.