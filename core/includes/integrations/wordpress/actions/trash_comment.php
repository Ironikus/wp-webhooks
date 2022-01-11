<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_trash_comment' ) ) :

	/**
	 * Load the trash_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_trash_comment {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Comments' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-trash_comment-description";

			$parameter = array(
				'comment_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(int) The comment id of the comment you want to trash.', $translation_ident ) ),
				'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the action was fired.', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>trash_comment</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $trashed, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ID of the comment you trashed.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$trashed</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "The respone of the wp_trash_comment() function.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The comment id as comment_id.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The comment was successfully trashed.',
				'data' => 
				array (
				  'comment_id' => 4,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Trash a comment',
				'webhook_slug' => 'trash_comment',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the comment ID of the comment you want to trash. You can do that by using the <strong>comment_id</strong> argument.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'trash_comment',
				'name'			  => WPWHPRO()->helpers->translate( 'Trash comment', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'trash a comment', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Trash a comment using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$textdomain_context = 'trash_comment';
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'comment_id'   => 0,
				),
			);

			$comment_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ));

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );


			if( empty( $comment_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("A comment id is required to trash the comment.", 'action-' . $textdomain_context );

				return $return_args;
			}
 
			$return_args['data']['comment_id'] = $comment_id;
			
			$trashed = wp_trash_comment( $comment_id );

			if( $trashed ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate("The comment was successfully trashed.", 'action-' . $textdomain_context );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("Error while trashing the comment.", 'action-' . $textdomain_context );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $trashed, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.