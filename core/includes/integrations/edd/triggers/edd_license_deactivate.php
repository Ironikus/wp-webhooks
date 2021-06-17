<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_license_deactivate' ) ) :

 /**
  * Load the edd_license_deactivate trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_license_deactivate {

    public function is_active(){

        $is_active = class_exists( 'EDD_Software_Licensing' );

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
                'hook' => 'edd_sl_deactivate_license',
                'callback' => array( $this, 'wpwh_trigger_edd_license_deactivate' ),
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

        $translation_ident = "action-edd_license_deactivate-description";

        $parameter = array(
            'ID' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The license id.', $translation_ident ) ),
            'key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The license key.', $translation_ident ) ),
            'customer_email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer.', $translation_ident ) ),
            'customer_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full customer name.', $translation_ident ) ),
            'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The id of the product.', $translation_ident ) ),
            'product_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full product name.', $translation_ident ) ),
            'activation_limit' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The activation limit.', $translation_ident ) ),
            'activation_count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of total activations.', $translation_ident ) ),
            'activated_urls' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A list of activated URLs.', $translation_ident ) ),
            'expiration' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The expiration date in SQL format.', $translation_ident ) ),
            'is_lifetime' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number 1 or 0 if it is a lifetime.', $translation_ident ) ),
            'status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The current license status.', $translation_ident ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data once a license is deactivated, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD License Deactivation</strong> (edd_license_deactivate) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD License Deactivation</strong> (edd_license_deactivate)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>edd_sl_deactivate_license</strong> hook of Easy Digital Downloads", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_sl_deactivate_license', array( $this, 'wpwh_trigger_edd_license_deactivate_init' ), 10, 2 );</pre>
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
            'trigger'           => 'edd_license_deactivate',
            'name'              => WPWHPRO()->helpers->translate( 'License deactivated', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires on deactivation of a license within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_license_deactivate',
            'integration'       => 'edd',
        );

    }

    /**
     * Triggers once a new EDD payment was changed
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_license_deactivate( $license_id = 0, $download_id = 0 ){
        $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_license_deactivate' );
        $response_data_array = array();

        foreach( $webhooks as $webhook ){
            $license_data = $edd_helpers->edd_get_license_data( $license_id, $download_id );

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

            if( $webhook_url_name !== null ){
                $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
            } else {
                $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
            }

            do_action( 'wpwhpro/webhooks/trigger_edd_license_deactivate', $license_id, $download_id, $license_data, $response_data_array );
        }
    }

    public function get_demo( $options = array() ) {

        $data = array(
            'ID'               => 1234,
            'key'              => '736b31fec1ecb01c28b51a577bb9c2b3',
            'customer_name'    => 'Jane Doe',
            'customer_email'   => 'jane@test.com',
            'product_id'       => 4321,
            'product_name'     => 'Sample Product',
            'activation_limit' => 1,
            'activation_count' => 1,
            'activated_urls'   => 'sample.com',
            'expiration'       => date( 'Y-n-d H:i:s', current_time( 'timestamp' ) ),
            'is_lifetime'      => 0,
            'status'           => 'inactive',
        );

      return $data;
    }

  }

endif; // End if class_exists check.