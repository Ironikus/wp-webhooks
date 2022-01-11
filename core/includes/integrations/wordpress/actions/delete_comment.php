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
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after this webhook is executed.', $translation_ident ) )
			);

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "Please note: The attachment is moved to the trash instead of being permanently deleted, unless trash for media is disabled, the item is already in the trash, or force_delete is true.", $translation_ident ); ?>
			<?php
			$parameter['force_delete']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_attachment</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $deleted, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the comment you deleted.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$deleted</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "True if the comment was deleted, false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The comment id as comment_id and the force_delete status.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
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

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete a comment',
				'webhook_slug' => 'delete_comment',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>comment_id</strong> argument. Please set it to the ID of the comment you want to delete.', $translation_ident )
				)
			) );

			return array(
				'action'			=> 'delete_comment',
				'name'			  => WPWHPRO()->helpers->translate( 'Delete comment', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'delete a comment', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Delete a comment using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
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