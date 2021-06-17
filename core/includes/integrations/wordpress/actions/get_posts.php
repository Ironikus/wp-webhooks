<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_get_posts' ) ) :

	/**
	 * Load the get_posts action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_get_posts {

        /*
	 * The core logic to grab certain users using WP_User_Query
	 */
	public function get_details(){

		$translation_ident = 'action-get_posts-content';

		$parameter = array(
			'arguments'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A string containing a JSON construct in the WP_Query notation.', $translation_ident ) ),
			'return_only'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Define the data you want to return. Please check the description for more information. Default: posts', $translation_ident ) ),
			'load_meta'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" to add the post meta to each given post. Default: "no"', $translation_ident ) ),
			'load_acf'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" to add the Advanced Custom Fields related post meta to each given post. Default: "no"', $translation_ident ) ),
			'load_taxonomies'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Single value or comma separated list of the taxonomies you want to addto the response.', $translation_ident ) ),
			'do_action'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		if( ! WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			unset( $parameter['load_acf'] );
		}

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON formatted string, which includes certain arguments from the WordPress post query called <strong>WP_Query</strong>. For further details, please check out the following link:", $translation_ident ); ?>
<br>
<a href="https://developer.wordpress.org/reference/classes/wp_query/" title="wordpress.org" target="_blank">https://developer.wordpress.org/reference/classes/wp_query/</a>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example on how the JSON is set up:", $translation_ident ); ?>
<pre>{"post_type":"post","posts_per_page":8}</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above will filter the posts for the post type \"post\" and returns maximum eight posts.", $translation_ident ); ?>
		<?php
		$parameter['arguments']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the output of the query using the <strong>return_only</strong> parameter. This allows you to output only certain elements or the whole WP_Query class. Here is an example:", $translation_ident ); ?>
<pre>posts,post_count,found_posts,max_num_pages</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li>all</li>
    <li>posts</li>
    <li>post</li>
    <li>post_count</li>
    <li>found_posts</li>
    <li>max_num_pages</li>
    <li>current_post</li>
    <li>query_vars</li>
    <li>query</li>
    <li>tax_query</li>
    <li>meta_query</li>
    <li>date_query</li>
    <li>request</li>
    <li>in_the_loop</li>
    <li>current_post</li>
</ol>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can also attach the assigned taxonomies of the returned posts. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", $translation_ident ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
		<?php
		$parameter['load_taxonomies']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_posts</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $post_query, $args, $return_only ){
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
        <strong>$post_query</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The full WP_Query object.", $translation_ident ); ?>
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
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'The data construct of the post query. This depends on the parameters you send.', $translation_ident ) ),
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
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fetch one or multiple posts from your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>get_posts</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>get_posts</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>get_posts</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>arguments</strong>, which contains a JSON formatted string with the parameters used to identify the posts. More details about that is available within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the fetching of the posts.", $translation_ident ); ?></li>
</ol>
<?php
            $description = ob_get_clean();

            return array(
                'action'            => 'get_posts',
                'name'              => WPWHPRO()->helpers->translate( 'Get multiple posts', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Search for posts on your WordPress website', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium' 			=> false,
            );

        }

        /**
         * Grab certain posts using WP_Query
         */
        public function execute( $return_data, $response_body ) {

            $return_args = array(
                'success' => false,
                'msg'     => '',
                'data' => array()
            );

            $args = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
            $return_only = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
            $load_meta = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_meta' ) === 'yes' ) ? true : false;
            $load_acf = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_acf' ) === 'yes' ) ? true : false;
            $load_taxonomies = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'load_taxonomies' );
            $do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
            $post_query = null;

            if( empty( $args ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate("arguments is a required parameter. Please define it.", 'action-get_posts-failure' );

                return $return_args;
            }

            $serialized_args = null;
            if( WPWHPRO()->helpers->is_json( $args ) ){
                $serialized_args = json_decode( $args, true );
            }

            $return = array( 'posts' );
            if( ! empty( $return_only ) ){
                $return = array_map( 'trim', explode( ',', $return_only ) );
            }

            if( is_array( $serialized_args ) ){
                $post_query = new WP_Query( $serialized_args );

                if ( is_wp_error( $post_query ) ) {
                    $return_args['msg'] = WPWHPRO()->helpers->translate( $post_query->get_error_message(), 'action-get_posts-failure' );
                } else {

                    foreach( $return as $single_return ){

                        switch( $single_return ){
                            case 'all':
                                $return_args['data'][ $single_return ] = $post_query;
                                break;
                            case 'posts':
                                $return_args['data'][ $single_return ] = $post_query->posts;
                                break;
                            case 'post':
                                $return_args['data'][ $single_return ] = $post_query->post;
                                break;
                            case 'post_count':
                                $return_args['data'][ $single_return ] = $post_query->post_count;
                                break;
                            case 'found_posts':
                                $return_args['data'][ $single_return ] = $post_query->found_posts;
                                break;
                            case 'max_num_pages':
                                $return_args['data'][ $single_return ] = $post_query->max_num_pages;
                                break;
                            case 'current_post':
                                $return_args['data'][ $single_return ] = $post_query->current_post;
                                break;
                            case 'query_vars':
                                $return_args['data'][ $single_return ] = $post_query->query_vars;
                                break;
                            case 'query':
                                $return_args['data'][ $single_return ] = $post_query->query;
                                break;
                            case 'tax_query':
                                $return_args['data'][ $single_return ] = $post_query->tax_query;
                                break;
                            case 'meta_query':
                                $return_args['data'][ $single_return ] = $post_query->meta_query;
                                break;
                            case 'date_query':
                                $return_args['data'][ $single_return ] = $post_query->date_query;
                                break;
                            case 'request':
                                $return_args['data'][ $single_return ] = $post_query->request;
                                break;
                            case 'in_the_loop':
                                $return_args['data'][ $single_return ] = $post_query->in_the_loop;
                                break;
                            case 'current_post':
                                $return_args['data'][ $single_return ] = $post_query->current_post;
                                break;
                        }

                    }

                    if( $load_meta ){

                        //Add the meta data to the posts array
                        if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
                            foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
                                $return_args['data']['posts'][ $single_post_key ]->meta_data = get_post_meta( $single_post->ID );
                            }
                        }

                        //Add the post meta to the single post
                        if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
                            $return_args['data']['post']->meta_data = get_post_meta( $return_args['data']['post']->ID );
                        }
                    }

                    if( $load_acf && WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){

                        //Add the meta data to the posts array
                        if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
                            foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
                                $return_args['data']['posts'][ $single_post_key ]->acf_data = get_fields( $single_post->ID );
                            }
                        }

                        //Add the post meta to the single post
                        if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
                            $return_args['data']['post']->acf_data = get_fields( $return_args['data']['post']->ID );
                        }
                    }

                    if( ! empty( $load_taxonomies ) ){

                        $post_taxonomies_out = array_map( 'trim', explode( ',', $load_taxonomies ) );

                        //Add the taxonomies to the posts array
                        if( isset( $return_args['data']['posts'] ) && is_array( $return_args['data']['posts'] ) ){
                            foreach( $return_args['data']['posts'] as $single_post_key => $single_post ){
                                $return_args['data']['posts'][ $single_post_key ]->taxonomies = wp_get_post_terms( $single_post->ID, $post_taxonomies_out );
                            }
                        }

                        //Add the taxonomies to the single post
                        if( isset( $return_args['data']['post'] ) && is_object( $return_args['data']['post'] ) ){
                            $return_args['data']['post']->taxonomies = wp_get_post_terms( $return_args['data']['post']->ID, $post_taxonomies_out );
                        }
                    }

                    $return_args['msg'] = WPWHPRO()->helpers->translate( "Query was successfully executed.", 'action-get_posts-success' );
                    $return_args['success'] = true;

                }

            } else {
                $return_args['msg'] = WPWHPRO()->helpers->translate("The arguments parameter does not contain a valid json. Please check it first.", 'action-get_posts-failure' );
            }

            if( ! empty( $do_action ) ){
                do_action( $do_action, $return_args, $post_query, $args, $return_only );
            }

            return $return_args;
        }

    }

endif; // End if class_exists check.