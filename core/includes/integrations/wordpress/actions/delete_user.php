<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_user' ) ) :

	/**
	 * Load the delete_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_user {

		/*
	 * The core logic to delete a specified user
	 */
	public function get_details(){

		$translation_ident = "action-delete-user-content";

		$parameter = array(
			'user_id'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the numeric id of the user.', $translation_ident ) ),
			'user_email'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email is defined) Include the assigned email of the user.', $translation_ident ) ),
			'send_email'	=> array(
				'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user that the account got deleted.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing the notice of the deleted account.", $translation_ident )
			),
			'remove_from_network'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to delete a user from the whole network. WARNING: This will delete all posts authored by the user. Default: "no"', $translation_ident ) ),
			'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_user</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user, $user_id, $user_email, $send_email ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$user</strong> (object)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the WordPress user object.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the user id of the deleted user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_email</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the user email.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$send_email</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the send_email argument.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user delete success boolean with the key "user_deleted". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'User successfully deleted.',
			'data' => 
			array (
			  'user_deleted' => true,
			  'user_id' => 112,
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Delete a user',
			'webhook_slug' => 'delete_user',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set either the email or the user id of the user you want to delete. You can do that by using the <strong>user_id</strong> or <strong>user_email</strong> argument.', $translation_ident )
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'Please note that deleting a user inside of a multisite network without setting the <strong>remove_from_network</strong> argument, just deletes the user from the current site, but not from the whole network.', $translation_ident )
			),
		) );

		return array(
			'action'			=> 'delete_user',
			'name'			  => WPWHPRO()->helpers->translate( 'Delete user', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'delete a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Deletes a user via on your WordPress website or network.', 'action-create-user-content' ),
			'description'	   => $description,
			'integration'	   => 'wordpress',
			'premium' 			=> false,
		);

	}

		/**
		 * Delete function for defined action
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array(
					'user_deleted' => false,
					'user_id' => 0
				)
			);

			$user_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$user_email  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
			$send_email  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
			$remove_from_network  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_from_network' ) == 'yes' ) ? 'yes' : 'no';
			$user = '';

			if( ! empty( $user_id ) ){
				$user = get_user_by( 'id', $user_id );
			} elseif( ! empty( $user_email ) ){
				$user = get_user_by( 'email', $user_email );
			}

			if( ! empty( $user ) ){
				if( ! empty( $user->ID ) ){

					$user_id = $user->ID;

					$delete_administrators = apply_filters( 'wpwhpro/run/delete_action_user_admins', false );
					if ( in_array( 'administrator', $user->roles ) && ! $delete_administrators ) {
						exit;
					}

					require_once( ABSPATH . 'wp-admin/includes/user.php' );

					if( is_multisite() && $remove_from_network == 'yes' ){

						if( ! function_exists( 'wpmu_delete_user' ) ){
							require_once( ABSPATH . 'wp-admin/includes/ms.php' );
						}

						$checkdelete = wpmu_delete_user( $user_id );
					} else {
						$checkdelete = wp_delete_user( $user_id );
					}

					if ( $checkdelete ) {

						$send_admin_notification = apply_filters( 'wpwhpro/run/delete_action_user_notification', true );
						if( $send_admin_notification && $send_email == 'yes' ){
							$blog_name = get_bloginfo( "name" );
							$blog_email = get_bloginfo( "admin_email" );
							$headers = 'From: ' . $blog_name . ' <' . $blog_email . '>' . "\r\n";
							$subject = WPWHPRO()->helpers->translate( 'Your account has been deleted.', 'action-delete-user' );
							$content = sprintf( WPWHPRO()->helpers->translate( "Hello %s,\r\n", 'action-delete-user' ), $user->user_nicename );
							$content .= sprintf( WPWHPRO()->helpers->translate( 'Your account at %s (%d) has been deleted.' . "\r\n", 'action-delete-user' ), $blog_name, home_url() );
							$content .= sprintf( WPWHPRO()->helpers->translate( 'Please contact %s for further questions.', 'action-delete-user' ), $blog_email );

							wp_mail( $user_email, $subject, $content, $headers );
						}

						do_action( 'wpwhpro/run/delete_action_user_deleted' );

						$return_args['msg'] = WPWHPRO()->helpers->translate("User successfully deleted.", 'action-delete-user-error' );
						$return_args['success'] = true;
						$return_args['data']['user_deleted'] = true;
						$return_args['data']['user_id'] = $user_id;
					} else {
						$return_args['msg'] = WPWHPRO()->helpers->translate("Error deleting user.", 'action-delete-user-error' );
					}

				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Could not delete user because the user not given.", 'action-delete-user-error' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $user, $user_id, $user_email, $send_email );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.