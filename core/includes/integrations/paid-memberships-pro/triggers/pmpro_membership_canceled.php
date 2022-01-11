<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_membership_canceled' ) ) :

 /**
  * Load the pmpro_membership_canceled trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_membership_canceled {

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
                'hook' => 'pmpro_after_change_membership_level',
                'callback' => array( $this, 'pmpro_after_change_membership_level_callback' ),
                'priority' => 20,
                'arguments' => 3,
                'delayed' => true,
            ),
        );
    }

    public function get_details(){

        $translation_ident = "action-pmpro_membership_canceled-description";
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
            'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user that the membership was canceled of.', $translation_ident ) ),
            'cancel_level' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the canceled level.', $translation_ident ) ),
            'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full WordPress user data.', $translation_ident ) ),
        );

        $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
            'webhook_name' => 'Membership canceled',
            'webhook_slug' => 'pmpro_membership_canceled',
            'post_delay' => true,
            'trigger_hooks' => array(
                array( 
                    'hook' => 'pmpro_after_change_membership_level',
                    'url' => 'https://www.paidmembershipspro.com/hook/pmpro_after_change_membership_level/',
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
            'trigger'           => 'pmpro_membership_canceled',
            'name'              => WPWHPRO()->helpers->translate( 'Membership canceled', $translation_ident ),
            'sentence'              => WPWHPRO()->helpers->translate( 'a membership was canceled', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a membership was canceled within Paid Memberships Pro.', $translation_ident ),
            'description'       => $description,
            'integration'       => 'paid-memberships-pro',
        );

    }

    /**
     * Triggers once a Paid Membership Pro membership was canceled
     *
     * @param int $level_id ID of the level changed to.
	 * @param int $user_id ID of the user changed to.
	 * @param int $cancel_level ID of the level being cancelled if specified.
     */
    public function pmpro_after_change_membership_level_callback( $level_id, $user_id, $cancel_level ){

        //Only continue if a cancelation is given
        if( ! is_numeric( $cancel_level ) ){
            return;
        }

        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pmpro_membership_canceled' );

        $payload = array(
            'user_id' => $user_id,
            'cancel_level' => $cancel_level,
            'user' => get_userdata( $user_id ),
        );

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                if( isset( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) && ! empty( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                    if( ! in_array( $cancel_level, $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
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

        do_action( 'wpwhpro/webhooks/trigger_pmpro_membership_canceled', $payload, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'user_id' => 122,
            'cancel_level' => 12,
            'user' => 
            array (
              'data' => 
              array (
                'ID' => '122',
                'user_login' => 'jon-doe',
                'user_pass' => '$P$BYHHFjHILOQENvBQKLWXXXXXXXX',
                'user_nicename' => 'jon-doe',
                'user_email' => 'jon@doe.test',
                'user_url' => '',
                'user_registered' => '2020-01-15 09:34:16',
                'user_activation_key' => '',
                'user_status' => '0',
                'display_name' => 'Jon Doe',
                'spam' => '0',
                'deleted' => '0',
              ),
              'ID' => 122,
              'caps' => 
              array (
                'author' => true,
              ),
              'cap_key' => 'wp_capabilities',
              'roles' => 
              array (
                0 => 'author',
              ),
              'allcaps' => 
              array (
                'upload_files' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'publish_posts' => true,
                'read' => true,
                'level_2' => true,
                'level_1' => true,
                'level_0' => true,
                'delete_posts' => true,
                'delete_published_posts' => true,
                'edit_blocks' => true,
                'publish_blocks' => true,
                'read_blocks' => true,
                'delete_blocks' => true,
                'delete_published_blocks' => true,
                'edit_published_blocks' => true,
                'create_blocks' => true,
                'edit_aggregator-records' => true,
                'edit_published_aggregator-records' => true,
                'delete_aggregator-records' => true,
                'delete_published_aggregator-records' => true,
                'publish_aggregator-records' => true,
                'groups_access' => true,
                'author' => true,
              ),
              'filter' => NULL,
            ),
        );

        return $data;
    }

  }

endif; // End if class_exists check.