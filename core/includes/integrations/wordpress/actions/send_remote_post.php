<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_send_remote_post' ) ) :

	/**
	 * Load the send_remote_post action
	 *
	 * @since 3.3.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_send_remote_post {

		public function get_details(){

			$translation_ident = "action-send_remote_post-description";

			//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'url'	   => array( 'required' => true, 'multiple' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) A URL you want to send the data to. Our actions URLs are supported too.', 'action-send_remote_post-content' ) ),
				'method'	=> array( 'required' => true, 'default_value' => 'POST', 'type' => 'select', 'choices' => array(
					'POST' => array( 'label' => 'POST' ),
					'GET' => array( 'label' => 'GET' ),
					'HEAD' => array( 'label' => 'HEAD' ),
					'PUT' => array( 'label' => 'PUT' ),
					'DELETE' => array( 'label' => 'DELETE' ),
					'TRACE' => array( 'label' => 'TRACE' ),
					'OPTIONS' => array( 'label' => 'OPTIONS' ),
					'PATCH' => array( 'label' => 'PATCH' ),
				), 'short_description' => WPWHPRO()->helpers->translate( '(string) The request type used to send the request.', 'action-send_remote_post-content' ) ),
				'headers'	   => array( 'type' => 'repeater', 'multiple' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing further header details.', 'action-send_remote_post-content' ) ),
				'payload'	   => array( 'type' => 'repeater', 'variable' => false, 'multiple' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing further payoad data.', 'action-send_remote_post-content' ) ),
				'timeout'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(integer) Filters the timeout value for an HTTP request. Default: 5', 'action-send_remote_post-content' ) ),
				'redirection'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(integer) Filters the number of redirects allowed during an HTTP request. Default 5', 'action-send_remote_post-content' ) ),
				'httpversion'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Filters the version of the HTTP protocol used in a request. Default: 1.0', 'action-send_remote_post-content' ) ),
				'user-agent'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Filters the user agent value sent with an HTTP request.', 'action-send_remote_post-content' ) ),
				'blocking'	=> array( 
					'type' => 'select', 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', 'action-send_remote_post-content' ),
						'no' => WPWHPRO()->helpers->translate( 'No', 'action-send_remote_post-content' ),
					), 
					'short_description' => WPWHPRO()->helpers->translate( '(bool) Filter whether to wait for a response of the recipient or not. Default: true', 'action-send_remote_post-content' ) 
				),
				'reject_unsafe_urls'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(bool) Filters whether to pass URLs through wp_http_validate_url() in an HTTP request. Default: no', 'action-send_remote_post-content' ) ),
				'sslverify'	=> array( 
					'type' => 'select', 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', 'action-send_remote_post-content' ),
						'no' => WPWHPRO()->helpers->translate( 'No', 'action-send_remote_post-content' ),
					), 'short_description' => WPWHPRO()->helpers->translate( '(string) Validates the senders SSL certificate before sending the data. Default: no', 'action-send_remote_post-content' ) ),
				'limit_response_size'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(integer) Limit the response size of the data coming back from the recpient. Default: null', 'action-send_remote_post-content' ) ),
				'cookies'	   => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing additional cookie data.', 'action-send_remote_post-content' ) ),
				'do_action'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the webhook fires.', 'action-send_remote_post-content' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-send_remote_post-content' ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-send_remote_post-content' ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further details about the sent data.', 'action-send_remote_post-content' ) ),
			);

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The header argument accepts a JSON formatted string, containing additional header information. Down below you will find an example using two simple header settings:", $translation_ident ); ?>
			<pre>{
  "Content-Type": "application/json",
  "Custom-Header": "Some demo header"
}</pre>
			<?php
			$parameter['headers']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The payload argument accepts a JSON formatted string, containing your main information. Down below you will find an example for the payload:", $translation_ident ); ?>
			<pre>{
  "user-email": "jon@doe.test",
  "user-name": "Jon Doe"
}</pre>
			<?php
			$parameter['payload']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The cookies argument accepts a JSON formatted string, containing further cookie information. Down below you will find an example for the payload:", $translation_ident ); ?>
			<pre>{
  "test-cookie": "The Test Cookie"
}</pre>
			<?php
			$parameter['cookies']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to change the mehtod of this request. Default is POST. Down below you will find a list of all available request methods:", $translation_ident ); ?>
<ul>
	<li>POST</li>
	<li>GET</li>
	<li>HEAD</li>
	<li>PUT</li>
	<li>DELETE</li>
	<li>TRACE</li>
	<li>OPTIONS</li>
	<li>PATCH</li>
</ul>
		<?php
		$parameter['method']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to either send the request synchronously (waiting for a response) or asynchronously (response will be empty).", $translation_ident ); ?>
		<?php
		$parameter['blocking']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to false to use unsafe looking URLs like zfvshjhfbssdf.szfdhdf.com.", $translation_ident ); ?>
		<?php
		$parameter['reject_unsafe_urls']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to no to use unverified SSL connections for this URL.", $translation_ident ); ?>
		<?php
		$parameter['sslverify']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>send_remote_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $check, $arguments, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "Returns the HTTP object if the request was successful - WP Error or false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$arguments</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The arguments used to send the HTTP request.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the response data of the request.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'data' => 
				array (
				  'headers' => 
				  array (
				  ),
				  'body' => '{"some_key":"The response was successful"}',
				  'response' => 
				  array (
					'code' => 200,
					'message' => 'OK',
				  ),
				  'cookies' => 
				  array (
					0 => 
					array (
					  'name' => 'laravel_session',
					  'value' => '4hfXTJvekTA8kMXsZO6rL9pWF7hqHGxESj8Y3CJI',
					  'expires' => 1633887216,
					  'path' => '/',
					  'domain' => 'webhook.site',
					  'host_only' => true,
					),
				  ),
				  'filename' => NULL,
				  'http_response' => 
				  array (
					'data' => NULL,
					'headers' => NULL,
					'status' => NULL,
				  ),
				),
				'msg' => 'The request was sent successfully.',
			  );

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Send remote POST',
				'webhook_slug' => 'send_remote_post',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>url</strong>. Please set it to the recipient URL that should receive the request.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "In case the requests fails, it's highly suggested to take a closer look at the <strong>sslverify</strong> and <strong>reject_unsafe_urls</strong> arguments.", $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'send_remote_post',
				'name'			  => WPWHPRO()->helpers->translate( 'Send remote HTTP POST', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'send a remote HTTP request', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to send a HTTP request from your WordPress site.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.