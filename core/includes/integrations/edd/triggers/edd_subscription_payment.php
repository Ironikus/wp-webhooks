<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_subscription_payment' ) ) :

 /**
  * Load the edd_subscription_payment trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_subscription_payment {

    public function is_active(){

        $is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

        //Backwards compatibility for the "Easy Digital Downloads" integration
        if( defined( 'WPWH_EDD_NAME' ) ){
            $is_active = false;
        }

        return $is_active;
    }

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
    public function get_callbacks(){

        return array(
            array(
                'type' => 'action',
                'hook' => 'edd_recurring_add_subscription_payment',
                'callback' => array( $this, 'wpwh_trigger_edd_subscription_payment_init' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => true,
            ),
        );
    }

    /*
    * Register the post delete trigger as an element
    *
    * @since 1.2
    */
    public function get_details(){

        $translation_ident = "action-edd_subscription_payment-description";

        $parameter = array(
            'payment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The payment data. For further details, please refer to the example down below.', 'trigger-edd_subscription_payment-content' ) ),
            'subscription' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The subscription data. For further details, please refer to the example down below.', 'trigger-edd_subscription_payment-content' ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data once a new subscription payment is is made, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD New Subscription Payment</strong> (edd_subscription_payment) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD New Subscription Payment</strong> (edd_subscription_payment)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your recieving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For a better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you are able to recieve data on the URL once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>edd_recurring_add_subscription_payment</strong> hook of Easy Digital Downloads", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_recurring_add_subscription_payment', array( $this, 'wpwh_trigger_edd_subscription_payment_init' ), 10, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (user_register) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
        $description = ob_get_clean();

        $settings = array();

        return array(
            'trigger'           => 'edd_subscription_payment',
            'name'              => WPWHPRO()->helpers->translate( 'New subscription payment', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a new subscription payment is created within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_subscription_payment',
            'integration'       => 'edd',
        );

    }

    /**
     * Triggers once a new EDD customer was created
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_subscription_payment( EDD_Payment $payment, EDD_Subscription $subscription ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_subscription_payment' );

        $response_data_array = array();
        $data = array( 
            'payment' => $payment,
            'subscription' => $subscription
        );

        foreach( $webhooks as $webhook ){
            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

            if( $webhook_url_name !== null ){
                $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
            } else {
                $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
            }
        }

        do_action( 'wpwhpro/webhooks/trigger_edd_subscription_payment', $data, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array(
            'payment' => array(
                'ID' => 123,
                'key' => 'c36bc5d3315cde89ce18a19bb6a1d559',
                'subtotal' => 39,
                'tax' => '0',
                'fees' => 
                array (
                ),
                'total' => 39,
                'gateway' => 'manual',
                'email' => 'johndoe123@test.com',
                'date' => '2020-04-23 09:16:00',
                'products' => 
                array (
                  array (
                    'Product' => 'Demo Download',
                    'Subtotal' => 39,
                    'Tax' => '0.00',
                    'Discount' => 0,
                    'Price' => 39,
                    'PriceName' => 'Single Site',
                    'Quantity' => 1,
                  ),
                ),
                'discount_codes' => 'none',
                'first_name' => 'Jon',
                'last_name' => 'Doe',
                'transaction_id' => 123,
                'billing_address' => array( 'line1' => 'Street 1', 'line2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
                'shipping_address' => array( 'address' => 'Street 1', 'Address2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
                'metadata' => 
                array (
                  '_edd_payment_tax_rate' => 
                  array (
                    0 => '0',
                  ),
                  '_edd_complete_actions_run' => 
                  array (
                    0 => '8763342154',
                  ),
                ),
                'new_status' => 'publish',
                'old_status' => 'pending',
            ),
            'subscription' => array(
                'id'                => '183',
                'customer_id'       => '36',
                'period'            => 'month',
                'initial_amount'    => '16.47',
                'recurring_amount'  => '10.98',
                'bill_times'        => '0',
                'transaction_id'    => '',
                'parent_payment_id' => '845',
                'product_id'        => '8',
                'created'           => '2016-06-13 13:47:24',
                'expiration'        => '2016-07-13 23:59:59',
                'status'            => 'pending',
                'profile_id'        => 'ppe-4e3ca7d1c017e0ea8b24ff72d1d23022-8',
                'gateway'           => 'paypalexpress',
                'customer'          => array(
                    'id'             => '36',
                    'purchase_count' => '2',
                    'purchase_value' => '32.93',
                    'email'          => 'jane@test.com',
                    'emails'         => array(
                        'jane@test.com',
                    ),
                    'name'           => 'Jane Doe',
                    'date_created'   => '2016-06-13 13:19:50',
                    'payment_ids'    => '842,845,846',
                    'user_id'        => '1',
                    'notes'          => array(
                          'These are notes about the customer',
                    ),
                ),
                'user_id' => '24',
            )
        );

        return $data;
    }

  }

endif; // End if class_exists check.