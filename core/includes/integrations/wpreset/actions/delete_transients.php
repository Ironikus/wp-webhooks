<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_delete_transients' ) ) :

	/**
	 * Load the delete_transients action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_delete_transients {

	public function get_details(){

		$translation_ident = "action-delete_transients-content";

			$parameter = array(
				'confirm'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Please set this value to "yes". If not set, no transients are deleted.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) Count of all the deleted transients.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_transients</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $confirm, $count ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$confirm</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "Whether you confirmed the deletion or not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$count</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The number of deleted transients.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Transients successfully deleted.',
			'data' => 
			array (
				'count' => 26,
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Delete transients',
			'webhook_slug' => 'delete_transients',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>confirm</strong>, which by default is set to "no". This is a secondary security measurement to make sure you do not accidentially delete your transients.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'delete_transients', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Delete transients', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'delete all transients', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Delete all transients on your website using webhooks.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wpreset'
		);


		}

		public function execute( $return_data, $response_body ){

			$reset_helpers = WPWHPRO()->integrations->get_helper( 'wpreset', 'reset_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'count' => 0
				)
			);

			$confirm		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				$count = $reset_helpers->get_wp_reset()->do_delete_transients();

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Transients successfully deleted.", 'action-delete_transients-success' );
				$return_args['data']['count'] = $count;

			} else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: No transients deleted. You did not set the confirmation parameter.", 'action-delete_transients-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $count );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.