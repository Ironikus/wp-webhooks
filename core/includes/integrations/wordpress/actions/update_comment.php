<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_update_comment' ) ) :

	/**
	 * Load the update_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_update_comment {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Comments' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-update_comment-description";

			$parameter = array(
				'comment_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) The HTTP user agent of the comment_author when the comment was submitted. Default empty.', $translation_ident ) ),
				'comment_agent' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The HTTP user agent of the comment_author when the comment was submitted. Default empty.', $translation_ident ) ),
				'comment_approved' => array( 'short_description' => WPWHPRO()->helpers->translate( '(int|string) Whether the comment has been approved. Default 1.', $translation_ident ) ),
				'comment_author' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The name of the author of the comment. Default empty.', $translation_ident ) ),
				'comment_author_email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The email address of the $comment_author. Default empty.', $translation_ident ) ),
				'comment_author_IP' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The IP address of the $comment_author. Default empty.', $translation_ident ) ),
				'comment_author_url' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The URL address of the $comment_author. Default empty.', $translation_ident ) ),
				'comment_content' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The content of the comment. Default empty.', $translation_ident ) ),
				'comment_date' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date the comment was submitted. To set the date manually, comment_date_gmt must also be specified. Default is the current time.', $translation_ident ) ),
				'comment_date_gmt' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date the comment was submitted in the GMT timezone. Default is comment_date in the site\'s GMT timezone.', $translation_ident ) ),
				'comment_karma' => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The karma of the comment. Default 0.', $translation_ident ) ),
				'comment_parent' => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) ID of this comment\'s parent, if any. Default 0.', $translation_ident ) ),
				'comment_post_ID' => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) ID of the post that relates to the comment, if any. Default 0.', $translation_ident ) ),
				'comment_type' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Comment type. Default empty.', $translation_ident ) ),
				'comment_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Optional. Array of key/value pairs to be stored in commentmeta for the new comment. More info within the description.', $translation_ident ) ),
				'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) ID of the user who submitted the comment. Default 0.', $translation_ident ) ),
				'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the action was fired.', $translation_ident ) ),
			);

			ob_start();
		?>
<p><?php echo WPWHPRO()->helpers->translate( 'You can also add custom comment meta. Here is an example on how this would look like using the simple structure (We also support json):', $translation_ident ); ?></p>
<br><br>
<pre>meta_key_1,meta_value_1;my_second_key,add_my_value</pre>
<br><br>
<?php echo WPWHPRO()->helpers->translate( 'To separate the meta from the value, you can use a comma ",". To separate multiple meta settings from each other, easily separate them with a semicolon ";" (It is not necessary to set a semicolon at the end of the last one)', $translation_ident ); ?>
<br><br>
<?php echo WPWHPRO()->helpers->translate( 'This is an example on how you can include the comment meta using JSON.', $translation_ident ); ?>
<br>
<pre>
{
	"meta_key_1": "This is my meta value 1",
	"another_meta_key": "This is my second meta key!"
}
</pre>
		<?php
		$parameter['comment_meta']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>update_comment</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $commentdata, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ID of the comment you updated.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$commentdata</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the data that got updated.", $translation_ident ); ?>
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
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The data related to the comment, as well as the user and the post object, incl. the meta values.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Comment updated successfully.',
				'data' => 
				array (
				  'comment_id' => 1,
				  'comment_data' => 
				  array (
					'comment_ID' => '1',
					'comment_post_ID' => '1',
					'comment_author' => 'A WordPress Commenter',
					'comment_author_email' => 'wapuu@wordpress.example',
					'comment_author_url' => 'https://wordpress.org/',
					'comment_author_IP' => '',
					'comment_date' => '2021-06-01 07:23:29',
					'comment_date_gmt' => '2021-06-01 07:23:29',
					'comment_content' => htmlspecialchars( 'Hi, this is a comment.
					To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.
					Commenter avatars come from <a href="https://gravatar.com">Gravatar</a>.' ),
					'comment_karma' => '0',
					'comment_approved' => '1',
					'comment_agent' => '',
					'comment_type' => 'comment',
					'comment_parent' => '0',
					'user_id' => '0',
				  ),
				  'comment_meta' => 
				  array (
				  ),
				  'current_post_id' => '1',
				  'current_post_data' => 
				  array (
					'ID' => 1,
					'post_author' => '1',
					'post_date' => '2021-06-01 07:23:29',
					'post_date_gmt' => '2021-06-01 07:23:29',
					'post_content' => htmlspecialchars( '<!-- wp:paragraph -->
					<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>
					<!-- /wp:paragraph -->' ),
					'post_title' => 'Hello world!',
					'post_excerpt' => '',
					'post_status' => 'publish',
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_password' => '',
					'post_name' => 'hello-world',
					'to_ping' => '',
					'pinged' => '',
					'post_modified' => '2021-06-01 07:23:29',
					'post_modified_gmt' => '2021-06-01 07:23:29',
					'post_content_filtered' => '',
					'post_parent' => 0,
					'guid' => 'https://yourdomain.test/?p=1',
					'menu_order' => 0,
					'post_type' => 'post',
					'post_mime_type' => '',
					'comment_count' => '1',
					'filter' => 'raw',
				  ),
				  'current_post_data_meta' => 
				  array (
					'_edit_lock' => 
					array (
					  0 => '1622574778:1',
					),
				  ),
				  'user_id' => 0,
				  'user_data' => 
				  array (
				  ),
				  'user_data_meta' => 
				  array (
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Update a comment',
				'webhook_slug' => 'update_comment',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the comment ID of the comment you want to update. You can do that by using the <strong>comment_id</strong> argument.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'For security reasons, we don\'t send the password within the webhook response. To send the password as well, you can check out the following filter: wpwhpro/webhooks/action_update_comment_restrict_user_values', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'update_comment',
				'name'			  => WPWHPRO()->helpers->translate( 'Update comment', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'update a comment', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Updates a comment using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$plugin_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'comment_helpers' );
			$textdomain_context = 'update_comment';

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'comment_id'   => 0,
					'comment_data'  => array(),
					'comment_meta'  => array(),
					'current_post_id' => 0,
					'current_post_data' => array(),
					'current_post_data_meta' => array(),
					'user_id' => 0,
					'user_data' => array(),
					'user_data_meta' => array(),
				),
			);

			$comment_agent		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_agent' );
			$comment_approved		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_approved' );
			$comment_author		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author' );
			$comment_author_email		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_email' );
			$comment_author_IP		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_IP' );
			$comment_author_url		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_url' );
			$comment_content		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_content' );
			$comment_date		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_date' );
			$comment_date_gmt		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_date_gmt' );
			$comment_karma		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_karma' ) );
			$comment_parent		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_parent' ) );
			$comment_post_ID		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_post_ID' ) );
			$comment_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_type' );
			$comment_meta		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_meta' );
			$user_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ));
			$comment_ID		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ));

			if( empty( $comment_ID ) ){
				$comment_ID		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_ID' ));
			}

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			$commentdata = array();

			if( ! empty( $comment_ID ) ){
				$commentdata['comment_ID'] = $comment_ID;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("A comment id is required to update the comment.", 'action-' . $textdomain_context );

				return $return_args;
			}

			if( empty( $comment_agent ) ){
				$commentdata['comment_agent'] = '';
			} else {
				$commentdata['comment_agent'] = $comment_agent;
			}

			if( $comment_approved == 0 ){
				$commentdata['comment_approved'] = 0;
			} else {
				$commentdata['comment_approved'] = $comment_approved;
			}

			if( empty( $comment_author ) ){
				$commentdata['comment_author'] = '';
			} else {
				$commentdata['comment_author'] = $comment_author;
			}

			if( empty( $comment_author_email ) ){
				$commentdata['comment_author_email'] = '';
			} else {
				if( is_email( $comment_author_email ) ){
					$commentdata['comment_author_email'] = $comment_author_email;
				}
			}

			if( empty( $comment_author_IP ) ){
				$commentdata['comment_author_IP'] = '';
			} else {
				$commentdata['comment_author_IP'] = $comment_author_IP;
			}

			if( empty( $comment_author_url ) ){
				$commentdata['comment_author_url'] = '';
			} else {
				$commentdata['comment_author_url'] = $comment_author_url;
			}

			if( empty( $comment_date ) ){
				$commentdata['comment_date'] = current_time( 'mysql' );
			} else {
				$commentdata['comment_date'] = $comment_date;
			}

			if( empty( $comment_date_gmt ) ){
				if( ! empty( $commentdata['comment_date'] ) ){
					$commentdata['comment_date_gmt'] = $commentdata['comment_date'];
				} else {
					$commentdata['comment_date_gmt'] = current_time( 'mysql' );
				}
			} else {
				$commentdata['comment_date_gmt'] = $comment_date_gmt;
			}

			if( empty( $comment_content ) ){
				$commentdata['comment_content'] = '';
			} else {
				$commentdata['comment_content'] = $comment_content;
			}

			if( empty( $comment_karma ) ){
				$commentdata['comment_karma'] = 0;
			} else {
				$commentdata['comment_karma'] = $comment_karma;
			}

			if( empty( $comment_parent ) ){
				$commentdata['comment_parent'] = 0;
			} else {
				$commentdata['comment_parent'] = $comment_parent;
			}

			if( empty( $comment_post_ID ) ){
				$commentdata['comment_post_ID'] = 0;
			} else {
				$commentdata['comment_post_ID'] = $comment_post_ID;
			}

			if( empty( $comment_type ) ){
				$commentdata['comment_type'] = '';
			} else {
				$commentdata['comment_type'] = $comment_type;
			}

			if( empty( $user_id ) ){
				$commentdata['user_id'] = 0;
			} else {
				$commentdata['user_id'] = $user_id;
			}

			//Filter comment meta
			$commentdata = apply_filters( 'wpwhpro/webhooks/trigger_update_comment_commentdata', $commentdata );

			add_action( 'edit_comment', array( $plugin_helpers, 'create_update_comment_add_meta' ), 8, 1 );
			$comment_id = wp_update_comment( $commentdata );
			remove_action( 'edit_comment', array( $plugin_helpers, 'create_update_comment_add_meta' ), 8 );
 
			if ( ! empty( $comment_id ) ) {
				$return_args['success'] = true;
				$return_args['data']['comment_id'] = $comment_id;
				$return_args['data']['comment_data'] = get_comment( $comment_id );
				$return_args['data']['comment_meta'] = get_comment_meta( $comment_id );

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Comment updated successfully.", 'action-' . $textdomain_context );

				$comment = get_comment( $comment_id );

				if( isset( $comment->comment_post_ID ) ){
					$post_id = $comment->comment_post_ID;
					if( ! empty( $post_id ) ){
						$return_args['data']['current_post_id'] = $post_id;
						$return_args['data']['current_post_data'] = get_post( $post_id );
						$return_args['data']['current_post_data_meta'] = get_post_meta( $post_id );
					}
				}
	
				if( isset( $comment->comment_author_email ) && is_email( $comment->comment_author_email ) ){
					$user = get_user_by( 'email', sanitize_email( $comment->comment_author_email ) );
					if( ! empty( $user ) && ! is_wp_error( $user ) ){
						$return_args['data']['user_id'] = $user->data->ID;
						$return_args['data']['user_data'] = $user;
						$return_args['data']['user_data_meta'] = get_user_meta( $user->data->ID );
	
						//Restrict password
						$restrict = apply_filters( 'wpwhpro/webhooks/action_update_comment_restrict_user_values', array( 'user_pass' ) );
						
						if( is_array( $restrict ) && ! empty( $restrict ) ){
	
							foreach( $restrict as $data_key ){
								if( ! empty( $return_args['data']['user_data'] ) && isset( $return_args['data']['user_data']->data ) && isset( $return_args['data']['user_data']->data->{$data_key} )){
									unset( $return_args['data']['user_data']->data->{$data_key} );
								}
							}
							
						}
	
					}
				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The comment was not updated. this either happens because there was an issue or because there were no changes made to the comment.", 'action-' . $textdomain_context );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $commentdata, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.