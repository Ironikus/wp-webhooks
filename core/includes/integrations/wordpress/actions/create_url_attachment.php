<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_url_attachment' ) ) :

	/**
	 * Load the create_url_attachment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_url_attachment {

		public function is_active(){

			//Backwards compatibility for the "Manage Media Files" integration
			if( class_exists( 'WP_Webhooks_Pro_Manage_Media_Files' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-create_url_attachment-description";

			$parameter = array(
				'url'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The url of the file you want to create the attachment of.', $translation_ident ) ),
				'arguments'	  => array( 'short_description' => WPWHPRO()->helpers->translate( '(JSON) Custom arguments to customize your upload process. Default: Please see the description for further information.', $translation_ident ) ),
				'file_name'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Customize the file name of the attachment. - Please see the description for further information.', $translation_ident ) ),
				'parent_post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The parent post id in case you want to set a parent for the file. Default: 0', $translation_ident ) ),
				'add_post_thumbnail' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Assign this attachment as a post thumbnail to one or multiple posts. Please see the description for further details.', $translation_ident ) ),
				'attachment_image_alt' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Add a custom Alternative Text to the attachment (Image ALT).', $translation_ident ) ),
				'attachment_title' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Add a custom title to the attachment (Image Title).', $translation_ident ) ),
				'attachment_caption' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Add a custom caption to the attachment (Image Caption).', $translation_ident ) ),
				'attachment_description' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Add a custom description to the attachment (Image Descripiton).', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to customize the request used to fetch the media from your URL. It accepts a JSON formatted string. Down below, you will find a list of all available attributes:", $translation_ident ); ?>
<pre>{
   "method": "",
   "timeout": "",
   "redirection": "",
   "httpversion":"",
   "user-agent":"",
   "reject_unsafe_urls": "",
   "headers": "",
   "cookies": "",
   "body": "",
   "compress": "",
   "decompress": "",
   "sslverify": "",
   "sslcertificates": "",
   "stream": "",
   "filename": "",
   "limit_response_size": ""
}</pre>
			<?php
			$parameter['arguments']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "Using the <strong>file_name</strong> argument, you can customize the original name of the file (how it comes from the server). Please make sure to also include the extension since it tells this webhook what file type to use. changing the extension also means changing the filetype. E.g.:", $translation_ident ); ?>
<pre>demo-file.txt</pre>
			<?php
			$parameter['file_name']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>add_post_thumbnail</strong> argument allows you to assign the attachment, as a featured image, to one or multiple posts. To use it, simply include a comma-separated list of post ids as a value. Custom post types are supported as well. E.g.:", $translation_ident ); ?>
<pre>42,134,251</pre>
			<?php
			$parameter['add_post_thumbnail']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>create_url_attachment</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $url, $parent_post_id, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$url</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The original URL of the provided file.", $translation_ident ); ?>
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
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The attachment id on success, wp_error on inserting error, upload error on wrong upload or status code error.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'File successfully created.',
				'data' => 
				array (
				  'path' => NULL,
				  'attach_id' => 14,
				  'post_info' => NULL,
				  'add_post_thumbnail' => 
				  array (
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Create URL attachment',
				'webhook_slug' => 'create_url_attachment',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the <strong>url</strong> argument. Please set it to the URL of the media you want to create as an attachment within WordPress.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'create_url_attachment',
				'name'			  => WPWHPRO()->helpers->translate( 'Create URL attachment', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'create an attachment from a URL', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Create an attachment from an url on your website using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.