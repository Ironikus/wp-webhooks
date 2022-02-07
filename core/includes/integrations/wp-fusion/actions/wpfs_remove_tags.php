<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_remove_tags' ) ) :

	/**
	 * Load the wpfs_remove_tags action
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_remove_tags {

	public function get_details(){

		$translation_ident = "action-wpfs_remove_tags-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'tags'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the tags you want to remove the user from. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to remove multiple tags from the user, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpfs_remove_tags</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $user_id, $validated_tags ){
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
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the user.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$validated_tags</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array of the tags that have been removed from the user.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Tags have been removed from the given user.',
			'data' => 
			array (
			  'user_id' => 155,
			  'tags' => 
			  array (
				0 => 3,
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Remove tags',
			'webhook_slug' => 'wpfs_remove_tags',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email of the user you want to remove the tags from.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>tags</strong> argument. This argument accepts a comma-separated list of tag ids, as well as a JSON with each id on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wpfs_remove_tags', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Remove tags', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'remove one or multiple tags', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Remove one or multiple tags from a user within WP Fusion.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-fusion',
			'premium'	   	=> true,
		);


		}


	}

endif; // End if class_exists check.