<?php

/**
 * Template for fetching a single post
 * 
 * Webhook type: action
 * Webhook name: get_post
 * Template version: 1.0.0
 */

$translation_ident = "action-get-post-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fetch a single post from your WordPress system via a webhook call. It uses the default WordPress function get_post():", $translation_ident ); ?>
<br>
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/get_post/">https://developer.wordpress.org/reference/functions/get_post/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>get_post</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>get_post</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>get_post</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>post_id</strong>, which contains the id of the post you want to fetch.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the fetching of the post.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "return_only", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "You can also manipulate the result of the post data gathering using the <strong>return_only</strong> parameter. This allows you to output only certain elements of the request. Here is an example:", $translation_ident ); ?>
<pre>post,post_thumbnail,post_terms,post_meta,post_permalink</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li>all</li>
    <li>post</li>
    <li>post_thumbnail</li>
    <li>post_terms</li>
    <li>post_meta</li>
    <li>post_permalink</li>
</ol>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "thumbnail_size", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to return one or multiple thumbnail_sizes for the given post thumbnail. By default, we output only the full image. Here is an example: ", $translation_ident ); ?>
<pre>full,medium</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's a list of all available sizes for the <strong>thumbnail_size</strong> argument (The availalbe sizes may vary since you can also use third-party size definitions). In case you want to use multiple ones, simply separate them with a comma.", $translation_ident ); ?>
<ol>
    <li><strong>thumbnail</strong> <?php echo WPWHPRO()->helpers->translate( "(150px square)", $translation_ident ); ?></li>
    <li><strong>medium</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 300px width and height)", $translation_ident ); ?></li>
    <li><strong>large</strong> <?php echo WPWHPRO()->helpers->translate( "(maximum 1024px width and height)", $translation_ident ); ?></li>
    <li><strong>full</strong> <?php echo WPWHPRO()->helpers->translate( "(full/original image size you uploaded)", $translation_ident ); ?></li>
</ol>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "post_taxonomies", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "You can also customize the output of the returned taxonomies using the <strong>post_taxonomies</strong> argument. Default is post_tag. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", $translation_ident ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
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
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The id of the currently fetched post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$thumbnail_size</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted thumbnail sizes sent by the caller within the thumbnail_size argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_taxonomies</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The string formatted taxonomy slugs sent by the caller within the post_taxonomies argument.", $translation_ident ); ?>
    </li>
</ol>