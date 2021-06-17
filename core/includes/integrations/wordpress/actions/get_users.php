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
			'arguments'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_User_Query notation.', $translation_ident ) ),
			'return_only'    => array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: get_results', $translation_ident ) ),
			'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
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
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user_query</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The full WP_User_Query object.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$args</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted JSON construct that was sent by the caller within the arguments argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_only</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string that was sent by the caller via the return_only argument.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the user query. This depends on the parameters you send.', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg'     => '',
    'data' => array()
);
        </pre>
		<?php
		$returns_code = ob_get_clean();

		ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fetch one or multiple users from your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>get_users</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>get_users</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>get_users</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>arguments</strong>, which contains a JSON formatted string with the parameters used to identify the users.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the fetching of the users.", $translation_ident ); ?></li>
</ol>
<?php
		$description = ob_get_clean();

		return array(
			'action'            => 'get_users',
            'name'              => WPWHPRO()->helpers->translate( 'Get multiple users', $translation_ident ),
			'parameter'         => $parameter,
			'returns'           => $returns,
			'returns_code'      => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Search for users on your WordPress website', $translation_ident ),
            'description'       => $description,
            'integration'       => 'wordpress',
            'premium' 			=> false,
		);

	}

        /**
         * Delete function for defined action
         */
        public function execute( $return_data, $response_body ) {

            $return_args = array(
                'success' => false,
                'msg'     => '',
                'data' => array()
            );

            $args     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
            $return_only     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
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