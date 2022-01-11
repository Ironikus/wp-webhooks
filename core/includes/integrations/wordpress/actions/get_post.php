<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_get_post' ) ) :

	/**
	 * Load the get_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_get_post {

		/*
	 * The core logic to get a single post
	 */
	public function get_details(){

		$translation_ident = 'action-get_post-content';

		$parameter = array(
			'post_id'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The post id of the post you want to fetch.', $translation_ident ) ),
			'return_only'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Select the values you want to return. Default is all.', $translation_ident ) ),
			'thumbnail_size'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Pass the size of the thumbnail of your given post id. Default is full.', $translation_ident ) ),
			'post_taxonomies'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Single value or comma separated list of the taxonomies you want to return. Default: post_tag.', $translation_ident ) ),
			'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after our plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the result of the post data gathering using the <strong>return_only</strong> parameter. This allows you to output only certain elements of the request. Here is an example:", $translation_ident ); ?>
<pre>post,post_thumbnail,post_terms,post_meta,post_permalink</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
	<li><strong>all</strong></li>
	<li><strong>post</strong></li>
	<li><strong>post_thumbnail</strong></li>
	<li><strong>post_terms</strong></li>
	<li><strong>post_meta</strong></li>
	<li><strong>post_permalink</strong></li>
	<?php if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
		echo '<li><strong>acf_data</strong> (' . WPWHPRO()->helpers->translate( "Integrates Advanced Custom Fields", $translation_ident ) . ')</li>';
	} ?>
</ol>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to return one or multiple thumbnail_sizes for the given post thumbnail. By default, we output only the full image. Here is an example: ", $translation_ident ); ?>
<pre>full,medium</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available sizes for the <strong>thumbnail_size</strong> argument (The availalbe sizes may vary since you can also use third-party size definitions). In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
	<li><strong>thumbnail</strong> <?php echo WPWHPRO()->helpers->translate( "(150px square)", $translation_ident ); ?></li>
	<li><strong>medium</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 300px width and height)", $translation_ident ); ?></li>
	<li><strong>large</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 1024px width and height)", $translation_ident ); ?></li>
	<li><strong>full</strong> <?php echo WPWHPRO()->helpers->translate( "(full/original image size you uploaded)", $translation_ident ); ?></li>
</ol>
		<?php
		$parameter['thumbnail_size']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also customize the output of the returned taxonomies using the <strong>post_taxonomies</strong> argument. Default is post_tag. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", $translation_ident ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
		<?php
		$parameter['post_taxonomies']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $post_id, $thumbnail_size, $post_taxonomies ){
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
		<strong>$post_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the currently fetched post.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$thumbnail_size</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The string formatted thumbnail sizes sent by the caller within the thumbnail_size argument.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$post_taxonomies</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The string formatted taxonomy slugs sent by the caller within the post_taxonomies argument.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the single post. This depends on the parameters you send.', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Post was successfully returned.',
				'data' => 
				array (
				  'post' => 
				  array (
					'ID' => 7920,
					'post_author' => '1',
					'post_date' => '2021-12-31 11:11:11',
					'post_date_gmt' => '2021-12-31 11:11:11',
					'post_content' => 'The content of the post, including all HTML',
					'post_title' => 'A demo title',
					'post_excerpt' => 'The short description of the post',
					'post_status' => 'future',
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_password' => '',
					'post_name' => 'somedemoname',
					'to_ping' => '',
					'pinged' => '',
					'post_modified' => '2021-12-31 11:11:11',
					'post_modified_gmt' => '2021-12-31 11:11:11',
					'post_content_filtered' => '',
					'post_parent' => 0,
					'guid' => 'https://yourdomain.test/?p=7920',
					'menu_order' => 0,
					'post_type' => 'post',
					'post_mime_type' => '',
					'comment_count' => '0',
					'filter' => 'raw',
				  ),
				  'post_thumbnail' => false,
				  'post_terms' => 
				  array (
				  ),
				  'post_meta' => 
				  array (
					'first_custom_key' => 
					array (
					  0 => 'Some custom value',
					),
					'second_custom_key' => 
					array (
					  0 => 'The new value',
					),
					'wpwhpro_create_post_temp_status_jobs' => 
					array (
					  0 => 'future',
					),
				  ),
				  'post_permalink' => 'https://yourdomain.test/?p=7920',
				  'acf_data' => false,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Get a post',
				'webhook_slug' => 'get_post',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>post_id</strong> argument which contains the id of the post you want to fetch.', $translation_ident )
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'This webhook action uses the default WordPress function get_post():', $translation_ident ) . ' <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/get_post/">https://developer.wordpress.org/reference/functions/get_post/</a>',
				),
			) );

			return array(
				'action'			=> 'get_post',
				'name'			  => WPWHPRO()->helpers->translate( 'Get post', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'get a post', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Returns the object of a user', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		/**
		 * Get a single post using get_post
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array()
			);

			$post_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$return_only	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
			$thumbnail_size	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'thumbnail_size' );
			$post_taxonomies	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_taxonomies' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $post_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "It is necessary to define the post_id argument. Please define it first.", 'action-get_post-failure' );

				return $return_args;
			}

			$return = array( 'all' );
			if( ! empty( $return_only ) ){
				$return = array_map( 'trim', explode( ',', $return_only ) );
			}

			$thumbnail_sizes = 'full';
			if( ! empty( $thumbnail_size ) ){
				$thumbnail_sizes = array_map( 'trim', explode( ',', $thumbnail_size ) );
			}

			$post_taxonomies_out = 'post_tag';
			if( ! empty( $post_taxonomies ) ){
				$post_taxonomies_out = array_map( 'trim', explode( ',', $post_taxonomies ) );
			}

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
				$post_thumbnail = get_the_post_thumbnail_url( $post_id, $thumbnail_sizes );
				$post_terms = wp_get_post_terms( $post_id, $post_taxonomies_out );
				$post_meta = get_post_meta( $post_id );
				$permalink = get_permalink( $post_id );

				$acf_data = '';
				if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
					$acf_data = get_fields( $post_id );
				}

				if ( is_wp_error( $post ) ) {
					$return_args['msg'] = WPWHPRO()->helpers->translate( $post->get_error_message(), 'action-get_post-failure' );
				} else {

					foreach( $return as $single_return ){

						switch( $single_return ){
							case 'all':
								$return_args['data'][ 'post' ] = $post;
								$return_args['data'][ 'post_thumbnail' ] = $post_thumbnail;
								$return_args['data'][ 'post_terms' ] = $post_terms;
								$return_args['data'][ 'post_meta' ] = $post_meta;
								$return_args['data'][ 'post_permalink' ] = $permalink;

								if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
									$return_args['data'][ 'acf_data' ] = $acf_data;
								}

								break;
							case 'post':
								$return_args['data'][ $single_return ] = $post;
								break;
							case 'post_thumbnail':
								$return_args['data'][ $single_return ] = $post_thumbnail;
								break;
							case 'post_terms':
								$return_args['data'][ $single_return ] = $post_terms;
								break;
							case 'post_meta':
								$return_args['data'][ $single_return ] = $post_meta;
								break;
							case 'post_permalink':
								$return_args['data'][ $single_return ] = $permalink;
								break;
							case 'acf_data':
								if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
									$return_args['data'][ $single_return ] = $acf_data;
								}
								break;
						}
					}

					$return_args['msg'] = WPWHPRO()->helpers->translate("Post was successfully returned.", 'action-get_post-success' );
					$return_args['success'] = true;

				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("There is an issue with your defined arguments. Please check them first.", 'action-get_post-failure' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $post_id, $thumbnail_size, $post_taxonomies );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.