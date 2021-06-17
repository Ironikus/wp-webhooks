<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_comment' ) ) :

	/**
	 * Load the delete_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_comment {

        public function is_active(){

            //Backwards compatibility for the "Comments" integration
            if( class_exists( 'WP_Webhooks_Comments' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "action-delete_comment-description";

			$parameter = array(
				'comment_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(int) The comment id of the comment you want to delete.', $translation_ident ) ),
				'force_delete' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Wether you want to bypass the trash or not. You can set this value to "yes" or "no". Default "no"', $translation_ident ) ),
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The comment id as comment_id and the force_delete status.', $translation_ident ) ),
				'msg'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The comment was successfully trashed.',
                'data' => 
                array (
                  'comment_id' => 4,
                  'force_delete' => false,
                ),
            );

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( "This hook enables you to delete a comment with all of its settings.", "action-delete_comment-content" ); ?></p>
				<p><?php echo WPWHPRO()->helpers->translate( 'Please note, that this function, by default, pushes the comment to the trash. In case you want to fully delete it, set force_delete to "yes".', $translation_ident ); ?></p>
            <?php
			$description = ob_get_clean();

            return array(
                'action'            => 'delete_comment',
                'name'              => WPWHPRO()->helpers->translate( 'Delete a comment', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Delete a comment using webhooks.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

			$textdomain_context = 'delete_comment';
			$return_args = array(
				'success' => false,
                'msg' => '',
                'data' => array(
					'comment_id'   => 0,
					'force_delete'   => 0,
				),
			);

			$comment_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ));
			$force_delete = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );


			if( empty( $comment_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("A comment id is required to delete the comment.", 'action-' . $textdomain_context );

				return $return_args;
			}
 
			$return_args['data']['comment_id'] = $comment_id;
			$return_args['data']['force_delete'] = $force_delete;
			
			$deleted = wp_delete_comment( $comment_id, $force_delete );

			if( $deleted ){
				$return_args['success'] = true;

				if( $force_delete ){
					$return_args['msg'] = WPWHPRO()->helpers->translate("The comment was successfully deleted.", 'action-' . $textdomain_context );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("The comment was successfully trashed.", 'action-' . $textdomain_context );
				}
				
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("Error while deleting the comment.", 'action-' . $textdomain_context );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $deleted, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.