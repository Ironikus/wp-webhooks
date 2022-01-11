<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_order_created' ) ) :

 /**
  * Load the pmpro_order_created trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_order_created {

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
                'hook' => 'pmpro_added_order',
                'callback' => array( $this, 'pmpro_added_order_callback' ),
                'priority' => 20,
                'arguments' => 1,
                'delayed' => true,
            ),
        );
    }

    public function get_details(){

        $translation_ident = "action-pmpro_order_created-description";
        $validated_levels = array();
        $pmpro_helpers = WPWHPRO()->integrations->get_helper( 'paid-memberships-pro', 'pmpro_helpers' );
        if( method_exists( $pmpro_helpers, 'get_membership_levels' ) ){
            $membership_levels = $pmpro_helpers->get_membership_levels( true );
            if( ! empty( $membership_levels ) && is_array( $membership_levels ) ){
                foreach( $membership_levels as $level_id => $single_level ){
                    $validated_levels[ $level_id ] = isset( $single_level->name ) ? sanitize_text_field( $single_level->name ) : WPWHPRO()->helpers->translate( 'undefined', $translation_ident );
                }
            }
        }

        $parameter = array(
            'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user that the new order was created for.', $translation_ident ) ),
            'membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the order membership level.', $translation_ident ) ),
            'order' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full order data of the current order.', $translation_ident ) ),
        );

        $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
            'webhook_name' => 'Order created',
            'webhook_slug' => 'pmpro_order_created',
            'post_delay' => true,
            'trigger_hooks' => array(
                array( 
                    'hook' => 'pmpro_added_order',
                    'url' => 'https://www.paidmembershipspro.com/hook/pmpro_added_order/',
                ),
            )
        ) );

        $settings = array(
            'load_default_settings' => true,
            'data' => array(
                'wpwhpro_pmpro_trigger_on_membership_level' => array(
                    'id'		  => 'wpwhpro_pmpro_trigger_on_membership_level',
                    'type'		=> 'select',
                    'multiple'	=> true,
                    'choices'	  => $validated_levels,
                    'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected membership level', $translation_ident ),
                    'placeholder' => '',
                    'required'	=> false,
                    'description' => WPWHPRO()->helpers->translate( 'Select only the membership levels you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
                ),
            )
        );

        return array(
            'trigger'           => 'pmpro_order_created',
            'name'              => WPWHPRO()->helpers->translate( 'Order created', $translation_ident ),
            'sentence'              => WPWHPRO()->helpers->translate( 'an order was created', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a order was created within Paid Memberships Pro.', $translation_ident ),
            'description'       => $description,
            'integration'       => 'paid-memberships-pro',
        );

    }

    /**
     * Triggers once a new Paid Membership Pro membership was purchased
     *
	 * @param object|MemberOrder $order The data about the current purchase
     */
    public function pmpro_added_order_callback( MemberOrder $order ){

        //try to fetch the order again for better values
        if( isset( $order->id ) && ! empty( $order->id ) ){
            $norder = new MemberOrder( $order->id );
            if( ! empty( $norder ) ){
                $order = $norder;
            }
        }

        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pmpro_order_created' );
		$membership_level = $order->getMembershipLevel();
        $membership_id = ( is_object( $membership_level ) && isset( $membership_level->id ) ) ? $membership_level->id : 0;
        $user = $order->getUser();
		$user_id = $user->ID;

        $payload = array(
            'user_id' => $user_id,
            'membership_id' => $membership_id,
            'order' => $order,
        );

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                if( isset( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) && ! empty( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                    if( ! in_array( $membership_id, $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
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

        do_action( 'wpwhpro/webhooks/trigger_pmpro_order_created', $payload, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'user_id' => '1',
            'membership_id' => '1',
            'order' => 
            array (
              'gateway' => '',
              'Gateway' => 
              array (
                'gateway' => '',
              ),
              'billing' => 
              array (
                'name' => '',
                'street' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => '',
                'phone' => '',
              ),
              'code' => 'E46CF607B6',
              'user_id' => '1',
              'membership_id' => '1',
              'subtotal' => '0',
              'tax' => '',
              'total' => '0',
              'payment_type' => '',
              'cardtype' => '',
              'accountnumber' => '',
              'expirationmonth' => '',
              'expirationyear' => '',
              'status' => 'success',
              'gateway_environment' => 'sandbox',
              'payment_transaction_id' => '',
              'subscription_transaction_id' => '',
              'notes' => '',
              'timestamp' => 1626956520,
              'certificate_id' => '0',
              'certificateamount' => '',
              'paypal_token' => '',
              'couponamount' => '',
              'affiliate_id' => '',
              'affiliate_subid' => '',
              'session_id' => '',
              'ExpirationDate' => '',
              'datetime' => '2021-07-22 12:22:00',
              'checkout_id' => '1',
              'sqlQuery' => 'INSERT INTO wp_pmpro_membership_orders
                                          (`code`, `session_id`, `user_id`, `membership_id`, `paypal_token`, `billing_name`, `billing_street`, `billing_city`, `billing_state`, `billing_zip`, `billing_country`, `billing_phone`, `subtotal`, `tax`, `couponamount`, `certificate_id`, `certificateamount`, `total`, `payment_type`, `cardtype`, `accountnumber`, `expirationmonth`, `expirationyear`, `status`, `gateway`, `gateway_environment`, `payment_transaction_id`, `subscription_transaction_id`, `timestamp`, `affiliate_id`, `affiliate_subid`, `notes`, `checkout_id`)
                                          VALUES(\'E46CF607B6\',
                                                 \'\',
                                                 1,
                                                 1,
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'0\',
                                                 \'\',
                                                 \'\',
                                                 0,
                                                 \'\',
                                                 \'0\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'\',
                                                 \'success\',
                                                 \'\',
                                                 \'sandbox\',
                                                 \'\',
                                                 \'\',
                                                 \'2021-07-22 12:22:00\',
                                                 \'\',
                                                 \'\',
                                                  \'\',
                                                  1
                                                 )',
              'id' => '2',
              'FirstName' => 'Jon',
              'LastName' => 'Doe',
              'Address1' => '',
              'Email' => 'jon@doe.test',
              'ExpirationDate_YdashM' => '-',
              'discount_code' => NULL,
              'membership_level' => 
              array (
                'level_id' => '1',
                'name' => 'First Level',
                'description' => 'This is a demo level',
                'allow_signups' => '1',
                'expiration_number' => '0',
                'expiration_period' => '',
                'id' => '1',
                'user_id' => '1',
                'membership_id' => '1',
                'code_id' => '0',
                'initial_payment' => 0,
                'billing_amount' => 0,
                'cycle_number' => '0',
                'cycle_period' => '',
                'billing_limit' => '0',
                'trial_amount' => 0,
                'trial_limit' => '0',
                'status' => 'active',
                'startdate' => '1626948898',
                'enddate' => NULL,
                'modified' => '2021-07-22 12:14:58',
              ),
              'user' => 
              array (
                'ID' => '1',
                'user_login' => 'jondoe',
                'user_pass' => '$P$B4B1t8fCUMz4XXXXXXXXEbzY1',
                'user_nicename' => 'jondoe',
                'user_email' => 'jon@doe.test',
                'user_url' => '',
                'user_registered' => 1501199891,
                'user_activation_key' => '',
                'user_status' => '0',
                'display_name' => 'jondoe',
                'spam' => '0',
                'deleted' => '0',
              ),
            ),
        );

        return $data;
    }

  }

endif; // End if class_exists check.