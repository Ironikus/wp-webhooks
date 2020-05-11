<?php

/**
 * Template for creating a post
 * 
 * Webhook type: action
 * Webhook name: create_post
 * Template version: 1.0.0
 */

$translation_ident = "action-create-post-description";

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
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "post_author", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "post_content", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "post_status", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The post status defines further details about how your post will be treated. By default, WordPress offers the following post statuses: <strong>draft, pending, private, publish</strong>. Please note that other plugins can extend the post status values to offer a bigger variety, e.g. Woocommerce.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "post_type", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The post type determines to which group of posts your currently created post belongs. Please use the slug of the post type to assign it properly.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "tags_input", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument supports the default tags_input variable of the <strong>wp_insert_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example:", $translation_ident ); ?>
<pre>342,5678,2</pre>
<?php echo WPWHPRO()->helpers->translate( "This argument supports a comma separated list of tag names, slugs, or IDs.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "tax_input", $translation_ident ); ?></h5>
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
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "meta_input", $translation_ident ); ?></h5>
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
<strong><?php echo WPWHPRO()->helpers->translate( "Advanced", $translation_ident ); ?></strong>: <?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array serialization for single post meta values. This means, you can turn JSON into a serialized array.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "As an example: The following JSON <strong>{\"price\": \"100\"}</strong> will turn into <strong>a:1:{s:5:\"price\";s:3:\"100\";}</strong>", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "To make it work, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <strong>ironikus-serialize</strong>. Here's a full example:", $translation_ident ); ?>
<pre>{
  "meta_key_1": "This is my meta value 1",
  "another_meta_key": "This is my second meta key!",
  "third_meta_key": "ironikus-serialize{\"price\": \"100\"}"
}</pre>
<?php echo WPWHPRO()->helpers->translate( "This example will create three post meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <strong>a:1:{s:5:\"price\";s:3:\"100\";}</strong>. The string <strong>ironikus-serialize</strong> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "wp_error", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
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
        <strong>$meta_input</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the unformatted post meta as you sent it over within the webhook request as a string.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
    </li>
</ol>