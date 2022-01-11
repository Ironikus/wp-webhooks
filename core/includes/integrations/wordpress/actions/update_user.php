<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_update_user' ) ) :

	/**
	 * Load the create_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_update_user {

	/*
	 * The core logic to update an user via WP Webhooks
	 */
	public function get_details(){

		$translation_ident = "action-update-user-content";

		$parameter = array(
			'user_id'		   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email or user_login is defined) Include the numeric id of the user. (Note that the user_id has a higher priority than the user_email.)', $translation_ident ) ),
			'user_email'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_id or user_login is defined) Include the email correlated to the account.', $translation_ident ) ),
			'user_login'		=> array(
				'required' => true,
				'short_description' => WPWHPRO()->helpers->translate( '(Optional if user_email or user_id is defined) A string with which the user can log in to your site. This value can also be used ot update a user.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "This argument can also be used to identify a user by the login name. E.g. If you do not provide the user email or user id, we will try to fetch the user from the user login.", $translation_ident )
			),
			'first_name'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the user.', $translation_ident ) ),
			'last_name'		 => array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the user.', $translation_ident ) ),
			'nickname'		  => array( 'short_description' => WPWHPRO()->helpers->translate( 'The nickname. Please note that the nickname will be sanitized by WordPress automatically.', $translation_ident ) ),
			'display_name'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'The name that will be seen on the frontend of your site.', $translation_ident ) ),
			'user_nicename'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'A URL-friendly name. Default is user\' username.', $translation_ident ) ),
			'description'	   => array( 'short_description' => WPWHPRO()->helpers->translate( 'A description for the user that will be available on the profile page.', $translation_ident ) ),
			'rich_editing'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether the user should be able to use the Rich editor. Set it to "yes" or "no". Default "no".', $translation_ident ) ),
			'user_registered'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date the user got registered. Date structure: Y-m-d H:i:s', $translation_ident ) ),
			'user_url'		  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Include a website url.', $translation_ident ) ),
			'role'			  => array( 'short_description' => WPWHPRO()->helpers->translate( 'The main user role. If set, all additional roles are removed.', $translation_ident ) ),
			'additional_roles'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'This allows to add/remove multiple roles on a user. For more information, please read the description.', $translation_ident ) ),
			'user_pass'		 => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user password. If not defined, we don\'t generate a new one.', $translation_ident ) ),
			'user_meta'  		=> array( 'short_description' => WPWHPRO()->helpers->translate( '<strong>DEPRECATED! Please use manage_meta_data instead.</strong>', $translation_ident ) ),
			'manage_meta_data'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Manage your user-related meta data. Please see the description for further details.', $translation_ident ) ),
			'manage_acf_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Allows you to manage fields that are especially related to Advanced Custom Fields. See the description for further information.', $translation_ident ) ),
			'send_email'		=> array(
				'short_description' => WPWHPRO()->helpers->translate( 'Set this field to "yes" to send a email to the user with the data.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing his login details.", $translation_ident )
			),
			'do_action'		 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook. More infos are in the description.', $translation_ident ) ),
			'create_if_none'	=> array(
				'short_description' => WPWHPRO()->helpers->translate( 'Wether you want to create the user if it does not exists or not. Set it to "yes" or "no" Default is "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>create_if_none</strong> argument to <strong>yes</strong>, a user will be created with the given details in case it does not exist.", $translation_ident )
			)
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The slug of the role. The default roles have the following slugs:", $translation_ident ); ?>
<ul>
<li>administrator</li>
<li>editor</li>
<li>author</li>
<li>contributor</li>
<li>subscriber</li>
</ul>
<strong><?php echo WPWHPRO()->helpers->translate( "Important", $translation_ident ); ?></strong>: <?php echo WPWHPRO()->helpers->translate( "Please note that once you set this value while updating a user, all of your additional roles are removed from that user. This is a default WordPress behavior. If you don't want that, please only use the <strong>additional_roles</strong> argument and leave this one empty. (In case you set the argument <strong>create_if_none</strong> to <strong>yes</strong>, it will create the user with the default role. If you don't want that, simply remove that role within the <strong>additional_roles</strong> argument again).", $translation_ident ); ?>
		<?php
		$parameter['role']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add or remove additional roles on the user. There are two possible ways of doing that:", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong><br>
		<?php echo WPWHPRO()->helpers->translate( "This method allows you to add or remove the user roles using a simple string. To make it work, simply add the slug of the role and define the action (add/remove) after, separated by double points (:). If you want to add multiple roles, simply separate them with a semicolon (;). Please refer to the example down below.", $translation_ident ); ?>
		<pre>editor:add;custom-role:add;custom-role-1:remove</pre>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong><br>
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
<?php echo WPWHPRO()->helpers->translate( "This argument is specifically designed to add/update or remove user meta to your updated user.", $translation_ident ); ?>
					<br>
					<?php echo WPWHPRO()->helpers->translate( "To create/update or delete custom meta values, we offer you two different ways:", $translation_ident ); ?>
					<ol>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update or delete the user meta using a simple string. To make it work, separate the meta key from the value using a comma (,). To separate multiple meta settings from each other, simply separate them with a semicolon (;). To remove a meta value, simply set as a value <strong>ironikus-delete</strong>", $translation_ident ); ?>
							<pre>meta_key_1,meta_value_1;my_second_key,ironikus-delete</pre>
							<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT:</strong> Please note that if you want to use values that contain commas or semicolons, the string method does not work. In this case, please use the JSON method.", $translation_ident ); ?>
						</li>
						<li>
						<strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update or remove the user meta using a JSON formatted string. To make it work, add the meta key as the key and the meta value as the value. To delete a meta value, simply set the value to <strong>ironikus-delete</strong>. Here's an example on how this looks like:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!"
"third_meta_key": "ironikus-delete"
}</pre>
						</li>
					</ol>
					<strong><?php echo WPWHPRO()->helpers->translate( "Advanced", $translation_ident ); ?></strong>: <?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array/object serialization for single user meta values. This means, you can turn JSON into a serialized array or object.", $translation_ident ); ?>
					<br>
					<?php echo WPWHPRO()->helpers->translate( "As an example: The following JSON <code>{\"price\": \"100\"}</code> will turn into <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code> with default serialization or into <code>a:1:{s:5:\"price\";s:3:\"100\";}</code> with array serialization.", $translation_ident ); ?>
					<ol>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "Object serialization", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to serialize a JSON to an object using the default json_decode() function of PHP.", $translation_ident ); ?>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "To serialize your JSON to an object, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the user_meta argument: <code>ironikus-serialize</code>. Here's a full example:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize{\"price\": \"100\"}"
}</pre>
							<?php echo WPWHPRO()->helpers->translate( "This example will create three user meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the user_meta argument, needs to be escaped.", $translation_ident ); ?>
						</li>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "Array serialization", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to serialize a JSON to an array using the json_decode( \$json, true ) function of PHP.", $translation_ident ); ?>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "To serialize your JSON to an array, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the user_meta argument: <code>ironikus-serialize-array</code>. Here's a full example:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize-array{\"price\": \"100\"}"
}</pre>
							<?php echo WPWHPRO()->helpers->translate( "This example will create three user meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>a:1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize-array</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the user_meta argument, needs to be escaped.", $translation_ident ); ?>
						</li>
					</ol>
		<?php
		$parameter['user_meta']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument integrates the full features of managing user related meta values.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note</strong>: This argument is very powerful and requires some good understanding of JSON. It is integrated with the commonly used functions for managing user meta within WordPress. You can find a list of all avaialble functions here: ", $translation_ident ); ?>
<ul>
	<li><strong>add_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_user_meta/">https://developer.wordpress.org/reference/functions/add_user_meta/</a></li>
	<li><strong>update_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_user_meta/">https://developer.wordpress.org/reference/functions/update_user_meta/</a></li>
	<li><strong>delete_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_user_meta/">https://developer.wordpress.org/reference/functions/delete_user_meta/</a></li>
</ul>
<br>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a complete JSON example that shows you how to use each of the functions above.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array/object serialization for single user meta values. This means, you can turn JSON into a serialized array or object.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a JSON construct as an input. This construct contains each available function as a top-level key within the first layer and the assigned data respectively as a value. If you want to learn more about each line, please take a closer look at the bottom of the example.", $translation_ident ); ?>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a list that explains each of the top level keys.", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "add_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "add_user_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": { "some_array_key": "Some array Value" },
		"unique": true
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>add_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_user_meta/">https://developer.wordpress.org/reference/functions/add_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "In the example above, you will find two entries within the add_user_meta key. The first one shows the default behavior using only the meta key and the value. This causes the meta key to be created without checking upfront if it exists - that allows you to create the meta value multiple times.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "As seen in the second entry, you will find a third key called <strong>unique</strong> that allows you to check upfront if the meta key exists already. If it does, the meta entry is neither created, nor updated. Set the value to <strong>true</strong> to check against existing ones. Default: false", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "If you look closely to the second entry again, the value included is not a string, but a JSON construct, which is considered as an array and will therefore be serialized. The given value will be saved to the database in the following format: <code>a:1:{s:14:\"some_array_key\";s:16:\"Some array Value\";}</code>", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "update_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "update_user_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "The new value",
		"prev_value": "The previous value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>update_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_user_meta/">https://developer.wordpress.org/reference/functions/update_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "The example above shows you two entries for this function. The first one is the default set up thats used in most cases. Simply define the meta key and the meta value and the key will be updated if it does exist and if it does not exist, it will be created.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The third argument, as seen in the second entry, allows you to check against a previous value before updating. That causes that the meta value will only be updated if the previous key fits to whats currently saved within the database. Default: ''", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "delete_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "delete_user_meta":[
	  {
		"meta_key": "first_custom_key"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "Target specific value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>delete_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_user_meta/">https://developer.wordpress.org/reference/functions/delete_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "Within the example above, you will see that only the meta key is required for deleting an entry. This will cause all meta keys on this post with the same key to be deleted.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The second argument allows you to target only a specific meta key/value combination. This gets important if you want to target a specific meta key/value combination and not delete all available entries for the given post. Default: ''", $translation_ident ); ?>
	</li>
</ol>
<strong><?php echo WPWHPRO()->helpers->translate( "Some tipps:", $translation_ident ); ?></strong>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "You can include the value for this argument as a simple string to your webhook payload or you integrate it directly as JSON into your JSON payload (if you send a raw JSON response).", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Changing the order of the functions within the JSON causes the user meta to behave differently. If you, for example, add the <strong>delete_user_meta</strong> key before the <strong>update_user_meta</strong> key, the meta values will first be deleted and then added/updated.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "The webhook response contains a validted array that shows each initialized meta entry, as well as the response from its original WordPress function. This way you can see if the meta value was adjusted accordingly.", $translation_ident ); ?></li>
</ol>
		<?php
		$parameter['manage_meta_data']['description'] = ob_get_clean();

		//Remove if ACF isn't active
		if( ! WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			unset( $parameter['manage_acf_data'] );
		} else {
			ob_start();
			WPWHPRO()->acf->load_acf_description( $translation_ident );
			$parameter['manage_acf_data']['description'] = ob_get_clean();
		}

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>update_user</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user_data, $user_id, $user_meta, $update ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$user_data</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to update the user.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the user id of the updated user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_meta</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the unformatted user meta as you sent it over within the webhook request as a string.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$update</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "This value will be set to 'true' for the update_user webhook.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the user id with the key "user_id" and the user data with the key "user_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'User successfully updated.',
			'data' => 
			array (
			  'user_id' => 108,
			  'user_data' => 
			  array (
				'user_email' => 'newmail@demo.test',
				'ID' => 108,
				'display_name' => 'Jon Doe',
				'first_name' => 'Jon',
				'last_name' => 'Doe',
				'additional_roles' => false,
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Update a user',
			'webhook_slug' => 'update_user',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the users email address or the user id using the argument <strong>user_email/user_id</strong>. You can as well set both of them, but in this case, the user id has a higher priority than the email for fetching the user.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'update_user',
			'name'			  => WPWHPRO()->helpers->translate( 'Update user', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'update a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Update a user on your WordPress website or network.', 'action-create-user-content' ),
			'description'	   => $description,
			'integration'	   => 'wordpress',
			'premium' 			=> true,
		);

	}

	}

endif; // End if class_exists check.