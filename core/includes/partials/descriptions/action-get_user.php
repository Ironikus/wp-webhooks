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