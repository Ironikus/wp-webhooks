<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_delete_themes' ) ) :

	/**
	 * Load the delete_themes action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_delete_themes {

		public function get_details(){

			$translation_ident = "action-delete_themes-content";

			$parameter = array(
				'confirm'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Please set this value to "yes". If not set, no theme will be deleted.', $translation_ident ) ),
				'keep_default_theme' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether to keep the default theme or not. Possible values: "yes" and "no". Default: "yes"', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the webhook action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$confirm</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "Returns true if the confirm argument was set correctly and false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$count</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the number of deleted themes.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) Count of all the deleted themes.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Themes successfully deleted.',
				'data' => 
				array (
				  'count' => 2,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete themes',
				'webhook_slug' => 'delete_themes',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>confirm</strong>, which is needed to confirm that you really want to delete all themes as the action is irreversible.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'It uses the WP Reset function <strong>do_delete_themes()</strong> to delete custom tables.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'delete_themes', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Delete themes', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'delete all themes', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Delete all themes on your website using webhooks.', $translation_ident ),
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

			$confirm			= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$keep_default_theme = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_default_theme' ) == 'no' ) ? false : true;
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				if (!function_exists('delete_theme')) {
					require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
				}

				$count = $reset_helpers->get_wp_reset()->do_delete_themes( $keep_default_theme );

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Themes successfully deleted.", 'action-delete_themes-success' );
				$return_args['data']['count'] = $count;

			} else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: Themes not deleted. You did not set the confirmation parameter.", 'action-delete_themes-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $count );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.