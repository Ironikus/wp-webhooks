<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_update_post' ) ) :

	/**
	 * Load the create_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_update_post {

		/*
	 * The core logic to handle the creation of a user
	 */
	public function get_details(){

		$translation_ident = 'action-update-post-content';

		$parameter = array(
			'post_id'			   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(int) The post id itself. This field is mandatory', $translation_ident ) ),
			'post_author'		   => array(
				'short_description' => WPWHPRO()->helpers->translate( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", $translation_ident ),
			),
			'post_date'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post. Default is the current time.', $translation_ident ) ),
			'post_date_gmt'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', $translation_ident ) ),
			'post_content'		  => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post content. Default empty.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", $translation_ident ),
			),
			'post_content_filtered' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The filtered post content. Default empty.', $translation_ident ) ),
			'post_title'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post title. Default empty.', $translation_ident ) ),
			'post_excerpt'		  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post excerpt. Default empty.', $translation_ident ) ),
			'post_status'		   => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post status. Default \'draft\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post status defines further details about how your post will be treated. By default, WordPress offers the following post statuses: <strong>draft, pending, private, publish</strong>. Please note that other plugins can extend the post status values to offer a bigger variety, e.g. Woocommerce.", $translation_ident ),
			),
			'post_type'			 => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post type. Default \'post\'.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post type determines to which group of posts your currently updated post belongs. Please use the slug of the post type.", $translation_ident ),
			),
			'comment_status'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', $translation_ident ) ),
			'ping_status'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', $translation_ident ) ),
			'post_password'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The password to access the post. Default empty.', $translation_ident ) ),
			'post_name'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', $translation_ident ) ),
			'to_ping'			   => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', $translation_ident ) ),
			'pinged'				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', $translation_ident ) ),
			'post_modified'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date when the post was last modified. Default is the current time.', $translation_ident ) ),
			'post_modified_gmt'	 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date when the post was last modified in the GMT timezone. Default is the current time.', $translation_ident ) ),
			'post_parent'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', $translation_ident ) ),
			'menu_order'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', $translation_ident ) ),
			'post_mime_type'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The mime type of the post. Default empty.', $translation_ident ) ),
			'guid'				  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', $translation_ident ) ),
			'post_category'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of category IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3. Please note that WordPress just accepts categories of the type "category" here.', $translation_ident ) ),
			'tags_input'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty. Please note that WordPress just accepts tags of the type "post_tag" here.', $translation_ident ) ),
			'tax_input'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty.', $translation_ident ) ),
			'meta_input'		  	=> array( 'short_description' => WPWHPRO()->helpers->translate( '<strong>DEPRECATED! Please use manage_meta_data instead.</strong>', $translation_ident ) ),
			'manage_meta_data'  	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Manage your post-related meta data.', $translation_ident ) ),
			'manage_acf_data'   	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Allows you to manage fields that are especially related to Advanced Custom Fields.', $translation_ident ) ),
			'wp_error'			  => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", $translation_ident ),
			),
			'create_if_none'		=> array(
				'short_description' => WPWHPRO()->helpers->translate( 'Wether you want to create the post if it does not exists or not. Set it to "yes" or "no" Default is "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>create_if_none</strong> argument to <strong>yes</strong>, a post will be created with the given details in case it does not exist.", $translation_ident )
			),
			'do_action'			 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) ),
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument supports the default tags_input variable of the <strong>wp_update_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", $translation_ident ); ?>
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
		<strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong><br>
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
	<strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong><br>
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
<?php echo WPWHPRO()->helpers->translate( "This argument is specifically designed to add/update or remove post meta to your updated post.", $translation_ident ); ?>
					<br>
					<?php echo WPWHPRO()->helpers->translate( "To create/update or delete custom meta values, we offer you two different ways:", $translation_ident ); ?>
					<ol>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "String method", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update or delete the post meta using a simple string. To make it work, separate the meta key from the value using a comma (,). To separate multiple meta settings from each other, simply separate them with a semicolon (;). To remove a meta value, simply set as a value <strong>ironikus-delete</strong>", $translation_ident ); ?>
							<pre>meta_key_1,meta_value_1;my_second_key,ironikus-delete</pre>
							<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT:</strong> Please note that if you want to use values that contain commas or semicolons, the string method does not work. In this case, please use the JSON method.", $translation_ident ); ?>
						</li>
						<li>
						<strong><?php echo WPWHPRO()->helpers->translate( "JSON method", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to add/update or remove the post meta using a JSON formatted string. To make it work, add the meta key as the key and the meta value as the value. To delete a meta value, simply set the value to <strong>ironikus-delete</strong>. Here's an example on how this looks like:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!"
"third_meta_key": "ironikus-delete"
}</pre>
						</li>
					</ol>
					<strong><?php echo WPWHPRO()->helpers->translate( "Advanced", $translation_ident ); ?></strong>: <?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array/object serialization for single post meta values. This means, you can turn JSON into a serialized array or object.", $translation_ident ); ?>
					<br>
					<?php echo WPWHPRO()->helpers->translate( "As an example: The following JSON <code>{\"price\": \"100\"}</code> will turn into <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code> with default serialization or into <code>a:1:{s:5:\"price\";s:3:\"100\";}</code> with array serialization.", $translation_ident ); ?>
					<ol>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "Object serialization", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to serialize a JSON to an object using the default json_decode() function of PHP.", $translation_ident ); ?>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "To serialize your JSON to an object, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <code>ironikus-serialize</code>. Here's a full example:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize{\"price\": \"100\"}"
}</pre>
							<?php echo WPWHPRO()->helpers->translate( "This example will create three post meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", $translation_ident ); ?>
						</li>
						<li>
							<strong><?php echo WPWHPRO()->helpers->translate( "Array serialization", $translation_ident ); ?></strong>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "This method allows you to serialize a JSON to an array using the json_decode( \$json, true ) function of PHP.", $translation_ident ); ?>
							<br>
							<?php echo WPWHPRO()->helpers->translate( "To serialize your JSON to an array, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <code>ironikus-serialize-array</code>. Here's a full example:", $translation_ident ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize-array{\"price\": \"100\"}"
}</pre>
							<?php echo WPWHPRO()->helpers->translate( "This example will create three post meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>a:1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize-array</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", $translation_ident ); ?>
						</li>
					</ol>
		<?php
		$parameter['meta_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument integrates the full features of managing post related meta values.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note</strong>: This argument is very powerful and requires some good understanding of JSON. It is integrated with the commonly used functions for managing post meta within WordPress. You can find a list of all avaialble functions here: ", $translation_ident ); ?>
<ul>
	<li><strong>add_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_post_meta/">https://developer.wordpress.org/reference/functions/add_post_meta/</a></li>
	<li><strong>update_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_post_meta/">https://developer.wordpress.org/reference/functions/update_post_meta/</a></li>
	<li><strong>delete_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_post_meta/">https://developer.wordpress.org/reference/functions/delete_post_meta/</a></li>
</ul>
<br>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a complete JSON example that shows you how to use each of the functions above.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array/object serialization for single post meta values. This means, you can turn JSON into a serialized array or object.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a JSON construct as an input. This construct contains each available function as a top-level key within the first layer and the assigned data respectively as a value. If you want to learn more about each line, please take a closer look at the bottom of the example.", $translation_ident ); ?>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a list that explains each of the top level keys.", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "add_post_meta", $translation_ident ); ?></strong>
		<pre>{
   "add_post_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": { "some_array_key": "Some array Value" },
		"unique": true
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>add_post_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_post_meta/">https://developer.wordpress.org/reference/functions/add_post_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "In the example above, you will find two entries within the add_post_meta key. The first one shows the default behavior using only the meta key and the value. This causes the meta key to be created without checking upfront if it exists - that allows you to create the meta value multiple times.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "As seen in the second entry, you will find a third key called <strong>unique</strong> that allows you to check upfront if the meta key exists already. If it does, the meta entry is neither created, nor updated. Set the value to <strong>true</strong> to check against existing ones. Default: false", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "If you look closely to the second entry again, the value included is not a string, but a JSON construct, which is considered as an array and will therefore be serialized. The given value will be saved to the database in the following format: <code>a:1:{s:14:\"some_array_key\";s:16:\"Some array Value\";}</code>", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "update_post_meta", $translation_ident ); ?></strong>
		<pre>{
   "update_post_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "The new value",
		"prev_value": "The previous value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>update_post_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_post_meta/">https://developer.wordpress.org/reference/functions/update_post_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "The example above shows you two entries for this function. The first one is the default set up thats used in most cases. Simply define the meta key and the meta value and the key will be updated if it does exist and if it does not exist, it will be created.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The third argument, as seen in the second entry, allows you to check against a previous value before updating. That causes that the meta value will only be updated if the previous key fits to whats currently saved within the database. Default: ''", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "delete_post_meta", $translation_ident ); ?></strong>
		<pre>{
   "delete_post_meta":[
	  {
		"meta_key": "first_custom_key"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "Target specific value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>delete_post_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_post_meta/">https://developer.wordpress.org/reference/functions/delete_post_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "Within the example above, you will see that only the meta key is required for deleting an entry. This will cause all meta keys on this post with the same key to be deleted.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The second argument allows you to target only a specific meta key/value combination. This gets important if you want to target a specific meta key/value combination and not delete all available entries for the given post. Default: ''", $translation_ident ); ?>
	</li>
</ol>
<strong><?php echo WPWHPRO()->helpers->translate( "Some tipps:", $translation_ident ); ?></strong>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "You can include the value for this argument as a simple string to your webhook payload or you integrate it directly as JSON into your JSON payload (if you send a raw JSON response).", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Changing the order of the functions within the JSON causes the post meta to behave differently. If you, for example, add the <strong>delete_post_meta</strong> key before the <strong>update_post_meta</strong> key, the meta values will first be deleted and then added/updated.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "The webhook response contains a validted array that shows each initialized meta entry, as well as the response from its original WordPress function. This way you can see if the meta value was adjusted accordingly.", $translation_ident ); ?></li>
</ol>
		<?php
		$parameter['manage_meta_data']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the update_post action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post_data, $post_id, $meta_input, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$post_data</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to update the post and some additional data as the meta input.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the post id of the newly updated post. Please note that it can also contain a wp_error object since it is the response of the wp_update_user() function.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$meta_input</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the unformatted post meta as you sent it over within the webhook request as a string.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		//Remove if ACF isn't active
		if( ! WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			unset( $parameter['manage_acf_data'] );
		} else {
			ob_start();
			WPWHPRO()->acf->load_acf_description( $translation_ident );
			$parameter['manage_acf_data']['description'] = ob_get_clean();
		}

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Post successfully updated',
				'data' => 
				array (
				  'post_id' => 1339,
				  'post_data' => 
				  array (
					'ID' => 1339,
					'post_content' => 'Some new post content.',
					'post_title' => 'The new post title',
					'post_status' => 'publish',
					'meta_data' => false,
					'tax_input' => false,
				  ),
				  'permalink' => 'https://yourdomain.test/blog/2021/08/28/post-name/',
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Update a post',
				'webhook_slug' => 'update_post',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'Another argument you need to define is the <strong>post_id</strong>. It contains the ID of the post within your WordPress site.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'To update a post, you only need to set the values you want to update. The undefined settings won\'t be overwritten.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'In case you want to create the post if it does not exists at that point, you can set the <strong>create_if_none</strong> argument to <strong>yes</strong>', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'update_post',
				'name'			  => WPWHPRO()->helpers->translate( 'Update post', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'update a post', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Update a post. You have all functionalities available from wp_update_post', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

	}

endif; // End if class_exists check.