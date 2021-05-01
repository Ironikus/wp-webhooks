<?php
if ( ! class_exists( 'WP_Webhooks_Action_create_update_post' ) ) :

	/**
	 * Load the create_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Action_create_update_post {

		function __construct(){
			$this->page_name    = WPWHPRO()->settings->get_page_name();
			$this->page_title   = WPWHPRO()->settings->get_page_title();

            add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_action_details' ), 10 );
			add_filter( 'wpwhpro/webhooks/add_webhook_actions', array( $this, 'add_action_callback' ), 1000, 4 );
        }

        /**
         * Register the webhook details
         *
         * @param array $actions
         * @return array The adjusted webhook details
         */
		public function add_action_details( $actions ){

			$actions['create_post'] = $this->action_create_post_content();

			return $actions;
		}

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function add_action_callback( $response, $action, $response_ident_value, $response_api_key ){

			switch( $action ){
				case 'create_post':
					$response = $this->action_create_post();
					break;
			}

			return $response;
		}

		/*
	 * The core logic to handle the creation of a user
	 */
	public function action_create_post_content(){

		$translation_ident = 'action-create-post-content';

		$parameter = array(
			'post_author'           => array(
				'short_description' => WPWHPRO()->helpers->translate( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", $translation_ident ),
			),
			'post_date'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', $translation_ident ) ),
			'post_date_gmt'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', $translation_ident ) ),
			'post_content'          => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post content. Default empty.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", $translation_ident )
			),
			'post_content_filtered' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The filtered post content. Default empty.', $translation_ident ) ),
			'post_title'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post title. Default empty.', $translation_ident ) ),
			'post_excerpt'          => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post excerpt. Default empty.', $translation_ident ) ),
			'post_status'           => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post status. Default \'draft\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post status defines further details about how your post will be treated. By default, WordPress offers the following post statuses: <strong>draft, pending, private, publish</strong>. Please note that other plugins can extend the post status values to offer a bigger variety, e.g. Woocommerce.", $translation_ident ),
			),
			'post_type'             => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post type. Default \'post\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post type determines to which group of posts your currently created post belongs. Please use the slug of the post type to assign it properly.", $translation_ident ),
			),
			'comment_status'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', $translation_ident ) ),
			'ping_status'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', $translation_ident ) ),
			'post_password'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The password to access the post. Default empty.', $translation_ident ) ),
			'post_name'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', $translation_ident ) ),
			'to_ping'               => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', $translation_ident ) ),
			'pinged'                => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', $translation_ident ) ),
			'post_parent'           => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', $translation_ident ) ),
			'menu_order'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', $translation_ident ) ),
			'post_mime_type'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The mime type of the post. Default empty.', $translation_ident ) ),
			'guid'                  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', $translation_ident ) ),
			'import_id'             => array(
				'short_description' => WPWHPRO()->helpers->translate( '(integer) In case you want to give your post a specific post id, please define it here.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "This argument allows you to define a suggested post ID for your post. In case the ID is already taken, the post will be created using the default behavior by asigning automatically an ID. ", $translation_ident ),
			),
			'post_category'         => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of category IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', $translation_ident ) ),
			'tags_input'            => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', $translation_ident ) ),
			'tax_input'             => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty.', $translation_ident ) ),
			'wp_error'              => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", $translation_ident ),
			),
			'do_action'             => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument supports the default tags_input variable of the <strong>wp_insert_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example:", $translation_ident ); ?>
<pre>342,5678,2</pre>
<?php echo WPWHPRO()->helpers->translate( "This argument supports a comma separated list of tag names, slugs, or IDs.", $translation_ident ); ?>
		<?php
		$parameter['tags_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add/append/delete any kind of taxonomies on your post. It uses a custom functionality that adds the taxonomies independently of the <strong>wp_update_post()</strong> function.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "To make it work, we offer certain different features and methods to make the most out of the taxonomy management. Down below, you will find further information about the whole functionality.", $translation_ident ); ?>
<ol>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too. In case you use more complex taxonomies that use semicolons or double points within the slugs, you need to use the JSON method.", $translation_ident ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Replace existing taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "This method allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", $translation_ident ); ?>
                <pre>taxonomy_1,tax_item_1:tax_item_2:tax_item_3;taxonomy_2,tax_item_5:tax_item_7:tax_item_8</pre>
                <?php echo WPWHPRO()->helpers->translate( "To separate the taxonomies from the single taxonomy items, please use a comma \",\". In case you want to add multiple items per taxonomy, you can separate them via a double point \":\". To separate multiple taxonomies from each other, please separate them with a semicolon \";\" (It is not necessary to set a semicolon at the end of the last one)", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove all taxonomy items for a single taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all;</strong> in front of a semicolon-separated list of the taxonomies you want to remove all items for. Here is an example:", $translation_ident ); ?>
                <pre>ironikus-remove-all;taxonomy_1;taxonomy_2</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove single taxonomy items for a taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also remove only single taxonomy items for one or multiple taxonomies. Here is an example:", $translation_ident ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2-ironikus-delete:value_3;taxonomy_2,value_5:value_6:value_7-ironikus-delete</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2. We also add the taxonomy items value_1, value_3, value_5 and value_6. We also remove the taxonomy items value_2 and value_7.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Append taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append;</strong> at the beginning of the string.", $translation_ident ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2:value_3;taxonomy_2,value_1:value_2:value_3</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2 with multiple taxonomy items on the post. The already assigned ones won't be replaced.", $translation_ident ); ?>
            </li>
        </ul>
    </li>
    <li>
    <strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too.", $translation_ident ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Replace existing taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "This JSON allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", $translation_ident ); ?>
                <pre>{
  "category": [
    "test-category",
    "second-category"
  ],
  "post_tag": [
    "dog",
    "male",
    "simple"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "The key on the first layer of the JSON is the slug of the taxonomy. As a value, it accepts multiple slugs of the single taxonomy terms. To add multiple taxonomies, simply append them on the first layer of the JSON.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove all taxonomy items for a single taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all</strong> as a separate value with the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. Here is an example:", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-remove-all",
  "category": [],
  "post_tag": []
}</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Append taxonomy items", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append</strong> to the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. All the taxonomies you add after, will be added to the existing ones on the post.", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-append",
  "category": [
    "test-category",
    "second-category"
  ],
  "post_tag": [
    "dog"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies category and post_tag with multiple taxonomy items on the post. The already assigned ones won't be replaced.", $translation_ident ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo WPWHPRO()->helpers->translate( "Remove single taxonomy items for a taxonomy", $translation_ident ); ?></strong>
                <br>
                <?php echo WPWHPRO()->helpers->translate( "You can also remove only single taxonomy items for one or multiple taxonomies. To do that, simply append <strong>-ironikus-delete</strong> at the end of the taxonomy term slug. This specific taxonomy term will then be removed from the post. Here is an example:", $translation_ident ); ?>
                <pre>{
  "wpwhtype": "ironikus-append",
  "category": [
    "test-category",
    "second-category-ironikus-delete"
  ],
  "post_tag": [
    "dog-ironikus-delete"
  ]
}</pre>
                <?php echo WPWHPRO()->helpers->translate( "In the example above, we append the taxonomies category and post_tag. We also add the taxonomy item test-category. We also remove the taxonomy items second-category and dog.", $translation_ident ); ?>
            </li>
        </ul>
    </li>
</ol>
		<?php
		$parameter['tax_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the create_post action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post_data, $post_id, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$post_data</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to create the post and some additional data as the meta input.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the post id of the newly created post. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
    </li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success'   => false,
    'msg'       => '',
    'data'      => array(
        'post_id' => null,
        'post_data' => null
    )
);
			</pre>
			<?php
			$returns_code = ob_get_clean();

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a post on your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>create_post</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>create_post</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>create_post</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the post. We would still recommend to set the attribute <strong>post_title</strong>, to make recognizing it easy, as well as for creating proper permalinks/slugs.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you want to create a post for a custom post type, you can do that by using the <strong>post_type</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "By default, we create each post in a draft state. If you want to directly publish a post, use the <strong>post_status</strong> argument and set it to <strong>publish</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you want to set a short description for your post, you can use the <strong>post_excerpt</strong> argument.", $translation_ident ); ?></li>
</ol>
<?php
			$description = ob_get_clean();

			return array(
				'action'            => 'create_post',
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Insert/Create a post. You have all functionalities available from wp_insert_post', $translation_ident ),
				'description'       => $description
			);

		}

		/**
		 * Create a post via an action call
		 */
		public function action_create_post(){

			$response_body = WPWHPRO()->helpers->get_response_body();
			$return_args = array(
				'success'   => false,
				'msg'       => '',
				'data'      => array(
					'post_id' => null,
					'post_data' => null
				)
			);

			$post_id                = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );

			$post_author            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_author' );
			$post_date              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date' ) );
			$post_date_gmt          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date_gmt' ) );
			$post_content           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content' );
			$post_content_filtered  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content_filtered' );
			$post_title             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_title' );
			$post_excerpt           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_excerpt' );
			$post_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_status' ) );
			$post_type              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_type' ) );
			$comment_status         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
			$ping_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
			$post_password          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_password' ) );
			$post_name              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_name' ) );
			$to_ping                = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
			$pinged                 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
			$post_modified          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified' ) );
			$post_modified_gmt      = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified_gmt' ) );
			$post_parent            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_parent' ) );
			$menu_order             = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
			$post_mime_type         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_mime_type' ) );
			$guid                   = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
			$import_id              = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'import_id' ) );
			$post_category          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_category' );
			$tags_input             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
			$tax_input              = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
			$wp_error               = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )     ? true : false;
			$do_action              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

			$post_data = array();

			if( ! empty( $post_author ) ){

				$post_author_id = 0;
				if( is_numeric( $post_author ) ){
					$post_author_id = intval( $post_author );
				} elseif ( is_email( $post_author ) ) {
					$get_user = get_user_by( 'email', $post_author );
					if( ! empty( $get_user ) && ! empty( $get_user->data ) && ! empty( $get_user->data->ID ) ){
						$post_author_id = $get_user->data->ID;
					}
				}

				$post_data['post_author'] = $post_author_id;
			}

			if( ! empty( $post_date ) ){
				$post_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $post_date ) );
			}

			if( ! empty( $post_date_gmt ) ){
				$post_data['post_date_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_date_gmt ) );
			}

			if( ! empty( $post_content ) ){
				$post_data['post_content'] = $post_content;
			}

			if( ! empty( $post_content_filtered ) ){
				$post_data['post_content_filtered'] = $post_content_filtered;
			}

			if( ! empty( $post_title ) ){
				$post_data['post_title'] = $post_title;
			}

			if( ! empty( $post_excerpt ) ){
				$post_data['post_excerpt'] = $post_excerpt;
			}

			if( ! empty( $post_status ) ){
				$post_data['post_status'] = $post_status;
			}

			if( ! empty( $post_type ) ){
				$post_data['post_type'] = $post_type;
			}

			if( ! empty( $comment_status ) ){
				$post_data['comment_status'] = $comment_status;
			}

			if( ! empty( $ping_status ) ){
				$post_data['ping_status'] = $ping_status;
			}

			if( ! empty( $post_password ) ){
				$post_data['post_password'] = $post_password;
			}

			if( ! empty( $post_name ) ){
				$post_data['post_name'] = $post_name;
			}

			if( ! empty( $to_ping ) ){
				$post_data['to_ping'] = $to_ping;
			}

			if( ! empty( $pinged ) ){
				$post_data['pinged'] = $pinged;
			}

			if( ! empty( $post_modified ) ){
				$post_data['post_modified'] = date( "Y-m-d H:i:s", strtotime( $post_modified ) );
			}

			if( ! empty( $post_modified_gmt ) ){
				$post_data['post_modified_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_modified_gmt ) );
			}

			if( ! empty( $post_parent ) ){
				$post_data['post_parent'] = $post_parent;
			}

			if( ! empty( $menu_order ) ){
				$post_data['menu_order'] = $menu_order;
			}

			if( ! empty( $post_mime_type ) ){
				$post_data['post_mime_type'] = $post_mime_type;
			}

			if( ! empty( $guid ) ){
				$post_data['guid'] = $guid;
			}

			if( ! empty( $import_id ) ){
				$post_data['import_id'] = $import_id;
			}

			//Setup post categories
			if( ! empty( $post_category ) ){
				$post_category_data = explode( ',', trim( $post_category, ',' ) );

				if( ! empty( $post_category_data ) ){
					$post_data['post_category'] = $post_category_data;
				}
			}

			//Setup meta tags
			if( ! empty( $tags_input ) ){
				$post_tags_data = explode( ',', trim( $tags_input, ',' ) );

				if( ! empty( $post_tags_data ) ){
					$post_data['tags_input'] = $post_tags_data;
				}
			}

			//Fetch the current post type on update
			$current_post_type = $post_type;

			$post_id = wp_insert_post( $post_data, $wp_error );

			if ( ! is_wp_error( $post_id ) && is_numeric( $post_id ) ) {

				//Setup meta tax
				if( ! empty( $tax_input ) ){
					$remove_all = false;
					$tax_append = false; //Default by WP wp_set_object_terms
					$tax_data = array(
						'delete' => array(),
						'create' => array(),
					);

					if( WPWHPRO()->helpers->is_json( $tax_input ) ){
						$post_tax_data = json_decode( $tax_input, true );
						foreach( $post_tax_data as $taxkey => $single_meta ){

							//Validate special values
							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}

							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}

							$meta_key           = sanitize_text_field( $taxkey );
							$meta_values        = $single_meta;

							if( ! empty( $meta_key ) ){

								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}

								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}

										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {

										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}

										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}

							}
						}
					} else {
						$post_tax_data = explode( ';', trim( $tax_input, ';' ) );
						foreach( $post_tax_data as $single_meta ){

							//Validate special values
							if( $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}

							if( $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}

							$single_meta_data   = explode( ',', $single_meta );
							$meta_key           = sanitize_text_field( $single_meta_data[0] );
							$meta_values        = explode( ':', $single_meta_data[1] );

							if( ! empty( $meta_key ) ){

								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}

								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}

										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {

										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}

										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}

							}
						}
					}

					foreach( $tax_data['create'] as $tax_key => $tax_values ){

						if( $remove_all ){
							wp_set_object_terms( $post_id, array(), $tax_key, $tax_append );
						} else {
							wp_set_object_terms( $post_id, $tax_values, $tax_key, $tax_append );
						}

					}

					#$post_data['tax_input'] = $tax_data;
				}

				//Map external post data
				$post_data['tax_input'] = $tax_input;

				$return_args['msg'] = WPWHPRO()->helpers->translate("Post successfully created", 'action-create-post-success' );

				$return_args['success'] = true;
				$return_args['data']['post_data'] = $post_data;
				$return_args['data']['post_id'] = $post_id;

			} else {

				if( is_wp_error( $post_id ) && $wp_error ){

					$return_args['data']['post_data'] = $post_data;
					$return_args['data']['post_id'] = $post_id;
					$return_args['msg'] = WPWHPRO()->helpers->translate("WP Error", 'action-create-post-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Error creating post.", 'action-create-post-success' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post_data, $post_id, $return_args );
			}

			return $return_args;
		}

    }

endif; // End if class_exists check.