<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_update_customer' ) ) :

 /**
  * Load the edd_update_customer trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_update_customer {

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
                'hook' => 'edd_customer_post_update',
                'callback' => array( $this, 'wpwh_trigger_edd_update_customer' ),
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

        $translation_ident = "action-edd_update_customer-description";

        $parameter = array(
            'first_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer.', 'trigger-edd_update_customer-content' ) ),
            'last_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer.', 'trigger-edd_update_customer-content' ) ),
            'id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique id of the customer. (This is not the user id)', 'trigger-edd_update_customer-content' ) ),
            'purchase_count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of purchases of the customer.', 'trigger-edd_update_customer-content' ) ),
            'purchase_value' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The value of all purchases of the customer.', 'trigger-edd_update_customer-content' ) ),
            'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The main email of the customer.', 'trigger-edd_update_customer-content' ) ),
            'emails' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Additional emails of the customer.', 'trigger-edd_update_customer-content' ) ),
            'name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full name of the customer.', 'trigger-edd_update_customer-content' ) ),
            'date_created' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date and time of the user creation in SQL format.', 'trigger-edd_update_customer-content' ) ),
            'payment_ids' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comme-separated list of payment ids.', 'trigger-edd_update_customer-content' ) ),
            'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The user id of the customer.', 'trigger-edd_update_customer-content' ) ),
            'notes' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Additional ntes given by the customer.', 'trigger-edd_update_customer-content' ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on update of a customer, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD Customer Update</strong> (edd_update_customer) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD Customer Update</strong> (edd_update_customer)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>edd_customer_post_update</strong> hook of Easy Digital Downloads", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_customer_post_update', array( $this, 'wpwh_trigger_edd_update_customer' ), 10, 3 );</pre>
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
            'trigger'           => 'edd_update_customer',
            'name'              => WPWHPRO()->helpers->translate( 'Customer updated', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a customer is updated within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_update_customer',
            'integration'       => 'edd',
        );

    }

    /**
     * Triggers once a new EDD customer was created
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_update_customer( $updated = false, $customer_id = 0, $args = array() ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_update_customer' );

        if( ! function_exists( 'EDD_Customer' ) || ! $updated ){
            return;
        }
        
        $customer = new EDD_Customer( $customer_id );

        //Properly calculate names as given by the Zapier extension
        $first_name = '';
        $last_name = '';
        if( isset( $customer->name ) ){
            $separated_names = explode( ' ', $customer->name );

            $first_name = ( ! empty( $separated_names[0] ) ) ? $separated_names[0] : '';

            if( ! empty( $separated_names[1] ) ) {
                unset( $separated_names[0] );
                $last_name = implode( ' ', $separated_names );
            }
        }
        $customer->first_name = $first_name;
        $customer->last_name  = $last_name;

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

            if( $webhook_url_name !== null ){
                $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
            } else {
                $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
            }

        }

        do_action( 'wpwhpro/webhooks/trigger_edd_update_customer', $customer_id, $customer, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array(
            'user_id'        => 1234,
            'name'           => 'John Doe',
            'first_name'     => 'John',
            'last_name'      => 'Doe',
            'email'          => 'johndoe123@test.com',
            'payment_ids'    => 2345,
            'purchase_value' => '23.5',
            'date_created'   => date( 'Y-m-d h:i:s' ),
            'purchase_count' => 1,
            'notes'          => null,
        );

        return $data;
    }

  }

endif; // End if class_exists check.