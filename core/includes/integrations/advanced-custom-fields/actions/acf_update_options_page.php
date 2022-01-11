<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_update_options_page' ) ) :

	/**
	 * Load the acf_update_options_page action
	 *
	 * @since 4.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_update_options_page {

	public function get_details(){

		$translation_ident = "action-acf_update_options_page-description";

		$parameter = array(
			'manage_acf_data' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A JSON formatted string containing the values you want to add, update, delete. See the details for further information.', $translation_ident ) ),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The adjusted meta data, includnig the response of the related ACF function." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The given ACF data has been successfully executed.',
			'data' => 
			array (
			  'update_field' => 
			  array (
				0 => 
				array (
				  'selector' => 'your_text_field',
				  'value' => 'Some custom value',
				  'response' => true,
				),
			  ),
			),
		);

		ob_start();
		WPWHPRO()->acf->load_acf_description( $translation_ident );
		$parameter['manage_acf_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the acf_update_options_page action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $manage_acf_data, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$manage_acf_data</strong> (String)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ACF data that was sent by the webhook caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Update options page',
				'webhook_slug' => 'acf_update_options_page',
				'steps' => array(
					WPWHPRO()->helpers->translate( "The second argument you need to set is <strong>manage_acf_data</strong>. It accepts a JSON formatted string or array as seen within the details section of the argument.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'acf_update_options_page',
				'name'			  => WPWHPRO()->helpers->translate( 'Update options page', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'update an options page', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Update custom options page data within "Advanced Custom Fields".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'advanced-custom-fields',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.