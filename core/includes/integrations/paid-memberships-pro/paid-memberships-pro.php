<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_paid_memberships_pro Class
 *
 * This class integrates all Paid Memberships Pro related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_paid_memberships_pro {

    public function is_active(){
        return defined( 'PMPRO_BASE_FILE' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Paid Memberships Pro',
            'icon' => $integration_url . '/assets/img/icon-paid-memberships-pro.png',
        );
    }

}
