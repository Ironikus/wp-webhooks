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