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