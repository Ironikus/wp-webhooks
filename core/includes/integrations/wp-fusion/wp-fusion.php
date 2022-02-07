<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_fusion Class
 *
 * This class integrates all WP Fusion related features and endpoints
 *
 * @since 4.3.4
 */
class WP_Webhooks_Integrations_wp_fusion {

    public function is_active(){
        return function_exists( 'wp_fusion' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'WP Fusion',
            'icon' => $integration_url . '/assets/img/icon-wp-fusion.svg',
        );
    }

}
