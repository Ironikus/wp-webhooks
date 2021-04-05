<?php

/**
 * Template for deleting a user
 * 
 * Webhook type: action
 * Webhook name: delete_user
 * Template version: 1.0.0
 */

$translation_ident = "action-delete-user-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a user on your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>delete_user</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>delete_user</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>delete_user</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set either the email or the user id of the user you want to delete. You can do that by using the <strong>user_id</strong> or <strong>user_email</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the deletion of the user.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Please note that deleting a user inside of a multisite network without setting the <strong>remove_from_network</strong> argument, just deletes the user from the current site, but not from the whole network.", $translation_ident ); ?></li>
</ol>