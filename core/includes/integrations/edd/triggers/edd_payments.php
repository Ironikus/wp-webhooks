<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_payments' ) ) :

 /**
  * Load the edd_payments trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_payments {

    public function is_active(){

        $is_active = true;

        //Backwards compatibility
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
                'hook' => 'edd_payment_delete',
                'callback' => array( $this, 'wpwh_trigger_edd_payments_delete_prepare' ),
                'priority' => 10,
                'arguments' => 1,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_update_payment_status',
                'callback' => array( $this, 'wpwh_trigger_edd_payments' ),
                'priority' => 10,
                'arguments' => 3,
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

        $translation_ident = "action-edd_payments-description";

        $choices = array();
        if( function_exists( 'edd_get_payment_statuses' ) ){
            $choices = edd_get_payment_statuses();

            //add our custom delete status
            $choices['wpwh_deleted'] = WPWHPRO()->helpers->translate( 'Deleted', $translation_ident );
        }

        $parameter = array(
            'ID' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The payment id.', $translation_ident ) ),
            'key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique payment key.', $translation_ident ) ),
            'subtotal' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The subtotal of the payment.', $translation_ident ) ),
            'tax' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The tax amount of the payment.', $translation_ident ) ),
            'fees' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Additional payment fees of the payment.', $translation_ident ) ),
            'total' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The total amount of the payment.', $translation_ident ) ),
            'gateway' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The chosen payment gateway of the payment.', $translation_ident ) ),
            'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The customer email that was used for the payment the payment.', $translation_ident ) ),
            'date' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date (in SQL format) of the payment creation.', $translation_ident ) ),
            'products' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of al products that are included within the payment. Please check the example below for further details.', $translation_ident ) ),
            'discount_codes' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma separated list of applied coupon codes.', $translation_ident ) ),
            'first_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer.', $translation_ident ) ),
            'last_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer.', $translation_ident ) ),
            'transaction_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The transaction id of the payment.', $translation_ident ) ),
            'billing_address' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The billing adress with all its values. Please check the example below for further details.', $translation_ident ) ),
            'shipping_address' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The shipping adress with all its values. Please check the example below for further details.', $translation_ident ) ),
            'metadata' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of all available meta fields.', $translation_ident ) ),
            'new_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The new status of the payment.', $translation_ident ) ),
            'old_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The prrevious status of the payment.', $translation_ident ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on creation of a payment or any other payment status change, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD Payments</strong> (edd_payments) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD Payments</strong> (edd_payments)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>edd_update_payment_status</strong> hook of Easy Digital Downloads", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_update_payment_status', array( $this, 'wpwh_trigger_edd_payments_init' ), 10, 3 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (user_register) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you only want to fire the webhook on specific payment statuses, you can select them within the single webhook URL settings. Simply select the payment statuses you want to fire the webhook on and all others are ignored.", $translation_ident ); ?></li>
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

        $settings = array(
            'data' => array(
                'wpwhpro_trigger_edd_payments_whitelist_status' => array(
                    'id'          => 'wpwhpro_trigger_edd_payments_whitelist_status',
                    'type'        => 'select',
                    'multiple'    => true,
                    'choices'      => $choices,
                    'label'       => WPWHPRO()->helpers->translate('Trigger on selected payment status changes', $translation_ident),
                    'placeholder' => '',
                    'required'    => false,
                    'description' => WPWHPRO()->helpers->translate('Select only the payment statuses you want to fire the trigger on. You can choose multiple ones. If none is selected, all are triggered.', $translation_ident)
                ),
            )
        );

        return array(
            'trigger'           => 'edd_payments',
            'name'              => WPWHPRO()->helpers->translate( 'Payments', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires on certain status changes of payments within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_payments',
            'integration'       => 'edd',
        );

    }

    public function wpwh_trigger_edd_payments_delete_prepare( $payment_id = 0 ){

        if( ! isset( $this->pre_trigger_values['edd_payments'] ) ){
            $this->pre_trigger_values['edd_payments'] = array();
        }

        if( ! isset( $this->pre_trigger_values['edd_payments'][ $payment_id ] ) ){
            $this->pre_trigger_values['edd_payments'][ $payment_id ] = array();
        }

        $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
        $this->pre_trigger_values['edd_payments'][ $payment_id ] = $edd_helpers->wpwh_get_edd_order_data( $payment_id );
        
        //Init the post delay functions with further default parameters
        $this->wpwh_trigger_edd_payments_init( $payment_id, 'wpwh_deleted', 'wpwh_undeleted' );
        
    }

    /*
    * Register the edd payments post delay trigger logic
    */
    public function wpwh_trigger_edd_payments_init(){
        WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'wpwh_trigger_edd_payments' ), func_get_args() );
    }

    /**
     * Triggers once a new EDD payment was changed
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_payments( $payment_id, $new_status, $old_status ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_payments' );
        $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
        $order_data = array();

        //Only fire on change
        if( $new_status === $old_status ){
            return;
        }

        foreach( $webhooks as $webhook ){

            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                foreach( $webhook['settings'] as $settings_name => $settings_data ){

                    if( $settings_name === 'wpwhpro_trigger_edd_payments_whitelist_status' && ! empty( $settings_data ) ){
                        if( ! in_array( $new_status, $settings_data ) ){
                            $is_valid = false;
                        }
                    }

                }
            }

            if( $is_valid ) {

                if( isset( $this->pre_trigger_values['edd_payments'][ $payment_id ] ) ){
                    $order_data = $this->pre_trigger_values['edd_payments'][ $payment_id ];
                } else {
                    $order_data = $edd_helpers->wpwh_get_edd_order_data( $payment_id );
                }

                //append status changes
                $order_data['new_status'] = $new_status;
                $order_data['old_status'] = $old_status;

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $order_data );
                } else {
                    $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $order_data );
                }

                do_action( 'wpwhpro/webhooks/trigger_edd_payments', $payment_id, $new_status, $old_status, $response_data_array );
            }
            
        }
    }

    public function get_demo( $options = array() ) {

        $data = array (
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
          );

        return $data;
    }

  }

endif; // End if class_exists check.