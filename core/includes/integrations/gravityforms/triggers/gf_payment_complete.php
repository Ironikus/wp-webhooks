<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_gravityforms_Triggers_gf_payment_complete' ) ) :

	/**
	 * Load the gf_payment_complete trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_gravityforms_Triggers_gf_payment_complete {

		/*
		* Register the post delete trigger as an element
		*
		* @since 1.2
		*/
		public function get_details(){

			$translation_ident = "trigger-gf_payment_complete-description";

			$parameter = array(
				'entry_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the currently present payment form submission.', $translation_ident ) ),
				'entry' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full entry data of the currently present form submission.', $translation_ident ) ),
				'action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The action data, containing further details about the payment.', $translation_ident ) ),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on a completed payment form submission of a \"Gravity Form\" form, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Gravity Form Completed Payment</strong> (gf_payment_complete) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Gravity Form Completed Payment</strong> (gf_payment_complete)", $translation_ident ); ?></h4>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you can receive data on the URL once the trigger fires.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>gform_post_payment_completed</strong> hook of Gravity Forms:", $translation_ident ); ?> 
<a title="docs.gravityforms.com" target="_blank" href="https://docs.gravityforms.com/gform_post_payment_completed/">https://docs.gravityforms.com/gform_post_payment_completed/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'gform_post_payment_completed', array( $this, 'wpwh_gform_post_payment_completed' ), 20, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (gf_payment_complete) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>
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
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
			$description = ob_get_clean();

			$settings = array();

			return array(
				'trigger'		   => 'gf_payment_complete',
				'name'			  => WPWHPRO()->helpers->translate( 'Payment completed', $translation_ident ),
				'parameter'		 => $parameter,
				'settings'		  => $settings,
				'returns_code'	  => $this->get_demo( array() ),
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a Gravity Form Payment is completed.', $translation_ident ),
				'description'	   => $description,
				'callback'		  => 'test_gf_payment_complete',
				'integration'	   => 'gravityforms',
				'premium' => true,
			);

		}

		/*
		* Register the demo post delete trigger callback
		*
		* @since 1.2
		*/
		public function get_demo( $options = array() ) {

			$data = array (
				'entry_id' => '1',
				'entry' => 
				array (
				  'id' => '1',
				  'status' => 'active',
				  'form_id' => '1',
				  'ip' => '94.206.15.238',
				  'source_url' => 'https://your-domain.com/your-custom-path',
				  'currency' => 'USD',
				  'post_id' => NULL,
				  'date_created' => '2021-05-30 13:32:34',
				  'date_updated' => '2021-05-30 13:32:34',
				  'is_starred' => 0,
				  'is_read' => 0,
				  'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
				  'payment_status' => 'Paid',
				  'payment_date' => '2021-05-30 13:32:36',
				  'payment_amount' => 20,
				  'payment_method' => 'visa',
				  'transaction_id' => 'pi_1IwokmEOQk4ommW6eYx7F222',
				  'is_fulfilled' => '1',
				  'created_by' => '1',
				  'transaction_type' => '1',
				  '2.1' => 'Product Name',
				  '2.2' => '$10.00',
				  '2.3' => '2',
				  '1.1' => 'XXXXXXXXXXXX5556',
				  '1.4' => 'Visa',
				),
				'action' => 
				array (
				  'is_success' => true,
				  'transaction_id' => 'pi_1IwokmEOQk4ommW6eYx7F222',
				  'amount' => 20,
				  'payment_method' => 'visa',
				  'payment_status' => 'Paid',
				  'payment_date' => '2021-05-30 13:32:36',
				  'type' => 'complete_payment',
				  'transaction_type' => 'payment',
				  'amount_formatted' => '$20.00',
				  'note' => 'Payment has been completed. Amount: $20.00. Transaction Id: pi_1IwokmEOQk4ommW6eYx7F222.',
				),
			  );

			return $data;
		}

	}

endif; // End if class_exists check.