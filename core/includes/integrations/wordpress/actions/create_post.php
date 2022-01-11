<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_post' ) ) :

	/**
	 * Load the create_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_post {

		/*
	 * The core logic to handle the creation of a user
	 */
	public function get_details(){

		$translation_ident = 'action-create-post-content';

		$parameter = array(
			'post_author'		   => array(
				'short_description' => WPWHPRO()->helpers->translate( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", $translation_ident ),
			),
			'post_date'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', $translation_ident ) ),
			'post_date_gmt'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', $translation_ident ) ),
			'post_content'		  => array(
				'short_description' => WPWHPRO()->helpers->translate( '(string) The post content. Default empty.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", $translation_ident )
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
				'description' => WPWHPRO()->helpers->translate( "The post type determines to which group of posts your currently created post belongs. Please use the slug of the post type to assign it properly.", $translation_ident ),
			),
			'comment_status'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', $translation_ident ) ),
			'ping_status'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', $translation_ident ) ),
			'post_password'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The password to access the post. Default empty.', $translation_ident ) ),
			'post_name'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The post name. Default is the sanitized post title when creating a new post.', $translation_ident ) ),
			'to_ping'			   => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', $translation_ident ) ),
			'pinged'				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', $translation_ident ) ),
			'post_parent'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(int) Set this for the post it belongs to, if any. Default 0.', $translation_ident ) ),
			'menu_order'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(int) The order the post should be displayed in. Default 0.', $translation_ident ) ),
			'post_mime_type'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The mime type of the post. Default empty.', $translation_ident ) ),
			'guid'				  => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) Global Unique ID for referencing the post. Default empty.', $translation_ident ) ),
			'import_id'			 => array(
				'short_description' => WPWHPRO()->helpers->translate( '(integer) In case you want to give your post a specific post id, please define it here.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "This argument allows you to define a suggested post ID for your post. In case the ID is already taken, the post will be created using the default behavior by asigning automatically an ID. ", $translation_ident ),
			),
			'post_category'		 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of category IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', $translation_ident ) ),
			'tags_input'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', $translation_ident ) ),
			'tax_input'			 => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty.', $translation_ident ) ),
			'meta_input'		  	=> array( 'premium' => true, 'short_description' => WPWHPRO()->helpers->translate( '<strong>DEPRECATED! Please use manage_meta_data instead.</strong>', $translation_ident ) ),
			'manage_meta_data'  	=> array( 'premium' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Manage your post-related meta data.', $translation_ident ) ),
			'manage_acf_data'   	=> array( 'premium' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Allows you to manage fields that are especially related to Advanced Custom Fields.', $translation_ident ) ),
			'wp_error'			  => array(
				'short_description' => WPWHPRO()->helpers->translate( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", $translation_ident ),
			),
			'do_action'			 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
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
<?php echo WPWHPRO()->helpers->translate( "This argument is specifically designed to add/update or remove post meta to your created post.", $translation_ident ); ?>
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
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the create_post action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "Contains the data that is used to create the post and some additional data as the meta input.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the post id of the newly created post. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", $translation_ident ); ?>
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
				'msg' => 'Post successfully created',
				'data' => 
				array (
				  'post_id' => 7912,
				  'post_data' => 
				  array (
					'post_author' => 1,
					'post_date' => '2021-12-31 11:11:11',
					'post_content' => 'The content of the post, including all HTML',
					'post_title' => 'A demo title',
					'post_excerpt' => 'The short description of the post',
					'post_status' => 'publish',
					'post_type' => 'post',
					'meta_data' => false,
					'tax_input' => false,
				  ),
				  'permalink' => 'https://yourdomain.test/?p=7912',
				  'manage_meta_data' => 
				  array (
					'success' => true,
					'msg' => 'The meta data was successfully executed.',
					'data' => 
					array (
					  'update_post_meta' => 
					  array (
						0 => 
						array (
						  'meta_key' => 'first_custom_key',
						  'meta_value' => 'Some custom value',
						  'prev_value' => false,
						  'response' => 69679,
						),
						1 => 
						array (
						  'meta_key' => 'second_custom_key',
						  'meta_value' => 'The new value',
						  'prev_value' => 'The previous value',
						  'response' => 69680,
						),
					  ),
					),
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Create a post',
				'webhook_slug' => 'create_post',
				'tipps' => array(
					WPWHPRO()->helpers->translate( "In case you want to create a post for a custom post type, you can do that by using the <strong>post_type</strong> argument.", $translation_ident ),
					WPWHPRO()->helpers->translate( "By default, we create each post in a draft state. If you want to directly publish a post, use the <strong>post_status</strong> argument and set it to <strong>publish</strong>.", $translation_ident ),
					WPWHPRO()->helpers->translate( "In case you want to set a short description for your post, you can use the <strong>post_excerpt</strong> argument.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'create_post',
				'name'			  => WPWHPRO()->helpers->translate( 'Create post', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'create a post', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Insert/Create a post. You have all functionalities available from wp_insert_post', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		/**
		 * Create a post via an action call
		 *
		 * @param $update - Wether to create or to update the post
		 */
		public function execute( $return_data, $response_body ){

			$update = false;
			$post_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'post_helpers' );
			$return_args = array(
				'success'   => false,
				'msg'	   => '',
				'data'	  => array(
					'post_id' => null,
					'post_data' => null,
					'permalink' => '',
				)
			);

			$post_id				= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );

			$post_author			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_author' );
			$post_date			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date' ) );
			$post_date_gmt		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date_gmt' ) );
			$post_content		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content' );
			$post_content_filtered  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content_filtered' );
			$post_title			 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_title' );
			$post_excerpt		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_excerpt' );
			$post_status			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_status' ) );
			$post_type			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_type' ) );
			$comment_status		 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
			$ping_status			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
			$post_password		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_password' ) );
			$post_name			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_name' ) );
			$to_ping				= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
			$pinged				 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
			$post_modified		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified' ) );
			$post_modified_gmt	  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified_gmt' ) );
			$post_parent			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_parent' ) );
			$menu_order			 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
			$post_mime_type		 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_mime_type' ) );
			$guid				   = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
			$import_id			  = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'import_id' ) );
			$post_category		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_category' );
			$tags_input			 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
			$tax_input			  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
			$wp_error			   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )	 ? true : false;
			$create_if_none		 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'create_if_none' ) == 'yes' )	 ? true : false;
			$do_action			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

			if( $update && ! $create_if_none ){
				if ( empty( $post_id ) ) {
					$return_args['msg'] = WPWHPRO()->helpers->translate("The post id is required to update a post.", 'action-create-post-not-found' );

					return $return_args;
				}
			}

			$create_post_on_update = false;
			$post_data = array();

			if( $update ){
				$post = '';

				if( ! empty( $post_id ) ){
					$post = get_post( $post_id );
				}

				if( ! empty( $post ) ){
					if( ! empty( $post->ID ) ){
						$post_data['ID'] = $post->ID;
					}
				}

				if( empty( $post_data['ID'] ) ){

					$create_post_on_update = apply_filters( 'wpwhpro/run/create_action_post_on_update', $create_if_none );

					if( empty( $create_post_on_update ) ){
						$return_args['msg'] = WPWHPRO()->helpers->translate("Post not found.", 'action-create-post-not-found' );

						return $return_args;
					}

				}

			}

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

			if( ! empty( $import_id ) && ( ! $update || $create_post_on_update ) ){
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
			if( empty( $current_pist_type ) ){
				if( $update && ! empty( $post_data['ID'] ) ){
					$current_post_type = get_post_type( intval( $post_data['ID'] ) );
				}
			}

			if( $update && ! $create_post_on_update ){
				$post_id = wp_update_post( $post_data, $wp_error );
			} else {
				$post_id = wp_insert_post( $post_data, $wp_error );
			}

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

							$meta_key		   = sanitize_text_field( $taxkey );
							$meta_values		= $single_meta;

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
							$meta_key		   = sanitize_text_field( $single_meta_data[0] );
							$meta_values		= explode( ':', $single_meta_data[1] );

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

					if( $update && ! $create_post_on_update ){
						foreach( $tax_data['delete'] as $tax_key => $tax_values ){
							wp_remove_object_terms( $post_id, $tax_values, $tax_key );
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

				if( $update && ! $create_post_on_update ){
					$return_args['msg'] = WPWHPRO()->helpers->translate("Post successfully updated", 'action-create-post-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate("Post successfully created", 'action-create-post-success' );
				}

				$return_args['success'] = true;
				$return_args['data']['post_data'] = $post_data;
				$return_args['data']['post_id'] = $post_id;
				$return_args['data']['permalink'] = get_permalink( $post_id );

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