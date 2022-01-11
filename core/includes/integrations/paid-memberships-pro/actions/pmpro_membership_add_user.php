<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_add_user' ) ) :

	/**
	 * Load the pmpro_membership_add_user action
	 *
	 * @since 4.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_add_user {

	public function get_details(){

		$translation_ident = "action-pmpro_membership_add_user-description";

		$parameter = array(
			'user' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID or email of the user you want to assign the membership level to.', $translation_ident ) ),
			'level_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the membership level you want to assign to the user.', $translation_ident ) ),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The level was successfuly assigned to the given user.',
		);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the pmpro_membership_add_user action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $user_id, $level_id, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ID of the user you assigned the membership to.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$level_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ID of the membership level you assigned to the user.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Add user to membership',
				'webhook_slug' => 'pmpro_membership_add_user',
				'steps' => array(
					WPWHPRO()->helpers->translate( "The second argument you need to set is <strong>user</strong>. Please set it to either the ID of the user or the email address.", $translation_ident ),
					WPWHPRO()->helpers->translate( "It is also required to set the <strong>level_id</strong> argument. Please set it to the id of the membership level you want to assign to your given user.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'pmpro_membership_add_user',
				'name'			  => WPWHPRO()->helpers->translate( 'Add user to membership', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'add a user to a membership', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Add a user to a given membership within "Paid Memberships Pro".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'paid-memberships-pro',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$translation_ident = "action-pmpro_membership_add_user-execute";
			$return_args = array(
				'success' => false,
				'msg' => '',
			);
	
			$user_id = null;
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$level_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'level_id' ) );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( is_numeric( $user ) ){
				$user_id = intval( $user );
			} else {
				$email = sanitize_email( $user );
				$user = get_user_by( 'email', $email );
				if( ! empty( $user ) ){
					if( ! empty( $user->ID ) ){
						$user_id = $user->ID;
					}
				}
			}

			if( empty( $user_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find any user for the value of the user argument.", $translation_ident );
				return $return_args;
			}

			if( empty( $level_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "It is required to set the level_id argument.", $translation_ident );
				return $return_args;
			}

			//Shorten circle
			$user_level = pmpro_getMembershipLevelForUser( $user_id );
			if( ! empty( $user_level ) && intval( $user_level->ID ) === $level_id ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user is already a member of the given level.", $translation_ident );
				return $return_args;
			}
	
			$level_change = pmpro_changeMembershipLevel( $level_id, $user_id );
			if ( $level_change === true ) {
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The level was successfuly assigned to the given user.", $translation_ident );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We have been unable to assign the user level due to an error within Paid Memberships Pro.", $translation_ident );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $user_id, $level_id, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.