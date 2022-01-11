<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_contact_remove_list' ) ) :

	/**
	 * Load the fcrm_contact_remove_list action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_contact_remove_list {

	public function get_details(){

		$translation_ident = "action-fcrm_contact_remove_list-content";

			$parameter = array(
				'email'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set the email of the contact/user you want to remove the lists from.', $translation_ident ) ),
				'user_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'In case you did not set the email, you can also assign the user via a given user id.', $translation_ident ) ),
				'lists'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the lists you want to remove from the user. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to remove multiple lists from the contact, you can either comma-separate them like <code>2,3,12,44</code>, or you can remove them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['lists']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fcrm_contact_remove_list</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $validated_user_email, $contact, $validated_lists ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$validated_user_email</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The email of the contact we removed the lists from.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$contact</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Further data about the contact.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$validated_lists</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array of the lists that have been removed from the user.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
            'success' => true,
            'msg' => 'Lists have been removed from the given contact.',
            'data' => 
            array (
              'contact' => 
              array (
                'id' => '1',
                'user_id' => NULL,
                'hash' => 'c152149c03d10e23c036edbaXXXXXXX',
                'contact_owner' => NULL,
                'company_id' => NULL,
                'prefix' => 'Mr',
                'first_name' => 'Jon',
                'last_name' => 'Doe',
                'email' => 'jon.doe@demodomain.test',
                'timezone' => NULL,
                'address_line_1' => '',
                'address_line_2' => '',
                'postal_code' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'ip' => NULL,
                'latitude' => NULL,
                'longitude' => NULL,
                'total_points' => '0',
                'life_time_value' => '0',
                'phone' => '123456789',
                'status' => 'subscribed',
                'contact_type' => 'lead',
                'source' => NULL,
                'avatar' => NULL,
                'date_of_birth' => '1999-11-11',
                'created_at' => '2021-11-30 20:40:50',
                'last_activity' => NULL,
                'updated_at' => '2021-11-30 21:10:32',
                'photo' => 'https://www.gravatar.com/avatar/c152149c03d10e23c036edba08f95775?s=128',
                'full_name' => 'Jon Doe',
                'lists' => 
                array (
                  0 => 
                  array (
                    'id' => '1',
                    'title' => 'Demo Tag 1',
                    'slug' => 'demo-tag-1',
                    'description' => '',
                    'created_at' => '2021-12-01 10:22:36',
                    'updated_at' => '2021-12-01 10:22:36',
                    'pivot' => 
                    array (
                      'subscriber_id' => '1',
                      'object_id' => '1',
                      'object_type' => 'FluentCrm\\App\\Models\\Tag',
                      'created_at' => '2021-12-01 13:30:37',
                      'updated_at' => '2021-12-01 13:30:37',
                    ),
                  ),
                  1 => 
                  array (
                    'id' => '2',
                    'title' => 'Demo Tag 2',
                    'slug' => 'demo-tag-2',
                    'description' => '',
                    'created_at' => '2021-12-01 10:22:44',
                    'updated_at' => '2021-12-01 10:22:44',
                    'pivot' => 
                    array (
                      'subscriber_id' => '1',
                      'object_id' => '2',
                      'object_type' => 'FluentCrm\\App\\Models\\Tag',
                      'created_at' => '2021-12-01 13:28:27',
                      'updated_at' => '2021-12-01 13:28:27',
                    ),
                  ),
                ),
              ),
            ),
        );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Remove lists from contact',
			'webhook_slug' => 'fcrm_contact_remove_list',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set either the <strong>email</strong> argument or the <strong>user_id</strong> argument. Please set it to the user/contact email or the user id. Please note that the user id will only work if your contact is connected to a user.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>lists</strong> argument. This argument accepts a comma-separated list of tag ids, as well as a JSON with each id on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'fcrm_contact_remove_list', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Remove lists from contact', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'remove one or multiple lists from a contact', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Remove one or multiple lists from a contact within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
            'premium'		   => true,
		);


		}

	}

endif; // End if class_exists check.