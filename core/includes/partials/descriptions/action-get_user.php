<?php

/**
 * Template for fetching a single user
 * 
 * Webhook type: action
 * Webhook name: get_user
 * Template version: 1.0.0
 */

$translation_ident = "action-get-user-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fetch a single users from your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "It uses the WordPress function <strong>get_user_by()</strong> to fetch the user from the database. To learn more about this function, please check the official WordPress docs:", $translation_ident ); ?>
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/get_user_by/">https://developer.wordpress.org/reference/functions/get_user_by/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>get_user</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>get_user</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>get_user</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the argument <strong>user_value</strong>, which by default is the user id. You can also use other values like the email or the login name (But for doing so, please read the next step).", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you want to use e.g. the email instead of the user id, you need to set the argument <strong>value_type</strong> to <strong>email</strong>. Further details are down below within the <strong>Special Arguments</strong> description.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the fetching of the user.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>
<h5><?php echo WPWHPRO()->helpers->translate( "value_type", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument is used to change the data you can add within the <strong>user_value</strong> argument. Possible values are: <strong>id, ID, slug, email, login</strong>", $translation_ident ); ?>
<br>
<hr>
<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_user</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $user_value, $value_type, $user ){
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
        <strong>$user_value</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The value you included into the user_value argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$value_type</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The value you included into the value_type argument.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$user</strong> (mixed)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Returns null in case an the user_value wasn't set, the user object on success or a wp_error object in case an error occurs.", $translation_ident ); ?>
    </li>
</ol>