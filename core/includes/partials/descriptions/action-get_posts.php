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