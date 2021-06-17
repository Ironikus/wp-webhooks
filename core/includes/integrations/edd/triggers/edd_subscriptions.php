<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_subscriptions' ) ) :

 /**
  * Load the edd_subscriptions trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_subscriptions {

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
                'hook' => 'edd_subscription_post_create',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_create' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_subscription_post_renew',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_renew' ),
                'priority' => 10,
                'arguments' => 3,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_subscription_completed',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_completed' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_subscription_expired',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_expired' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_subscription_failing',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_failing' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => false,
            ),
            array(
                'type' => 'action',
                'hook' => 'edd_subscription_cancelled',
                'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_cancelled' ),
                'priority' => 10,
                'arguments' => 2,
                'delayed' => false,
            ),
        );
    }

    /*
    * Register the post delete trigger as an element
    *
    * @since 1.2
    */
    public function get_details(){

        $translation_ident = "action-edd_subscriptions-description";
        $choices = apply_filters( 'wpwhpro/settings/edd_subscription_statuses', array(
            'create' => WPWHPRO()->helpers->translate( 'Created', 'trigger-edd-subscriptions-content' ),
            'renew' => WPWHPRO()->helpers->translate( 'Renewed', 'trigger-edd-subscriptions-content' ),
            'completed' => WPWHPRO()->helpers->translate( 'Completed', 'trigger-edd-subscriptions-content' ),
            'expired' => WPWHPRO()->helpers->translate( 'Expired', 'trigger-edd-subscriptions-content' ),
            'failing' => WPWHPRO()->helpers->translate( 'Failed', 'trigger-edd-subscriptions-content' ),
            'cancelled' => WPWHPRO()->helpers->translate( 'Cancelled', 'trigger-edd-subscriptions-content' ),
        ) );

        $parameter = array(
            'id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The subscription id.', 'trigger-edd_subscriptions-content' ) ),
            'customer_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The id of the related customer.', 'trigger-edd_subscriptions-content' ) ),
            'period' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The subcription period.', 'trigger-edd_subscriptions-content' ) ),
            'initial_amount' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The initial price amount.', 'trigger-edd_subscriptions-content' ) ),
            'initial_tax_rate' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The initial tax rate.', 'trigger-edd_subscriptions-content' ) ),
            'initial_tax' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The initial tax amount.', 'trigger-edd_subscriptions-content' ) ),
            'recurring_amount' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The recurring price amount.', 'trigger-edd_subscriptions-content' ) ),
            'recurring_tax_rate' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The recurring tax rate.', 'trigger-edd_subscriptions-content' ) ),
            'recurring_tax' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The recurring tax amount.', 'trigger-edd_subscriptions-content' ) ),
            'bill_times' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The times the customer gets billed.', 'trigger-edd_subscriptions-content' ) ),
            'transaction_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The transaction id.', 'trigger-edd_subscriptions-content' ) ),
            'parent_payment_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The parent payment id in case the payment is recurring.', 'trigger-edd_subscriptions-content' ) ),
            'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The related product id for this subscription.', 'trigger-edd_subscriptions-content' ) ),
            'price_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The price id in case it is a variation.', 'trigger-edd_subscriptions-content' ) ),
            'created' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date and time of creation (in SQL format).', 'trigger-edd_subscriptions-content' ) ),
            'expiration' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date and time of expiration (in SQL format).', 'trigger-edd_subscriptions-content' ) ),
            'trial_period' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The trial period.', 'trigger-edd_subscriptions-content' ) ),
            'status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The current subscription status.', 'trigger-edd_subscriptions-content' ) ),
            'profile_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique profile id.', 'trigger-edd_subscriptions-content' ) ),
            'gateway' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The chosen gateway for this subscription.', 'trigger-edd_subscriptions-content' ) ),
            'customer' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array with all of the customer information. Please see the example down below for further details.', 'trigger-edd_subscriptions-content' ) ),
            'notes' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array with all the subscription notes.', 'trigger-edd_subscriptions-content' ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on any subscription status change, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD Subscriptions</strong> (edd_subscriptions) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD Subscriptions</strong> (edd_subscriptions)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the following hooks of Easy Digital Downloads", $translation_ident ); ?>
<ul>
    <li>edd_subscription_post_create</li>
    <li>edd_subscription_post_renew</li>
    <li>edd_subscription_completed</li>
    <li>edd_subscription_expired</li>
    <li>edd_subscription_failing</li>
    <li>edd_subscription_cancelled</li>
</ul>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here are the calls within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_subscription_post_create', array( $this, 'wpwh_trigger_edd_subscriptions_map_create' ), 10, 2 );
add_action( 'edd_subscription_post_renew', array( $this, 'wpwh_trigger_edd_subscriptions_map_renew' ), 10, 3 );
add_action( 'edd_subscription_completed', array( $this, 'wpwh_trigger_edd_subscriptions_map_completed' ), 10, 2 );
add_action( 'edd_subscription_expired', array( $this, 'wpwh_trigger_edd_subscriptions_map_expired' ), 10, 2 );
add_action( 'edd_subscription_failing', array( $this, 'wpwh_trigger_edd_subscriptions_map_failing' ), 10, 2 );
add_action( 'edd_subscription_cancelled', array( $this, 'wpwh_trigger_edd_subscriptions_map_cancelled' ), 10, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (user_register) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you only want to fire the webhook on specific subscription statuses, you can select them within the single webhook URL settings. Simply select the subscription statuses you want to fire the webhook on and all others are ignored.", $translation_ident ); ?></li>
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
                'wpwhpro_trigger_edd_subscriptions_whitelist_status' => array(
                    'id'          => 'wpwhpro_trigger_edd_subscriptions_whitelist_status',
                    'type'        => 'select',
                    'multiple'    => true,
                    'choices'      => $choices,
                    'label'       => WPWHPRO()->helpers->translate('Trigger on selected subscription status changes', 'trigger-edd_subscriptions-content'),
                    'placeholder' => '',
                    'required'    => false,
                    'description' => WPWHPRO()->helpers->translate('Select only the subscription statuses you want to fire the trigger on. You can choose multiple ones. If none is selected, all are triggered.', 'trigger-edd_subscriptions-content')
                ),
            )
        );

        return array(
            'trigger'           => 'edd_subscriptions',
            'name'              => WPWHPRO()->helpers->translate( 'Subscriptions', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires on certain status changes of subscriptions within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_subscriptions',
            'integration'       => 'edd',
        );

    }

    public function wpwh_trigger_edd_subscriptions_map_create( $subscription_id = 0, $args = array() ) {

        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $subscription = new EDD_Subscription( $subscription_id );

        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'create' );
    }

    public function wpwh_trigger_edd_subscriptions_map_renew( $sub_id = 0, $expiration = '', EDD_Subscription $subscription ) {
        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'renew' );
    }

    public function wpwh_trigger_edd_subscriptions_map_completed( $sub_id = 0, EDD_Subscription $subscription ) {
        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'completed' );
    }

    public function wpwh_trigger_edd_subscriptions_map_expired( $sub_id = 0, EDD_Subscription $subscription ) {
        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'expired' );
    }

    public function wpwh_trigger_edd_subscriptions_map_failing( $sub_id = 0, EDD_Subscription $subscription ) {
        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'failing' );
    }

    public function wpwh_trigger_edd_subscriptions_map_cancelled( $sub_id = 0, EDD_Subscription $subscription ) {
        if( ! class_exists( 'EDD_Subscription' ) ) {
            return;
        }
        $this->wpwh_trigger_edd_subscriptions_init( $subscription, 'cancelled' );
    }

    /*
    * Register the edd payments post delay trigger logic
    */
    public function wpwh_trigger_edd_subscriptions_init(){
        WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'wpwh_trigger_edd_subscriptions' ), func_get_args() );
    }

    /**
     * Triggers once a new EDD payment was changed
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_subscriptions( $subscription, $status ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_subscriptions' );
        $response_data_array = array();

        

        foreach( $webhooks as $webhook ){

            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                foreach( $webhook['settings'] as $settings_name => $settings_data ){

                    if( $settings_name === 'wpwhpro_trigger_edd_subscriptions_whitelist_status' && ! empty( $settings_data ) ){
                        if( ! in_array( $status, $settings_data ) ){
                            $is_valid = false;
                        }
                    }

                }
            }

            if( $is_valid ) {

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $subscription );
                } else {
                    $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $subscription );
                }

                do_action( 'wpwhpro/webhooks/trigger_edd_subscriptions', $subscription, $status, $response_data_array );
            }
            
        }
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'id' => '1',
            'customer_id' => '1',
            'period' => 'year',
            'initial_amount' => '9.97',
            'initial_tax_rate' => '',
            'initial_tax' => '',
            'recurring_amount' => '9.97',
            'recurring_tax_rate' => '',
            'recurring_tax' => '',
            'bill_times' => '2',
            'transaction_id' => '',
            'parent_payment_id' => '706',
            'product_id' => '285',
            'price_id' => '0',
            'created' => '2020-04-23 16:29:36',
            'expiration' => '2020-04-22 23:59:59',
            'trial_period' => '',
            'status' => 'completed',
            'profile_id' => 'xxxxxxxx',
            'gateway' => 'manual',
            'customer' => 
            array (
              'id' => '1',
              'purchase_count' => 2,
              'purchase_value' => 87.97,
              'email' => 'johndoe123@test.com',
              'emails' => 
              array (
                0 => 'johndoe123more@test.com',
              ),
              'name' => 'John Doe',
              'date_created' => '2019-02-26 07:32:56',
              'payment_ids' => '695,706',
              'user_id' => '1',
            ),
            'notes' => 
            array (
              'April 23, 2020 16:32:05 - Status changed from completed to failing by admin',
              'April 23, 2020 16:30:59 - Status changed from active to completed by admin',
              'April 23, 2020 16:30:45 - Status changed from expired to active by admin',
            ),
        );

        return $data;
    }

  }

endif; // End if class_exists check.