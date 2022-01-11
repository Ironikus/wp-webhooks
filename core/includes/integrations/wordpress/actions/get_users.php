<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_get_users' ) ) :

	/**
	 * Load the get_users action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_get_users {

		/*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function get_details(){

		$translation_ident = 'action-get_users-content';

		$parameter = array(
			'arguments'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_User_Query notation.', $translation_ident ) ),
			'return_only'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: get_results', $translation_ident ) ),
			'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON formatted string, which includes certain arguments from the WordPress user query called <strong>WP_User_Query</strong>. For further details, please check out the following link:", $translation_ident ); ?>
<br>
<a href="https://codex.wordpress.org/Class_Reference/WP_User_Query" title="wordpress.org" target="_blank">https://codex.wordpress.org/Class_Reference/WP_User_Query</a>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example on how the JSON is set up:", $translation_ident ); ?>
<pre>{"search":"Max","number":5}</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above will filter the users for the name \"Max\" and returns maximum five users with that name.", $translation_ident ); ?>
		<?php
		$parameter['arguments']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the output of the query using the <strong>return_only</strong> parameter. This allows you to, for example, output either only the search results, the total count, the whole query object or any combination in between. Here is an example that returns all of the data:", $translation_ident ); ?>
<pre>get_total,get_results,all,meta_data<?php echo ( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ) ? 'acf_data' : ''; ?></pre>
<?php echo WPWHPRO()->helpers->translate( "The <code>all</code> argument returns the whole WP_Query object, but not the results of the query. If you want the results of the query, you can use the <code>get_results</code> value. To use the <code>meta_data</code> setting, you also need to set the <code>get_results</code> key since the meta data will be attached to every user entry.", $translation_ident ); ?>
<?php 

if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
	echo '<br>' . WPWHPRO()->helpers->translate( "Since you have Advanced Custom Fields installed and active, you can also use the <code>acf_data</code> value for the <code>return_only</code> argument. Please keep in mind that you need to set the <code>get_results</code> argument as well.", $translation_ident );
}

?>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_users</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $user_query, $args, $return_only ){
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
		<strong>$user_query</strong> (object)<br>
		<?php echo WPWHPRO()->helpers->translate( "The full WP_User_Query object.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$args</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The string formatted JSON construct that was sent by the caller within the arguments argument.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_only</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The string that was sent by the caller via the return_only argument.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user query. This depends on the parameters you send.', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'Query was successfully executed.',
			'data' => 
			array (
			  'get_total' => 1,
			  'get_results' => 
			  array (
				0 => 
				array (
				  'data' => 
				  array (
					'ID' => '50',
					'user_login' => 'jon',
					'user_pass' => '$P$BmFilT8WMcJahus2xZ0PK06UGGyAjA/',
					'user_nicename' => 'jon',
					'user_email' => 'jon@doe.test',
					'user_url' => '',
					'user_registered' => '2018-09-19 14:23:16',
					'user_activation_key' => '',
					'user_status' => '0',
					'display_name' => 'jon',
					'spam' => '0',
					'deleted' => '0',
					'meta_data' => 
					array (
					  'nickname' => 
					  array (
						0 => 'jon',
					  ),
					  'first_name' => 
					  array (
						0 => '',
					  ),
					  'last_name' => 
					  array (
						0 => '',
					  ),
					  'description' => 
					  array (
						0 => '',
					  ),
					  'rich_editing' => 
					  array (
						0 => 'true',
					  ),
					  'syntax_highlighting' => 
					  array (
						0 => 'true',
					  ),
					  'comment_shortcuts' => 
					  array (
						0 => 'false',
					  ),
					  'admin_color' => 
					  array (
						0 => 'fresh',
					  ),
					  'use_ssl' => 
					  array (
						0 => '0',
					  ),
					  'show_admin_bar_front' => 
					  array (
						0 => 'true',
					  ),
					  'locale' => 
					  array (
						0 => '',
					  ),
					  'wp_capabilities' => 
					  array (
						0 => 'a:1:{s:10:"subscriber";b:1;}',
					  ),
					  'wp_user_level' => 
					  array (
						0 => '0',
					  ),
					  'dismissed_wp_pointers' => 
					  array (
						0 => 'wp496_privacy',
					  ),
					  'pending' => 
					  array (
						0 => '1',
					  ),
					  'Address' => 
					  array (
						0 => 'Rd. Victoria',
					  ),
					  'City' => 
					  array (
						0 => 'Atlanta',
					  ),
					  'State' => 
					  array (
						0 => 'Georgia',
					  ),
					  'Zip code' => 
					  array (
						0 => '1201',
					  ),
					  'Country' => 
					  array (
						0 => 'USA',
					  ),
					  'account_status' => 
					  array (
						0 => 'approved',
					  ),
					),
				  ),
				  'ID' => 50,
				  'caps' => 
				  array (
					'subscriber' => true,
				  ),
				  'cap_key' => 'wp_capabilities',
				  'roles' => 
				  array (
					0 => 'subscriber',
				  ),
				  'allcaps' => 
				  array (
					'read' => true,
					'level_0' => true,
					'read_private_locations' => true,
					'read_private_events' => true,
					'subscriber' => true,
				  ),
				  'filter' => NULL,
				),
			  ),
			  'all' => 
			  array (
				'query_vars' => 
				array (
				  'blog_id' => 1,
				  'role' => '',
				  'role__in' => 
				  array (
				  ),
				  'role__not_in' => 
				  array (
				  ),
				  'meta_key' => '',
				  'meta_value' => '',
				  'meta_compare' => '',
				  'include' => 
				  array (
				  ),
				  'exclude' => 
				  array (
				  ),
				  'search' => 'Max',
				  'search_columns' => 
				  array (
				  ),
				  'orderby' => 'login',
				  'order' => 'ASC',
				  'offset' => '',
				  'number' => 5,
				  'paged' => 1,
				  'count_total' => true,
				  'fields' => 'all',
				  'who' => '',
				  'has_published_posts' => NULL,
				  'nicename' => '',
				  'nicename__in' => 
				  array (
				  ),
				  'nicename__not_in' => 
				  array (
				  ),
				  'login' => '',
				  'login__in' => 
				  array (
				  ),
				  'login__not_in' => 
				  array (
				  ),
				),
				'meta_query' => 
				array (
				  'queries' => 
				  array (
					0 => 
					array (
					  'key' => 'wp_capabilities',
					  'compare' => 'EXISTS',
					),
					'relation' => 'AND',
				  ),
				  'relation' => NULL,
				  'meta_table' => 'wp_usermeta',
				  'meta_id_column' => 'user_id',
				  'primary_table' => 'wp_users',
				  'primary_id_column' => 'ID',
				),
				'request' => 'SELECT SQL_CALC_FOUND_ROWS wp_users.* FROM wp_users INNER JOIN wp_usermeta ON ( wp_users.ID = wp_usermeta.user_id ) WHERE 1=1 AND ( 
			wp_usermeta.meta_key = \'wp_capabilities\'
		  ) AND (user_login LIKE \'Max\' OR user_url LIKE \'Max\' OR user_email LIKE \'Max\' OR user_nicename LIKE \'Max\' OR display_name LIKE \'Max\' OR display_name LIKE \'Max\') ORDER BY user_login ASC LIMIT 0, 5',
				'query_fields' => 'SQL_CALC_FOUND_ROWS wp_users.*',
				'query_from' => 'FROM wp_users INNER JOIN wp_usermeta ON ( wp_users.ID = wp_usermeta.user_id )',
				'query_where' => 'WHERE 1=1 AND ( 
			wp_usermeta.meta_key = \'wp_capabilities\'
		  ) AND (user_login LIKE \'Max\' OR user_url LIKE \'Max\' OR user_email LIKE \'Max\' OR user_nicename LIKE \'Max\' OR display_name LIKE \'Max\' OR display_name LIKE \'Max\')',
				'query_orderby' => 'ORDER BY user_login ASC',
				'query_limit' => 'LIMIT 0, 5',
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Get multiple users',
			'webhook_slug' => 'get_users',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>arguments</strong>, which contains a JSON formatted string with the parameters used to identify the users.', $translation_ident )
			),
		) );

		return array(
			'action'			=> 'get_users',
			'name'			  => WPWHPRO()->helpers->translate( 'Get multiple users', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'get or search for multiple users', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Search for users on your WordPress website', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wordpress',
			'premium' 			=> false,
		);

	}

		/**
		 * Delete function for defined action
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array()
			);

			$args	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
			$return_only	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
			$user_query = null;

			if( empty( $args ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("arguments is a required parameter. Please define it.", 'action-get_users-failure' );

				return $return_args;
			}

			$serialized_args = null;
			if( WPWHPRO()->helpers->is_json( $args ) ){
				$serialized_args = json_decode( $args, true );
			}

			$return = array( 'get_results' );
			if( ! empty( $return_only ) ){
				$return = array_map( 'trim', explode( ',', $return_only ) );
			}

			if( ! empty( $serialized_args ) && is_array( $serialized_args ) ){
				$user_query = new WP_User_Query( $serialized_args );

				if ( is_wp_error( $user_query ) ) {
					$return_args['msg'] = WPWHPRO()->helpers->translate( $user_query->get_error_message(), 'action-get_users-failure' );
				} else {

					foreach( $return as $single_return ){

						switch( $single_return ){
							case 'all':
								$return_args['data'][ $single_return ] = $user_query;
								break;
							case 'get_results':
								$return_args['data'][ $single_return ] = $user_query->get_results();
								break;
							case 'get_total':
								$return_args['data'][ $single_return ] = $user_query->get_total();
								break;
						}

					}

					//Manually attach additional data to the query
					foreach( $return as $single_return ){

						if( $single_return === 'meta_data' ){
							if( isset( $return_args['data']['get_results'] ) && ! empty( $return_args['data']['get_results'] ) ){
								foreach( $return_args['data']['get_results'] as $user_key => $user_data ){
									if( isset( $user_data->data ) && isset( $user_data->data->ID ) ){
										$return_args['data']['get_results'][ $user_key ]->data->meta_data = get_user_meta( $user_data->data->ID );
									}
								}
							}
						}

						if( $single_return === 'acf_data' ){
							if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
								if( isset( $return_args['data']['get_results'] ) && ! empty( $return_args['data']['get_results'] ) ){
									foreach( $return_args['data']['get_results'] as $user_key => $user_data ){
										if( isset( $user_data->data ) && isset( $user_data->data->ID ) ){
											$return_args['data']['get_results'][ $user_key ]->data->acf_data = get_fields( 'user_' . $user_data->data->ID );
										}
									}
								}
							}
						}

					}

					$return_args['msg'] = WPWHPRO()->helpers->translate("Query was successfully executed.", 'action-get_users-success' );
					$return_args['success'] = true;

				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_users-failure' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $user_query, $args, $return_only );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.