<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_givewp Class
 *
 * This class integrates all GiveWP related features and endpoints
 *
 * @since 4.3.4
 */
class WP_Webhooks_Integrations_givewp {

    public function is_active(){
        return class_exists( 'Give' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'GiveWP',
            'icon' => $integration_url . '/assets/img/icon-givewp.svg',
        );
    }

}
