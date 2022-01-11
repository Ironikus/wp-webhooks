<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_delete_plugins' ) ) :

	/**
	 * Load the delete_plugins action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_delete_plugins {

		public function get_details(){

			$translation_ident = "action-delete_plugins-content";

			$parameter = array(
				'confirm'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Please set this value to "yes". If not set, no plugin will be deleted.', $translation_ident ) ),
				'keep_wp_reset'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Wether WP Reset should be deleted as well or not. Possible values: "yes" and "no". Default: "yes"', $translation_ident ) ),
				'silent_deactivate'  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Skip individual plugin deactivation functions when deactivating. Possible values: "yes" and "no". Default: "no"', $translation_ident ) ),
				'do_action'		  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
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
		<?php echo WPWHPRO()->helpers->translate( "Contains the number of deleted plugins.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) Count of all the deleted plugins.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Plugins successfully deleted.',
				'data' => 
				array (
				  'count' => 14,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete plugins',
				'webhook_slug' => 'delete_plugins',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>confirm</strong>, which is needed to confirm that you really want to delete all plugins as the action is irreversible.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'It uses the WP Reset function <strong>do_delete_plugins()</strong> to clean the folder.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'delete_plugins', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Delete plugins', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'delete all plugins', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Delete all plugins on your website using webhooks.', $translation_ident ),
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
			$keep_wp_reset	  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_wp_reset' ) == 'no' ) ? false : true;
			$silent_deactivate  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'silent_deactivate' ) == 'yes' ) ? true : false;
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				if (!function_exists('request_filesystem_credentials')) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				$count = $reset_helpers->get_wp_reset()->do_delete_plugins( $keep_wp_reset, $silent_deactivate );

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Plugins successfully deleted.", 'action-delete_plugins-success' );
				$return_args['data']['count'] = $count;

			} else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: Plugins not deleted. You did not set the confirmation parameter.", 'action-delete_plugins-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $count );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.