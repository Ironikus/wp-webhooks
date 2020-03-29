<?php

/**
 * Template for fetching one or multiple posts
 * 
 * Webhook type: action
 * Webhook name: get_posts
 * Template version: 1.0.0
 */

$translation_ident = "action-get-posts-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fetch one or multiple posts from your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>get_posts</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>get_posts</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>get_posts</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>arguments</strong>, which contains a JSON formatted string with the parameters used to identify the posts. More details about that is available within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the fetching of the posts.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "arguments", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument contains a JSON formatted string, which includes certain arguments from the WordPress post query called <strong>WP_Query</strong>. For further details, please check out the following link:", $translation_ident ); ?>
<br>
<a href="https://developer.wordpress.org/reference/classes/wp_query/" title="wordpress.org" target="_blank">https://developer.wordpress.org/reference/classes/wp_query/</a>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is an example on how the JSON is set up:", $translation_ident ); ?>
<pre>{"post_type":"post","posts_per_page":8}</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above will filter the posts for the post type \"post\" and returns maximum eight posts.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "return_only", $translation_ident ); ?></h5>
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
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
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