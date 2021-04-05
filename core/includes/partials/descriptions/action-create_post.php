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