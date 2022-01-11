<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_simple_pay_Triggers_wpsimplepay_purchase' ) ) :

 /**
  * Load the wpsimplepay_purchase trigger
  *
  * @since 4.2.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_simple_pay_Triggers_wpsimplepay_purchase {

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
                'hook' => 'simpay_charge_created',
                'callback' => array( $this, 'wpwh_trigger_wpsimplepay_purchase' ),
                'priority' => 20,
                'arguments' => 2,
                'delayed' => true,
            ),
        );
    }

    public function get_details(){

        $translation_ident = "action-wpsimplepay_purchase-description";
        $validated_forms = array();
        if( class_exists( 'WP_Query' ) ){
            $simplepayforms = new WP_Query( array(
                'post_type'      => 'simple-pay',
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC',
                'posts_per_page' => -1,
            ) );
            if( ! empty( $simplepayforms ) && ! is_wp_error( $simplepayforms ) && isset( $simplepayforms->posts ) ){
                foreach( $simplepayforms->posts as $single_form ){
                    $validated_forms[ $single_form->ID ] = wp_kses( $single_form->post_title, array() );
                }
            }
        }

        $parameter = array(
            'charge' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All required payment details.', 'trigger-wpsimplepay_purchase-content' ) ),
            'metadata' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the form such as the form id within WordPress.', 'trigger-wpsimplepay_purchase-content' ) ),
        );

        $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
            'webhook_name' => 'Purchase completed',
            'webhook_slug' => 'wpsimplepay_purchase',
            'post_delay' => true,
            'trigger_hooks' => array(
                array( 
                    'hook' => 'simpay_charge_created',
                ),
            )
        ) );

        $settings = array(
            'load_default_settings' => true,
            'data' => array(
                'wpwhpro_wpsimplepay_trigger_on_forms' => array(
                    'id'		  => 'wpwhpro_wpsimplepay_trigger_on_forms',
                    'type'		=> 'select',
                    'multiple'	=> true,
                    'choices'	  => $validated_forms,
                    'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
                    'placeholder' => '',
                    'required'	=> false,
                    'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
                ),
            )
        );

        return array(
            'trigger'           => 'wpsimplepay_purchase',
            'name'              => WPWHPRO()->helpers->translate( 'Purchase completed', $translation_ident ),
            'sentence'              => WPWHPRO()->helpers->translate( 'a purchase was completed', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a new payment was made within WP Simple Pay.', $translation_ident ),
            'description'       => $description,
            'integration'       => 'wp-simple-pay',
        );

    }

    /**
     * Triggers once a new EDD customer was created
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_wpsimplepay_purchase( $charge, $metadata ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpsimplepay_purchase' );

        $form_id = 0;
        if( is_array( $metadata ) && isset( $metadata['simpay_form_id'] ) ){
            $form_id = intval( $metadata['simpay_form_id'] );
        }

        $payload = array(
            'charge' => $charge,
            'metadata' => $metadata,
        );

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                if( isset( $webhook['settings']['wpwhpro_wpsimplepay_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_wpsimplepay_trigger_on_forms'] ) ){
                    if( ! in_array( $form_id, $webhook['settings']['wpwhpro_wpsimplepay_trigger_on_forms'] ) ){
                        $is_valid = false;
                    }
                }
            }

            if( $is_valid ){
                if( $webhook_url_name !== null ){
                    $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                } else {
                    $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                }
            }

        }

        do_action( 'wpwhpro/webhooks/trigger_wpsimplepay_purchase', $payload, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'charge' => 
            array (
              'id' => 'ch_xxxxxxxxxxxxxxxxxxxxxxxx',
              'object' => 'charge',
              'amount' => 100,
              'amount_captured' => 100,
              'amount_refunded' => 0,
              'application' => 'ca_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
              'application_fee' => NULL,
              'application_fee_amount' => NULL,
              'balance_transaction' => 'txn_xxxxxxxxxxxxxxxxxxxxxxxx',
              'billing_details' => 
              array (
                'address' => 
                array (
                  'city' => NULL,
                  'country' => 'US',
                  'line1' => NULL,
                  'line2' => NULL,
                  'postal_code' => NULL,
                  'state' => NULL,
                ),
                'email' => 'jon@doe.test',
                'name' => 'Jon Doe',
                'phone' => NULL,
              ),
              'calculated_statement_descriptor' => 'DEMO LLC',
              'captured' => true,
              'created' => 1625466095,
              'currency' => 'usd',
              'customer' => 'cus_xxxxxxxxxxxxxx',
              'description' => NULL,
              'destination' => NULL,
              'dispute' => NULL,
              'disputed' => false,
              'failure_code' => NULL,
              'failure_message' => NULL,
              'fraud_details' => 
              array (
              ),
              'invoice' => NULL,
              'livemode' => false,
              'metadata' => 
              array (
                'simpay_form_id' => '73',
              ),
              'on_behalf_of' => NULL,
              'order' => NULL,
              'outcome' => 
              array (
                'network_status' => 'approved_by_network',
                'reason' => NULL,
                'risk_level' => 'normal',
                'risk_score' => 2,
                'seller_message' => 'Payment complete.',
                'type' => 'authorized',
              ),
              'paid' => true,
              'payment_intent' => 'pi_xxxxxxxxxxxxxxxxxxxxxxxx',
              'payment_method' => 'pm_xxxxxxxxxxxxxxxxxxxxxxxx',
              'payment_method_details' => 
              array (
                'card' => 
                array (
                  'brand' => 'visa',
                  'checks' => 
                  array (
                    'address_line1_check' => NULL,
                    'address_postal_code_check' => NULL,
                    'cvc_check' => 'pass',
                  ),
                  'country' => 'US',
                  'exp_month' => 2,
                  'exp_year' => 2025,
                  'fingerprint' => 'xxxxxxxxxxxxxxxx',
                  'funding' => 'credit',
                  'installments' => NULL,
                  'last4' => '4242',
                  'network' => 'visa',
                  'three_d_secure' => NULL,
                  'wallet' => NULL,
                ),
                'type' => 'card',
              ),
              'receipt_email' => NULL,
              'receipt_number' => NULL,
              'receipt_url' => 'https://pay.stripe.com/receipts/acct_xxxxxxxxxxxxxxxx/ch_xxxxxxxxxxxxxxxxxxxxxxxx/rcpt_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
              'refunded' => false,
              'refunds' => 
              array (
                'object' => 'list',
                'data' => 
                array (
                ),
                'has_more' => false,
                'total_count' => 0,
                'url' => '/v1/charges/ch_1J9lBTEOQk5ommW6LVL9E52B/refunds',
                'url' => '/v1/charges/ch_xxxxxxxxxxxxxxxxxxxxxxxx/refunds',
              ),
              'review' => NULL,
              'shipping' => NULL,
              'source' => NULL,
              'source_transfer' => NULL,
              'statement_descriptor' => NULL,
              'statement_descriptor_suffix' => NULL,
              'status' => 'succeeded',
              'transfer_data' => NULL,
              'transfer_group' => NULL,
            ),
            'metadata' => 
            array (
              'simpay_form_id' => '73',
            ),
        );

        return $data;
    }

  }

endif; // End if class_exists check.