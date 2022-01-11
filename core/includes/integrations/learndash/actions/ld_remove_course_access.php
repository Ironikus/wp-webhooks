<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_remove_course_access' ) ) :

	/**
	 * Load the ld_remove_course_access action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_remove_course_access {

	public function get_details(){

		$translation_ident = "action-ld_remove_course_access-content";

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id (or user email) of the user you want to remove the course access from.', $translation_ident ) ),
				'course_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the course IDs of the courses you want to remove the access for. This argument accepts the value "all" to remove access to all courses of the user, a single course id, or a comma-separated string of course IDs.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the value 'all' to set all courses as completed, a single course id, as well as multiple course ids, separated by commas (Multiple course ids will set all the courses to completed for the given course of the specified user):", $translation_ident ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['course_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_remove_course_access</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array(
			'success' => true,
			'msg' => 'The course access has been successfully removed.',
			'data' => 
			array (
			  'user_id' => 109,
			  'course_ids' => '8053',
			  'removed_access' => 
			  array (
				8053 => 
				array (
				  'user_id' => 109,
				  'course_id' => 8053,
				  'response' => true,
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Remove course access',
			'webhook_slug' => 'ld_remove_course_access',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user_id</strong> argument. You can either set it to the user id or the user email of which you want to remove the course access for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>course_ids</strong> argument to one or multiple ids of the courses you want to remove access from the given user.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ld_remove_course_access', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Remove course access', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'remove course access from a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Remove one or multiple course access for a user within Learndash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

	}

endif; // End if class_exists check.