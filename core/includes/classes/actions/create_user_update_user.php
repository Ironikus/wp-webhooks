<?php
if ( ! class_exists( 'WP_Webhooks_Action_create_update_user' ) ) :

	/**
	 * Load the create_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Action_create_update_user {

		function __construct(){
			$this->page_name    = WPWHPRO()->settings->get_page_name();
			$this->page_title   = WPWHPRO()->settings->get_page_title();

            add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_action_details' ), 10 );
			add_filter( 'wpwhpro/webhooks/add_webhook_actions', array( $this, 'add_action_callback' ), 1000, 4 );
        }

		public function add_action_details( $actions ){

			$actions['create_user'] = $this->action_create_user_content();

			return $actions;
		}

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function add_action_callback( $response, $action, $response_ident_value, $response_api_key ){

			switch( $action ){
				case 'create_user':
					$response = $this->action_create_user();
					break;
			}

			return $response;
		}

		/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_user_content(){

		$translation_ident = 'action-create-user-content';

		$parameter = array(
			'user_email'        => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'This field is required. Include the email for the user.', $translation_ident ) ),
			'first_name'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the user.', $translation_ident ) ),
			'last_name'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the user.', $translation_ident ) ),
			'nickname'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'The nickname. Please note that the nickname will be sanitized by WordPress automatically.', $translation_ident ) ),
			'user_login'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'A string with which the user can log in to your site.', $translation_ident ) ),
			'display_name'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'The name that will be seen on the frontend of your site.', $translation_ident ) ),
			'user_nicename'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'A URL-friendly name. Default is user\' username.', $translation_ident ) ),
			'description'       => array( 'short_description' => WPWHPRO()->helpers->translate( 'A description for the user that will be available on the profile page.', $translation_ident ) ),
			'rich_editing'      => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether the user should be able to use the Rich editor. Set it to "yes" or "no". Default "no".', $translation_ident ) ),
			'user_registered'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date the user gets registered. Date structure: Y-m-d H:i:s', $translation_ident ) ),
			'user_url'          => array( 'short_description' => WPWHPRO()->helpers->translate( 'Include a website url.', $translation_ident ) ),
			'role'              => array(
				'short_description' => WPWHPRO()->helpers->translate( 'The main user role. If not set, default is subscriber.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "<p>The slug of the role. The default roles have the following slugs: </p>", $translation_ident ) . '<p><ul><li>administrator</li> <li>editor</li> <li>author</li> <li>contributor</li> <li>subscriber</li></ul></p>',
			),
			'additional_roles'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'This allows to add multiple roles to a user.', $translation_ident ) ),
			'user_pass'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user password. If not defined, we generate a 32 character long password dynamically.', $translation_ident ) ),
			'send_email'        => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user with the data.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing his login details.", $translation_ident )
			),
			'do_action'         => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add or remove additional roles on the user. There are two possible ways of doing that:", $translation_ident ); ?>
<ol>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add or remove the user roles using a simple string. To make it work, simply add the slug of the role and define the action (add/remove) after, separated by double points (:). If you want to add multiple roles, simply separate them with a semicolon (;). Please refer to the example down below.", $translation_ident ); ?>
        <pre>editor:add;custom-role:add;custom-role-1:remove</pre>
    </li>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "We also support a JSON formatted string, which contains the role slug as the JSON key and the action (add/remove) as the value. Please refer to the example below:", $translation_ident ); ?>
        <pre>{
  "editor": "add",
  "custom-role": "add",
  "custom-role-1": "remove"
}</pre>
    </li>
</ol>
		<?php
		$parameter['additional_roles']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the create_user action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user_data, $user_id, $update ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$user_data</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to create the user.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the user id of the newly created user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$update</strong> (bool)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This value will be set to 'false' for the create_user webhook.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user data with the key "user_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
        );

		$return_code_data = array(
			"success" => true,
			"msg" => "User successfully created.",
			"data" => array(
				"user_id" => 131,
				"user_data" => array(
					"user_email" => "demo_user@email.email",
					"role" => "subscriber",
					"nickname" => "nickname",
					"user_login" => "userlogin",
					"user_nicename" => "The Nice Name",
					"description" => "This is a user description",
					"rich_editing" => true,
					"user_registered" => "2020-12-11 14:10:10",
					"user_url" => "https://somedomain.com",
					"display_name" => "username",
					"first_name" => "Jon",
					"last_name" => "Doe",
					"user_pass" => "SomeCustomUserpass123",
					"additional_roles" => "author:add"
				)
			)
		);
		ob_start();
			echo WPWHPRO()->helpers->display_var( $return_code_data );
		$returns_code = ob_get_clean();

		ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a user on your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>create_user</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>create_user</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>create_user</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set an email address using the argument <strong>user_email</strong>. This should be the email address of the user you want to create.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the user. We would still recommend to set the attribute <strong>user_login</strong>, since this will be the name a user can log in with.", $translation_ident ); ?></li>
</ol>
<?php
		$description = ob_get_clean();

		return array(
			'action'            => 'create_user',
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => '<pre>' . $returns_code . '</pre>',
			'short_description' => WPWHPRO()->helpers->translate( 'Create a new user via webhooks.', $translation_ident ),
			'description'       => $description
		);

	}

		/**
		 * Create a user via a action call
		 *
		 * @param $update - Wether the user gets created or updated
		 */
		public function action_create_user(){

			$response_body = WPWHPRO()->helpers->get_response_body();
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'user_data' => array()
				)
			);

			$user_id            = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$nickname           = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'nickname' ) );
			$user_login         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_login' ) );
			$user_nicename      = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_nicename' ) );
			$description        = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' ) );
			$user_registered    = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_registered' ) );
			$user_url           = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_url' ) );
			$display_name       = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'display_name' ) );
			$user_email         = sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' ) );
			$first_name         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' ) );
			$last_name          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' ) );
			$role               = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'role' ) );
			$user_pass          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_pass' );
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			$rich_editing     	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'rich_editing' ) == 'yes' ) ? true : false;
			$send_email         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
			$additional_roles   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'additional_roles' );

			if ( empty( $user_email ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate("An email is required to create a user.", 'action-create-user-success' );

				return $return_args;
			}

			$user_data = array();

			if( $user_email ){
				$user_data['user_email'] = $user_email;
			}

			$dynamic_user_login = apply_filters( 'wpwhpro/run/create_action_user_login', false );
			if ( empty( $user_login ) && $dynamic_user_login ) {
				$user_login = WPWHPRO()->helpers->create_random_unique_username( $user_email, 'user_' );
			}

			//Define on new user
			if( ! empty( $role ) ){
				$user_data['role'] = 'subscriber';
			}

			//Auto generate on new user
			if( empty( $user_pass ) ){
				$user_data['user_pass'] = wp_generate_password( 32, true, false );
			}

			if( ! empty( $nickname ) ){
				$user_data['nickname'] = $nickname;
			}

			if( ! empty( $user_login ) ){
				$user_data['user_login'] = $user_login;
			} else {
				$user_data['user_login'] = sanitize_title( $user_email );
			}

			if( ! empty( $user_nicename ) ){
				$user_data['user_nicename'] = $user_nicename;
			}

			if( ! empty( $description ) ){
				$user_data['description'] = $description;
			}

			if( ! empty( $rich_editing ) ){
				$user_data['rich_editing'] = $rich_editing;
			}

			if( ! empty( $user_registered ) ){
				$user_data['user_registered'] = $user_registered;
			}

			if( ! empty( $user_url ) ){
				$user_data['user_url'] = $user_url;
			}

			if( ! empty( $display_name ) ){
				$user_data['display_name'] = $display_name;
			}

			if( ! empty( $first_name ) ){
				$user_data['first_name'] = $first_name;
			}

			if( ! empty( $last_name ) ){
				$user_data['last_name'] = $last_name;
			}

			if( ! empty( $role ) ){
				$user_data['role'] = $role;
			}

			if( ! empty( $user_pass ) ){
				$user_data['user_pass'] = $user_pass;
			}

			$user_id = wp_insert_user( $user_data );

			if ( ! is_wp_error( $user_id ) && is_numeric( $user_id ) ) {

				//Manage user roles
				if( ! empty( $additional_roles ) ){

					$wpwh_current_user = new WP_User( $user_id );

					if( WPWHPRO()->helpers->is_json( $additional_roles ) ){

						$additional_roles_meta_data = json_decode( $additional_roles, true );
						foreach( $additional_roles_meta_data as $sarole => $sastatus ){

							switch( $sastatus ){
								case 'add':
									$wpwh_current_user->add_role( sanitize_text_field( $sarole ) );
								break;
								case 'remove':
									$wpwh_current_user->remove_role( sanitize_text_field( $sarole ) );
								break;
							}

						}

					} else {

						$additional_roles_data = explode( ';', trim( $additional_roles, ';' ) );
						foreach( $additional_roles_data as $single_additional_role ){

							$additional_roles_data = explode( ':', trim( $single_additional_role, ':' ) );
							if(
								! empty( $additional_roles_data )
								&& is_array( $additional_roles_data )
								&& ! empty( $additional_roles_data[0] )
								&& ! empty( $additional_roles_data[1] )
							){

								switch( $additional_roles_data[1] ){
									case 'add':
										$wpwh_current_user->add_role( sanitize_text_field( $additional_roles_data[0] ) );
									break;
									case 'remove':
										$wpwh_current_user->remove_role( sanitize_text_field( $additional_roles_data[0] ) );
									break;
								}

							}
						}

					}

				}

				//Map additional roles to user data
				$user_data['additional_roles'] = $additional_roles;

				$return_args['msg'] = WPWHPRO()->helpers->translate("User successfully created.", 'action-create-user-success' );

				$return_args['success'] = true;
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['user_data'] = $user_data;

				if( apply_filters( 'wpwhpro/run/create_action_user_email_notification', true ) && $send_email == 'yes' ){
					wp_new_user_notification( $user_id, null, 'both' );
				}
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("An error occured while creating the user. Please check the response for more details.", 'action-create-user-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['user_data'] = $user_data;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $user_data, $user_id );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.