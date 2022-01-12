<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_post' ) ) :

	/**
	 * Load the delete_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_post {

		/*
	 * The core logic to delete a specified user
	 */
	public function get_details(){

		$translation_ident = 'action-delete-post-content';

		$parameter = array(
			'post_id'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of your specified post. This field is required.', $translation_ident ) ),
			'force_delete'  => array(
				'short_description' => WPWHPRO()->helpers->translate( '(optional) Whether to bypass trash and force deletion (added in WordPress 2.9). Possible values: "yes" and "no". Default: "no". Please note that soft deletion just works for the "post" and "page" post type.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the post will be completely removed from your WordPress website.", $translation_ident ),
			),
			'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post, $post_id, $check, $force_delete ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$post</strong> (object)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the WordPress post object of the already deleted post.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the post id of the deleted post.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$check</strong> (mixed)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the response of the wp_delete_post() function.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$force_delete</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the force_delete argument.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Post related data as an array. We return the post id with the key "post_id" and the force delete boolean with the key "force_delete". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);
		
			$returns_code = array (
				'success' => true,
				'msg' => 'Post successfully deleted.',
				'data' => 
				array (
				  'post_id' => 1337,
				  'force_delete' => false,
				  'permalink' => 'https://yourdomain.test/?p=1337',
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete a post',
				'webhook_slug' => 'delete_post',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the post id of the post you want to delete. You can do that by using the <strong>post_id</strong> argument.', $translation_ident )
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'Please note that deleting a post without defining the <strong>force_delete</strong> argument, only moves default posts and pages to the trash (wherever applicable) - otherwise they will be directly deleted.', $translation_ident )
				),
			) );

			return array(
				'action'			=> 'delete_post',
				'name'			  => WPWHPRO()->helpers->translate( 'Delete post', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'delete a post', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => sprintf( WPWHPRO()->helpers->translate( 'Delete a post via %s.', $translation_ident ), WPWH_NAME ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		/**
		 * The action for deleting a post
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'post_id' => 0,
					'force_delete' => false,
					'permalink' => ''
				)
			);

			$post_id		 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) : 0;
			$force_delete	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
			$do_action	   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) : '';
			$post = '';
			$check = '';

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
			}

			if( ! empty( $post ) ){
				if( ! empty( $post->ID ) ){

					$permalink = get_permalink( $post_id );
					$check = wp_delete_post( $post->ID, $force_delete );

					if ( $check ) {

						do_action( 'wpwhpro/run/delete_action_post_deleted' );

						$return_args['msg']	 = WPWHPRO()->helpers->translate("Post successfully deleted.", 'action-delete-post-success' );
						$return_args['success'] = true;
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
						$return_args['data']['permalink'] = $permalink;
					} else {
						$return_args['msg']  = WPWHPRO()->helpers->translate("Error deleting post. Please check wp_delete_post( " . $post->ID . ", " . $force_delete . " ) for more information.", 'action-delete-post-success' );
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					}

				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Could not delete the post: No ID given.", 'action-delete-post-success' );
				}
			} else {
				$return_args['msg']  = WPWHPRO()->helpers->translate("No post found to your specified post id.", 'action-delete-post-success' );
				$return_args['data']['post_id'] = $post_id;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post, $post_id, $check, $force_delete );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.