<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_download' ) ) :

	/**
	 * Load the edd_delete_download action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_download {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_delete_download-description";

            $parameter = array(
				'download_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The download id of the download you want to delete.', $translation_ident ) ),
				'force_delete'  	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(optional) Whether to bypass trash and force deletion. Possible values: "yes" and "no". Default: "no".', $translation_ident ) ),
				'do_action'     	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-edd_delete_download-content' ) ),
				'msg'        		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Within the data array, you will find further details about the response, as well as the download id and further information.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The download was successfully deleted.',
				'data' => 
				array (
				  'post_id' => 747,
				  'force_delete' => false,
				),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a download on your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>edd_delete_download</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_delete_download</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_delete_download</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the download id of the download you want to delete. You can do that by using the <strong>download_id</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the deletion of the download.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Please note that deleting a download without defining the <strong>force_delete</strong> argument set to <strong>yes</strong>, only moves the downloads to the trash.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "force_delete", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the download will be completely removed from your WordPress website.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_delete_download</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post, $post_id, $check, $force_delete ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$post</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the WordPress download object of the already deleted download.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the download id of the deleted post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$check</strong> (mixed)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the response of the wp_delete_post() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$force_delete</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the force_delete argument.", $translation_ident ); ?>
    </li>
</ol>
<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'edd_delete_download',
                'name'              => WPWHPRO()->helpers->translate( 'Delete download', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'delete a download', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to delete (or trash) a download within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'post_id' => 0,
					'force_delete' => false
				)
			);

			$post_id         = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );
			$force_delete    = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
			$do_action       = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) : '';
			$post = '';
			$check = '';

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
			}

			if( ! empty( $post ) ){
				if( ! empty( $post->ID ) ){

					if( $force_delete ){
						$check = wp_delete_post( $post->ID, $force_delete );
					} else {
						$check = wp_trash_post( $post->ID );
					}

					if ( $check ) {

						if( $force_delete  ){
							$return_args['msg']     = WPWHPRO()->helpers->translate("Download successfully deleted.", 'action-delete-download-success' );
						} else {
							$return_args['msg']     = WPWHPRO()->helpers->translate("Download successfully trashed.", 'action-delete-download-success' );
						}
						
						$return_args['success'] = true;
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					} else {
						if( $force_delete  ){
							$return_args['msg']  = WPWHPRO()->helpers->translate("Error deleting download. Please check wp_delete_post() for more information.", 'action-delete-download-success' );
						} else {
							$return_args['msg']  = WPWHPRO()->helpers->translate("Error trashing download. Please check wp_trash_post() for more information.", 'action-delete-download-success' );
						}
						
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					}

				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Could not delete the download: No ID given.", 'action-delete-download-success' );
				}
			} else {
				$return_args['msg']  = WPWHPRO()->helpers->translate("No download found to your specified download id.", 'action-delete-download-success' );
				$return_args['data']['post_id'] = $post_id;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post, $post_id, $check, $force_delete );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.