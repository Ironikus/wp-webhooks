<?php

/**
 * Template for custom action trigger
 * 
 * Webhook type: trigger
 * Webhook name: custom_action
 * Template version: 1.0.0
 */

$translation_ident = "trigger-custom-action-description";

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on a custom action, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Custom Action</strong> (custom_action) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Custom Action</strong> (custom_action)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it for the visual setup. Now you need to add a custom WordPress action call within your code to trigger this trigger. Down below you will find more details.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>wp_webhooks_send_to_webhook</strong> hook, which isn't fired by default.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'wp_webhooks_send_to_webhook', array( $this, 'wp_webhooks_send_to_webhook_action' ), 10, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook trigger is fired immediately after the <code>do_action( 'wp_webhooks_send_to_webhook' )</code> function is called.", $translation_ident ); ?>
<br><br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to fire this trigger?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can fire the trigger wherever you want within your PHP code. The only necessity is, that our plugin is fully initialized by WordPress. Here is a code example:", $translation_ident ); ?>
<pre>$custom_data = array(
	'data_1' => 'value'
);
$webhook_names = array(
	'15792546059992909'
);

do_action( 'wp_webhooks_send_to_webhook', $custom_data, $webhook_names );</pre>
<?php echo WPWHPRO()->helpers->translate( "The <code>do_action()</code> function accepts three parameters, which are explained down below:", $translation_ident ); ?>
<ol>
    <li>
        <strong>'wp_webhooks_send_to_webhook'</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This is our trigger identifier so that our plugin knows when to fire the webhook. Please don't change this value.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$custom_data</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This variable contains an array of data you want to send over to each of the webhook URL's (payload). Depending on your <strong>Webhook URL</strong> specific settings, it will be sent in different formats (default JSON).", $translation_ident ); ?>
    </li>
    <li>
        <strong>$webhook_names</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This variable contains an array with <strong>Webhook URL</strong>s this trigger should fire on. To add a trigger, add the <strong>Webhook Name</strong> to the array. If you don't send over the this argument at all, all webhook URL's for this webhook trigger will be triggered.", $translation_ident ); ?>
    </li>
</ol>
<br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the demo webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>