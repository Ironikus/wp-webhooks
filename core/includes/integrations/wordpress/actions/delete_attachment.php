<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_attachment' ) ) :

	/**
	 * Load the delete_attachment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_attachment {

		public function is_active(){

			//Backwards compatibility for the "Manage Media Files" integration
			if( class_exists( 'WP_Webhooks_Pro_Manage_Media_Files' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-delete_attachment-description";

			$parameter = array(
				'attachment_id'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The id of the attachment you want to delete.', $translation_ident ) ),
				'force_delete' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Whether to bypass trash and force deletion. Default: no'. 'Please read the description for more information.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "Please note: The attachment is moved to the trash instead of being permanently deleted, unless trash for media is disabled, the item is already in the trash, or force_delete is true.", $translation_ident ); ?>
			<?php
			$parameter['force_delete']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_attachment</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $attachment_id, $parent_post_id, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$attachment_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The attachment id of the attachment you just deleted.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$parent_post_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The parent post id. In case it wasn't given, we return 0.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The attachment data (post data) on success, false or null on error.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Attachment successfully deleted.',
				'data' => 
				array (
				  'data' => NULL,
				  'post_data' => 
				  array (
					'ID' => 14,
					'post_author' => '0',
					'post_date' => '2021-06-01 21:29:30',
					'post_date_gmt' => '2021-06-01 21:29:30',
					'post_content' => '',
					'post_title' => 'icon',
					'post_excerpt' => '',
					'post_status' => 'inherit',
					'comment_status' => 'open',
					'ping_status' => 'closed',
					'post_password' => '',
					'post_name' => 'icon-2',
					'to_ping' => '',
					'pinged' => '',
					'post_modified' => '2021-06-01 21:29:30',
					'post_modified_gmt' => '2021-06-01 21:29:30',
					'post_content_filtered' => '',
					'post_parent' => 0,
					'guid' => 'https://yourdomain.test/wp-content/uploads/2021/06/icon.png',
					'menu_order' => 0,
					'post_type' => 'attachment',
					'post_mime_type' => 'image/png',
					'comment_count' => '0',
					'filter' => 'raw',
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete attachment',
				'webhook_slug' => 'delete_attachment',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>attachment_id</strong> argument. Please set it to the attachmant id of the file you want to delete within WordPress.', $translation_ident )
				)
			) );

			return array(
				'action'			=> 'delete_attachment',
				'name'			  => WPWHPRO()->helpers->translate( 'Delete attachment', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'delete an attachment', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Delete an attachment from your website using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.