<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_bulk_webhooks' ) ) :

	/**
	 * Load the bulk_webhooks action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_bulk_webhooks {

		function __construct(){
			$this->page_title   = WPWHPRO()->settings->get_page_title();
		}

		/*
	 * The core logic to use a bulk action webhook
	 */
	public function get_details(){

		$translation_ident = 'action-ironikus-bulk_webhooks-content';
		$trigger_settings = WPWHPRO()->settings->get_required_trigger_settings();
		$authentication_templates = WPWHPRO()->auth->get_auth_templates();

		$parameter = array(
			'actions'	   => array( 'short_description' => WPWHPRO()->helpers->translate( 'This argument contains all of your executable webhook calls and settings.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON construct that allows you to register multiple webhooks, which will then be executed in the given order. Each of the row acts as a separate webhook call with all of the available settings and configurations.", $translation_ident ); ?>
<pre>{
  "first_webhook_call": {
	  "http_arguments": {
		  "sslverify": false
	  },
	  "webhook_url": null,
	  "webhook_name": "bulk_actions",
	  "webhook_status": "active",
	  "webhook_settings": {
		  "wpwhpro_trigger_allow_unverified_ssl": 1,
		  "wpwhpro_trigger_allow_unsafe_urls": 1
	  },
	  "payload_data": {
		  "action": "ironikus_test",
		  "test_var": "test-value123"
	  }
  },
  "second_webhook_call": {
	  "payload_data": {
		  "action": "ironikus_test",
		  "test_var": "test-value123"
	  }
  }
}</pre>
<?php echo WPWHPRO()->helpers->translate( "The JSON can contain multiple webhook calls that are marked via the top level key within the JSON (first_webhook_call, second_webhook_call, ...). This top-level-key indicates the webhook you want to fire and is later used within the response to add the response for that call. Down below you will find an explanation on each of the available settings.", $translation_ident ); ?>
<ol>
	<li>
		<strong>http_arguments</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "This key accepts an array containing multiple arguments from the WP_Http object within WordPress. You can take a look at the argumet list by visiting on the following link:", $translation_ident ); ?> <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/classes/WP_Http/request/">https://developer.wordpress.org/reference/classes/WP_Http/request/</a>
	</li>
	<li>
		<strong>webhook_url</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "This contains the webhook URL you want to send the request to. By default, it is set to the same webhook URL you are sending this webhook action call to. You can also define external URL's here and send data out of WordPress.", $translation_ident ); ?>
	</li>
	<li>
		<strong>webhook_name</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The name as an identicator when you sent the webhook. By default, it is set to <strong>bulk_actions</strong>. This value will be sent over to the webhook call within the header as well.", $translation_ident ); ?>
	</li>
	<li>
		<strong>webhook_status</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Use this argumet to prevent the webhook from being sent in the first place. This allows you to temporarily deactivate the call instead of removing it completely fromthe JSON. To deactivate it, please set it to <strong>inactive</strong>. Default: <strong>active</strong>", $translation_ident ); ?>
	</li>
	<li>
		<strong>webhook_settings</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "This powerful argument allows you to assign All the settings features, that are available for triggers, for ANY webhook call. That means you can even assign authentication and data mapping templates to triggers just for reformatting the data. Down below, you will find a list with all default trigger settings and its possible values:", $translation_ident ); ?>
		<ol>
			<?php
				foreach( $trigger_settings as $setting => $setting_data ){
					$value = '';
					$type = 'unknown';

					if( isset( $setting_data['type'] ) ){
						$type = $setting_data['type'];

						if( $setting_data['type'] === 'select' ){

							$choices = $setting_data['choices'];

							if( $setting === 'wpwhpro_trigger_authentication' ){
								$choices = array_replace( $choices, WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
							}

							$value .= '<ul class="pl-3">';
							foreach( $choices as $ck => $cv ){
								$value .= '<li><strong>' . sanitize_title( $ck ) . '</strong> (' . sanitize_text_field( $cv ) . ')' . '</li>';
							}
							$value .= '</ul>';

						} elseif( $setting_data['type'] === 'checkbox' ){
							$value .= '<ul class="pl-3">';
							$value .= '<li>0</li>';
							$value .= '<li>1</li>';
							$value .= '</ul>';
						}
					}


					echo '<li>';
					echo '<strong>' . sanitize_title( $setting ) . '</strong> (' . WPWHPRO()->helpers->translate( "Type", $translation_ident ) . ' ' . $type . '): ';
					echo $value;
					echo '</li>';
				}
			?>
		</ol>
	</li>
	<li>
		<strong>payload_data</strong> (mixed)<br>
		<?php echo WPWHPRO()->helpers->translate( "This key contains all of the actual data you would like to send to this specific webhook call.", $translation_ident ); ?>
	</li>
</ol>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the bulk_webhooks action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $actions, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$actions</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the validated data from the <code>actions</code> argument.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the full response that is sent back to the webhook caller.s", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['actions']['description'] = ob_get_clean();

		$returns = array(
			'actions'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A list of all executed actions and their responses.', $translation_ident ) ),
		);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'actions' => ''
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Fire multiple webhooks',
				'webhook_slug' => 'bulk_webhooks',
				'before_description' => '<p>' . WPWHPRO()->helpers->translate( 'This webhook enables you to use the full finctionality of the woocommerce REST API. It also works with all integrated extensions like <strong>Woocommerce Memberships</strong>, <strong>Woocommerce Subscriptions</strong> or <strong>Woocommerce Bookings</strong>.', 'action-create_woocommerce_order-content' ) . '<br>' . WPWHPRO()->helpers->translate( 'You can also add a custom action, that fires after the webhook was called. Simply specify your webhook identifier (e.g. my_csutom_webhook) and call it within your theme or plugin (e.g. add_action( "my_csutom_webhook", "my_csutom_webhook_callback" ) ).', $translation_ident ) . '</p>',
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To make the most out of this webhook endpoint, please take a look at the <strong>Arguments</strong> list.", $translation_ident ),
					WPWHPRO()->helpers->translate( "You can send data to internal and external URL's. There is no limitation to the system.", $translation_ident ),
					WPWHPRO()->helpers->translate( "This action integrates all available feature of this plugin. You can also connect data mapping templates, as well as authentication templates and much more.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'bulk_webhooks',
				'name'			  => WPWHPRO()->helpers->translate( 'Fire multiple webhooks', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'fire multiple webhooks within one request', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Execute multiple webhooks within a single webhook call.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium'		   => true,
			);

		}

	}

endif; // End if class_exists check.