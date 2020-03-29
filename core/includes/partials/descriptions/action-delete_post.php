<?php

/**
 * Template for deleting a post
 * 
 * Webhook type: action
 * Webhook name: delete_post
 * Template version: 1.0.0
 */

$translation_ident = "action-delete-post-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a post on your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>delete_post</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>delete_post</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>delete_post</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the post id of the post you want to delete. You can do that by using the <strong>post_id</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the deletion of the post.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Please note that deleting a post without defining the <strong>force_delete</strong> argument, only moves default posts and pages to the trash (wherever applicable) - otherwise they will be directly deleted.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "force_delete", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the post will be completely removed from your WordPress website.", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_post</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post, $post_id, $check, $force_delete ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$post</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the WordPress post object of the already deleted post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$post_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the post id of the deleted post.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$check</strong> (mixed)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the response of the wp_delete_post() function.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$force_delete</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns either yes or no, depending on your settings for the force_delete argument.", $translation_ident ); ?>
    </li>
</ol>