<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_get' ) ) :

	/**
	 * Load the pmpro_membership_get action
	 *
	 * @since 4.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_get {

	public function get_details(){

		$translation_ident = "action-pmpro_membership_get-description";

		$parameter = array(
			'user' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID or email of the user you want to get the membership level from.', $translation_ident ) ),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'membership' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Every data related to the assigned membership." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The memberships have been returned successfully.',
			'membership' => 
			array (
			  'ID' => '2',
			  'id' => '2',
			  'subscription_id' => '13',
			  'name' => 'Second Level',
			  'description' => 'the second level',
			  'confirmation' => '',
			  'expiration_number' => '0',
			  'expiration_period' => '',
			  'allow_signups' => '1',
			  'initial_payment' => 0,
			  'billing_amount' => 0,
			  'cycle_number' => '0',
			  'cycle_period' => '',
			  'billing_limit' => '0',
			  'trial_amount' => 0,
			  'trial_limit' => '0',
			  'code_id' => '0',
			  'startdate' => '1626965948',
			  'enddate' => NULL,
			),
		);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the pmpro_membership_get action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $user_id, $user_level, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ID of the user you get the membership level from.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_level</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The assigned user level.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Get user membership',
				'webhook_slug' => 'pmpro_membership_get',
				'steps' => array(
					WPWHPRO()->helpers->translate( "The second argument you need to set is <strong>user</strong>. Please set it to either the ID of the user or the email address.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'pmpro_membership_get',
				'name'			  => WPWHPRO()->helpers->translate( 'Get user membership', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'get a user membership', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Get the current membership of a user within "Paid Memberships Pro".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'paid-memberships-pro',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.